<?php

namespace App\Filament\Resources\Applications\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use App\Models\AcademicYear;
use App\Models\Section;
use App\Models\Student;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ApplicationsTable
{
    /**
     * تسميات الحالات بالعربية.
     *
     * @var array<string, string>
     */
    protected static array $statusLabels = [
        'pending' => 'قيد الانتظار',
        'reviewing' => 'قيد المراجعة',
        'approved' => 'مقبول',
        'rejected' => 'مرفوض',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('full_name')
                    ->label('اسم الطالب')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name']),
                TextColumn::make('parent_name')
                    ->label('ولي الأمر')
                    ->searchable(),
                TextColumn::make('parent_phone')
                    ->label('رقم الهاتف')
                    ->searchable(),
                TextColumn::make('grade.name')
                    ->label('الصف المطلوب')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => static::$statusLabels[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'reviewing' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('تاريخ التقديم')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(static::$statusLabels),
                SelectFilter::make('grade_applying_for')
                    ->label('الصف المطلوب')
                    ->relationship('grade', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->label('عرض التفاصيل'),
                EditAction::make()
                    ->label('للمراجعة وتغيير الحالة فقط'),

                Action::make('approve')
                    ->label('قبول')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('قبول الطلب')
                    ->modalDescription('هل أنت متأكد من قبول هذا الطلب؟')
                    ->visible(fn ($record): bool => $record->status !== 'approved')
                    ->action(function ($record): void {
                        $record->update([
                            'status' => 'approved',
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                            'rejection_reason' => null,
                        ]);

                        Notification::make()
                            ->title('تم قبول الطلب بنجاح')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record): bool => $record->status !== 'rejected')
                    ->schema([
                        Textarea::make('rejection_reason')
                            ->label('سبب الرفض')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (array $data, $record): void {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);

                        Notification::make()
                            ->title('تم رفض الطلب')
                            ->danger()
                            ->send();
                    }),

                Action::make('convertToStudent')
                    ->label('تحويل لطالب')
                    ->icon('heroicon-o-user-plus')
                    ->color('primary')
                    ->modalHeading('تحويل الطلب إلى طالب')
                    ->modalSubmitActionLabel('تحويل')
                    ->visible(fn ($record): bool => $record->status === 'approved'
                        && ! Student::query()->where('application_id', $record->id)->exists())
                    ->schema([
                        Select::make('section_id')
                            ->label('الفصل')
                            ->options(fn () => Section::query()->with('grade')->get()
                                ->mapWithKeys(fn (Section $s) => [$s->id => trim(($s->grade?->name ?? '') . ' - ' . $s->name)]))
                            ->required()
                            ->searchable(),
                    ])
                    ->action(function (array $data, $record): void {
                        $section = Section::find($data['section_id']);

                        $student = Student::create([
                            'first_name' => $record->first_name,
                            'last_name' => $record->last_name,
                            'birth_date' => $record->birth_date,
                            'gender' => $record->gender,
                            'address' => $record->address,
                            'phone' => $record->parent_phone,
                            'status' => 'active',
                            'section_id' => $section?->id,
                            'academic_year_id' => $section?->academic_year_id ?? AcademicYear::current()->value('id'),
                            'application_id' => $record->id,
                        ]);

                        Notification::make()
                            ->title('تم تحويل الطلب إلى طالب بنجاح')
                            ->body("رقم الطالب: #{$student->id}")
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label('قبول المحدد')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            $records->each(fn ($record) => $record->update([
                                'status' => 'approved',
                                'reviewed_by' => auth()->id(),
                                'reviewed_at' => now(),
                                'rejection_reason' => null,
                            ]));

                            Notification::make()
                                ->title('تم قبول الطلبات المحددة')
                                ->success()
                                ->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
