<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section as FormSection;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use UnitEnum;

class BulkAttendance extends Page
{
    use CanUseDatabaseTransactions;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|UnitEnum|null $navigationGroup = 'الحضور والدرجات';

    protected static ?string $navigationLabel = 'تسجيل الحضور اليومي';

    protected static ?string $title = 'تسجيل الحضور اليومي';

    protected static ?string $slug = 'attendance/bulk';

    protected static ?int $navigationSort = 1;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'date' => today()->toDateString(),
            'rows' => [],
        ]);
    }

    /** @var array<string, string> */
    protected array $statusOptions = [
        'present' => 'حاضر',
        'absent' => 'غائب',
        'late' => 'متأخر',
        'excused' => 'بعذر',
    ];

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormSection::make('اختيار الفصل والمادة')
                    ->columns(3)
                    ->schema([
                        Select::make('section_id')
                            ->label('الفصل')
                            ->options(fn () => Section::query()->with('grade')->get()
                                ->mapWithKeys(fn (Section $s) => [$s->id => trim(($s->grade?->name ?? '') . ' - ' . $s->name)]))
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get) => $this->loadStudents($set, $get)),
                        Select::make('subject_id')
                            ->label('المادة')
                            ->options(fn () => Subject::query()->pluck('name', 'id'))
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get) => $this->loadStudents($set, $get)),
                        \Filament\Forms\Components\DatePicker::make('date')
                            ->label('التاريخ')
                            ->native(false)
                            ->default(today())
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get) => $this->loadStudents($set, $get)),
                    ]),

                \Filament\Forms\Components\Placeholder::make('summary')
                    ->label('الملخّص')
                    ->content(function (Get $get): string {
                        $rows = $get('rows') ?? [];
                        if (empty($rows)) {
                            return 'اختر الفصل والمادة لعرض الطلاب.';
                        }
                        $counts = ['present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0];
                        foreach ($rows as $row) {
                            $s = $row['status'] ?? 'present';
                            $counts[$s] = ($counts[$s] ?? 0) + 1;
                        }

                        return "حاضر: {$counts['present']} • غائب: {$counts['absent']} • متأخر: {$counts['late']} • بعذر: {$counts['excused']}";
                    }),

                Repeater::make('rows')
                    ->label('الطلاب')
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
                    ->columns(3)
                    ->schema([
                        Hidden::make('student_id'),
                        TextInput::make('student_name')
                            ->label('الطالب')
                            ->disabled()
                            ->dehydrated(false),
                        ToggleButtons::make('status')
                            ->label('الحالة')
                            ->inline()
                            ->live()
                            ->default('present')
                            ->options($this->statusOptions)
                            ->colors([
                                'present' => 'success',
                                'absent' => 'danger',
                                'late' => 'warning',
                                'excused' => 'info',
                            ]),
                        TextInput::make('note')->label('ملاحظة'),
                    ]),
            ]);
    }

    public function loadStudents(Set $set, Get $get): void
    {
        $sectionId = $get('section_id');
        $subjectId = $get('subject_id');
        $date = $get('date');

        if (! $sectionId) {
            $set('rows', []);

            return;
        }

        // الحضور المسجّل مسبقًا لنفس المادة والتاريخ
        $existing = collect();
        if ($subjectId && $date) {
            $existing = Attendance::query()
                ->where('section_id', $sectionId)
                ->where('subject_id', $subjectId)
                ->whereDate('date', $date)
                ->get()
                ->keyBy('student_id');
        }

        $rows = Student::query()
            ->where('section_id', $sectionId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get()
            ->map(fn (Student $s): array => [
                'student_id' => $s->id,
                'student_name' => $s->full_name,
                'status' => $existing[$s->id]->status ?? 'present',
                'note' => $existing[$s->id]->note ?? null,
            ])
            ->all();

        $set('rows', $rows);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        if (empty($data['section_id']) || empty($data['subject_id']) || empty($data['date']) || empty($data['rows'])) {
            Notification::make()->title('يرجى اختيار الفصل والمادة والتاريخ أولًا')->warning()->send();

            return;
        }

        $section = Section::find($data['section_id']);
        $academicYearId = $section?->academic_year_id ?? AcademicYear::current()->value('id');

        $this->beginDatabaseTransaction();

        foreach ($data['rows'] as $row) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $row['student_id'],
                    'subject_id' => $data['subject_id'],
                    'date' => $data['date'],
                ],
                [
                    'section_id' => $data['section_id'],
                    'academic_year_id' => $academicYearId,
                    'status' => $row['status'] ?? 'present',
                    'note' => $row['note'] ?? null,
                    'recorded_by' => auth()->id(),
                ],
            );
        }

        $this->commitDatabaseTransaction();

        Notification::make()
            ->title('تم حفظ الحضور بنجاح')
            ->body('عدد الطلاب: ' . count($data['rows']))
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('حفظ الحضور')
                ->icon('heroicon-o-check')
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('bulk-attendance-form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make($this->getFormActions())
                            ->alignment(Alignment::Start)
                            ->key('form-actions'),
                    ]),
            ]);
    }
}
