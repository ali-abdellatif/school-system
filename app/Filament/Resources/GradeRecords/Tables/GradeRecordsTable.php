<?php

namespace App\Filament\Resources\GradeRecords\Tables;

use App\Models\GradeRecord;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GradeRecordsTable
{
    /** @var array<string, string> */
    protected static array $examTypeLabels = [
        'monthly1' => 'شهري 1',
        'monthly2' => 'شهري 2',
        'midterm' => 'نصف الفصل',
        'final' => 'النهائي',
        'assignment' => 'واجب',
        'oral' => 'شفهي',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.full_name')
                    ->label('الطالب')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                TextColumn::make('subject.name')
                    ->label('المادة')
                    ->sortable(),
                TextColumn::make('exam_type')
                    ->label('نوع التقييم')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => static::$examTypeLabels[$state] ?? $state)
                    ->color(fn (string $state): string|array => match ($state) {
                        'monthly1', 'monthly2' => 'info',
                        'midterm' => 'warning',
                        'final' => 'danger',
                        'assignment' => 'gray',
                        'oral' => Color::Purple,
                        default => 'gray',
                    }),
                TextColumn::make('score')
                    ->label('الدرجة')
                    ->formatStateUsing(fn ($state, GradeRecord $record): string => rtrim(rtrim((string) $state, '0'), '.') . ' / ' . rtrim(rtrim((string) $record->max_score, '0'), '.')),
                TextColumn::make('percentage')
                    ->label('النسبة %')
                    ->state(fn (GradeRecord $record): string => $record->percentage . '%')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('letter_grade')
                    ->label('التقدير')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ممتاز', 'جيد جداً' => 'success',
                        'جيد' => 'info',
                        'مقبول' => 'warning',
                        default => 'danger',
                    }),
                TextColumn::make('term')
                    ->label('الفصل')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => $state === 'first' ? 'الفصل الأول' : 'الفصل الثاني'),
                TextColumn::make('academicYear.name')
                    ->label('السنة الدراسية')
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('subject_id')
                    ->label('المادة')
                    ->relationship('subject', 'name')
                    ->preload(),
                SelectFilter::make('exam_type')
                    ->label('نوع التقييم')
                    ->options(static::$examTypeLabels),
                SelectFilter::make('term')
                    ->label('الفصل الدراسي')
                    ->options(['first' => 'الفصل الأول', 'second' => 'الفصل الثاني']),
                SelectFilter::make('section_id')
                    ->label('الفصل')
                    ->relationship('section', 'name')
                    ->preload(),
                SelectFilter::make('academic_year_id')
                    ->label('السنة الدراسية')
                    ->relationship('academicYear', 'name')
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
