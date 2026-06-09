<?php

namespace App\Filament\Resources\Students\Tables;

use App\Filament\Pages\StudentReport;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class StudentsTable
{
    /** @var array<string, string> */
    protected static array $statusLabels = [
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'graduated' => 'متخرج',
        'transferred' => 'محوّل',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label('الصورة')
                    ->circular()
                    ->defaultImageUrl(asset('favicon.svg')),
                TextColumn::make('full_name')
                    ->label('الاسم')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name']),
                TextColumn::make('national_id')
                    ->label('الرقم القومي')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('gender')
                    ->label('الجنس')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'male' ? 'ذكر' : 'أنثى')
                    ->color(fn (string $state): array => $state === 'male' ? Color::Blue : Color::Pink),
                TextColumn::make('section.name')
                    ->label('الفصل')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('section.grade.name')
                    ->label('الصف')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => static::$statusLabels[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        'graduated' => 'info',
                        'transferred' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('age')
                    ->label('العمر')
                    ->formatStateUsing(fn ($state): string => filled($state) ? "{$state} سنة" : '—'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(static::$statusLabels),
                SelectFilter::make('grade')
                    ->label('الصف')
                    ->options(fn () => Grade::query()->orderBy('level')->pluck('name', 'id'))
                    ->query(fn (Builder $query, array $data): Builder => filled($data['value'])
                        ? $query->whereHas('section', fn (Builder $q) => $q->where('grade_id', $data['value']))
                        : $query),
                SelectFilter::make('section_id')
                    ->label('الفصل')
                    ->relationship('section', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('gender')
                    ->label('الجنس')
                    ->options(['male' => 'ذكر', 'female' => 'أنثى']),
                SelectFilter::make('academic_year_id')
                    ->label('السنة الدراسية')
                    ->relationship('academicYear', 'name')
                    ->preload(),
                TrashedFilter::make(),
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
                Action::make('report')
                    ->label('التقرير الأكاديمي')
                    ->icon('heroicon-o-document-chart-bar')
                    ->color('info')
                    ->url(fn (Student $record): string => StudentReport::getUrl(['student' => $record->id])),
                Action::make('transfer')
                    ->label('تحويل طالب')
                    ->icon('heroicon-o-arrows-right-left')
                    ->color('warning')
                    ->fillForm(fn (Student $record): array => ['section_id' => $record->section_id])
                    ->schema([
                        Select::make('section_id')
                            ->label('الفصل الجديد')
                            ->options(fn () => Section::query()->with('grade')->get()
                                ->mapWithKeys(fn (Section $s) => [$s->id => trim(($s->grade?->name ?? '') . ' - ' . $s->name)]))
                            ->required()
                            ->searchable(),
                        Textarea::make('note')
                            ->label('ملاحظة التحويل')
                            ->rows(2),
                    ])
                    ->action(function (array $data, Student $record): void {
                        $record->update(['section_id' => $data['section_id']]);

                        Notification::make()
                            ->title('تم تحويل الطالب إلى الفصل الجديد')
                            ->body($data['note'] ?? null)
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('graduate')
                        ->label('تخريج')
                        ->icon('heroicon-o-academic-cap')
                        ->color('info')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            $records->each->update(['status' => 'graduated']);

                            Notification::make()
                                ->title('تم تخريج الطلاب المحددين')
                                ->success()
                                ->send();
                        }),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    /**
     * تصدير الطلاب إلى ملف CSV متوافق مع Excel (UTF-8 مع BOM للعربية).
     */
    protected static function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');
            // BOM حتى تظهر العربية بشكل صحيح في Excel
            fwrite($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['#', 'الاسم', 'الرقم القومي', 'الجنس', 'تاريخ الميلاد', 'العمر', 'الصف', 'الفصل', 'الحالة']);

            Student::query()
                ->with('section.grade')
                ->chunk(200, function (Collection $students) use ($handle): void {
                    foreach ($students as $student) {
                        fputcsv($handle, [
                            $student->id,
                            $student->full_name,
                            $student->national_id,
                            $student->gender === 'male' ? 'ذكر' : 'أنثى',
                            optional($student->birth_date)->format('Y-m-d'),
                            $student->age,
                            $student->section?->grade?->name,
                            $student->section?->name,
                            static::$statusLabels[$student->status] ?? $student->status,
                        ]);
                    }
                });

            fclose($handle);
        }, 'students-' . now()->format('Ymd-His') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
