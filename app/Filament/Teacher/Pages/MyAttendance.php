<?php

namespace App\Filament\Teacher\Pages;

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

class MyAttendance extends Page
{
    use CanUseDatabaseTransactions;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'تسجيل الحضور';

    protected static ?string $title = 'تسجيل الحضور (اليوم)';

    protected static ?string $slug = 'attendance';

    protected static ?int $navigationSort = 1;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(['rows' => []]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        $teacher = auth()->user()?->teacher;
        $sectionIds = $teacher ? $teacher->assignedSectionIds() : [];
        $subjectIds = $teacher ? $teacher->assignedSubjectIds() : [];

        return $schema
            ->components([
                FormSection::make('اختيار الفصل والمادة — التاريخ: ' . today()->format('Y-m-d'))
                    ->columns(2)
                    ->schema([
                        Select::make('section_id')
                            ->label('الفصل')
                            ->options(fn () => Section::query()->whereIn('id', $sectionIds ?: [0])->with('grade')->get()
                                ->mapWithKeys(fn (Section $s) => [$s->id => trim(($s->grade?->name ?? '') . ' - ' . $s->name)]))
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get) => $this->loadStudents($set, $get)),
                        Select::make('subject_id')
                            ->label('المادة')
                            ->options(fn () => Subject::query()->whereIn('id', $subjectIds ?: [0])->pluck('name', 'id'))
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get) => $this->loadStudents($set, $get)),
                    ]),

                Repeater::make('rows')
                    ->label('الطلاب')
                    ->addable(false)->deletable(false)->reorderable(false)
                    ->columns(3)
                    ->schema([
                        Hidden::make('student_id'),
                        TextInput::make('student_name')->label('الطالب')->disabled()->dehydrated(false),
                        ToggleButtons::make('status')->label('الحالة')->inline()->live()->default('present')
                            ->options(['present' => 'حاضر', 'absent' => 'غائب', 'late' => 'متأخر', 'excused' => 'بعذر'])
                            ->colors(['present' => 'success', 'absent' => 'danger', 'late' => 'warning', 'excused' => 'info']),
                        TextInput::make('note')->label('ملاحظة'),
                    ]),
            ]);
    }

    public function loadStudents(Set $set, Get $get): void
    {
        $sectionId = $get('section_id');
        $subjectId = $get('subject_id');

        if (! $sectionId) {
            $set('rows', []);

            return;
        }

        $existing = collect();
        if ($subjectId) {
            $existing = Attendance::query()
                ->where('section_id', $sectionId)
                ->where('subject_id', $subjectId)
                ->whereDate('date', today())
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
        $teacher = auth()->user()?->teacher;

        if (! $teacher || empty($data['section_id']) || empty($data['subject_id']) || empty($data['rows'])) {
            Notification::make()->title('يرجى اختيار الفصل والمادة أولًا')->warning()->send();

            return;
        }

        $section = Section::find($data['section_id']);
        $academicYearId = $section?->academic_year_id ?? AcademicYear::current()->value('id');
        $present = $absent = $late = 0;

        $this->beginDatabaseTransaction();

        foreach ($data['rows'] as $row) {
            Attendance::updateOrCreate(
                ['student_id' => $row['student_id'], 'subject_id' => $data['subject_id'], 'date' => today()->toDateString()],
                [
                    'section_id' => $data['section_id'],
                    'teacher_id' => $teacher->id,
                    'academic_year_id' => $academicYearId,
                    'status' => $row['status'] ?? 'present',
                    'note' => $row['note'] ?? null,
                    'recorded_by' => auth()->id(),
                ],
            );

            match ($row['status'] ?? 'present') {
                'absent' => $absent++,
                'late' => $late++,
                default => $present++,
            };
        }

        $this->commitDatabaseTransaction();

        Notification::make()
            ->title('تم حفظ الحضور')
            ->body("حاضر: {$present} • غائب: {$absent} • متأخر: {$late}")
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')->label('حفظ الحضور')->icon('heroicon-o-check')->submit('save'),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('my-attendance-form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make($this->getFormActions())->alignment(Alignment::Start)->key('form-actions'),
                ]),
        ]);
    }
}
