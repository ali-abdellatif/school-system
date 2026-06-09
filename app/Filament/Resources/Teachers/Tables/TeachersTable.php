<?php

namespace App\Filament\Resources\Teachers\Tables;

use App\Models\AcademicYear;
use App\Models\Section;
use App\Models\Teacher;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TeachersTable
{
    /** @var array<string, string> */
    protected static array $statusLabels = [
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'on_leave' => 'في إجازة',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('اسم المعلم')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('employee_number')
                    ->label('رقم الموظف')
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                TextColumn::make('specialization')
                    ->label('التخصص')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('qualification')
                    ->label('المؤهل')
                    ->placeholder('—'),
                TextColumn::make('subjects_count')
                    ->label('عدد المواد')
                    ->counts('subjects')
                    ->badge()
                    ->color('info'),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => static::$statusLabels[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'on_leave' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('hire_date')
                    ->label('تاريخ التعيين')
                    ->date('Y-m-d')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(static::$statusLabels),
                SelectFilter::make('specialization')
                    ->label('التخصص')
                    ->options(fn (): array => Teacher::query()
                        ->whereNotNull('specialization')
                        ->distinct()
                        ->orderBy('specialization')
                        ->pluck('specialization', 'specialization')
                        ->all()),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('تصدير إلى Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(fn () => static::exportCsv()),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                Action::make('assignSubject')
                    ->label('تعيين مادة')
                    ->icon('heroicon-o-link')
                    ->color('primary')
                    ->schema([
                        Select::make('subject_id')
                            ->label('المادة')
                            ->options(fn (Teacher $record) => $record->subjects()->pluck('name', 'subjects.id'))
                            ->required()
                            ->searchable()
                            ->helperText('المواد المتاحة هي التي يُدرّسها المعلم (من تبويب «المواد والفصول»).'),
                        Select::make('section_id')
                            ->label('الفصل')
                            ->options(fn () => Section::query()->with('grade')->get()
                                ->mapWithKeys(fn (Section $s) => [$s->id => trim(($s->grade?->name ?? '') . ' - ' . $s->name)]))
                            ->required()
                            ->searchable(),
                        Select::make('academic_year_id')
                            ->label('السنة الدراسية')
                            ->options(fn () => AcademicYear::query()->orderByDesc('name')->pluck('name', 'id'))
                            ->default(fn () => AcademicYear::current()->value('id'))
                            ->required(),
                    ])
                    ->action(function (array $data, Teacher $record): void {
                        DB::table('teacher_section')->updateOrInsert(
                            [
                                'teacher_id' => $record->id,
                                'section_id' => $data['section_id'],
                                'subject_id' => $data['subject_id'],
                                'academic_year_id' => $data['academic_year_id'],
                            ],
                            ['updated_at' => now(), 'created_at' => now()],
                        );

                        Notification::make()->title('تم تعيين المادة للفصل')->success()->send();
                    }),

                Action::make('unassign')
                    ->label('إلغاء تعيين')
                    ->icon('heroicon-o-link-slash')
                    ->color('danger')
                    ->visible(fn (Teacher $record): bool => DB::table('teacher_section')->where('teacher_id', $record->id)->exists())
                    ->schema([
                        Select::make('assignment_id')
                            ->label('التعيين المراد إلغاؤه')
                            ->options(fn (Teacher $record) => static::assignmentOptions($record))
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        DB::table('teacher_section')->where('id', $data['assignment_id'])->delete();

                        Notification::make()->title('تم إلغاء التعيين')->success()->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @return array<int, string>
     */
    protected static function assignmentOptions(Teacher $teacher): array
    {
        return DB::table('teacher_section as ts')
            ->where('ts.teacher_id', $teacher->id)
            ->leftJoin('subjects', 'subjects.id', '=', 'ts.subject_id')
            ->leftJoin('sections', 'sections.id', '=', 'ts.section_id')
            ->selectRaw('ts.id, subjects.name as subject_name, sections.name as section_name')
            ->get()
            ->mapWithKeys(fn ($row): array => [
                $row->id => trim(($row->subject_name ?? 'مادة') . ' — ' . ($row->section_name ?? 'فصل')),
            ])
            ->all();
    }

    protected static function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['رقم الموظف', 'اسم المعلم', 'التخصص', 'المؤهل', 'الحالة', 'تاريخ التعيين', 'عدد المواد']);

            Teacher::query()
                ->with('user')
                ->withCount('subjects')
                ->chunk(200, function (Collection $teachers) use ($handle): void {
                    foreach ($teachers as $teacher) {
                        fputcsv($handle, [
                            $teacher->employee_number,
                            $teacher->user?->name,
                            $teacher->specialization,
                            $teacher->qualification,
                            static::$statusLabels[$teacher->status] ?? $teacher->status,
                            optional($teacher->hire_date)->format('Y-m-d'),
                            $teacher->subjects_count,
                        ]);
                    }
                });

            fclose($handle);
        }, 'teachers-' . now()->format('Ymd-His') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
