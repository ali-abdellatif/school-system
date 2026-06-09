<?php

namespace App\Filament\Teacher\Resources\GradeRecords\Tables;

use App\Models\GradeRecord;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GradeRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.full_name')->label('الطالب')->searchable(['first_name', 'last_name'])->sortable(),
                TextColumn::make('subject.name')->label('المادة')->sortable(),
                TextColumn::make('exam_type')
                    ->label('نوع التقييم')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => [
                        'monthly1' => 'شهري 1', 'monthly2' => 'شهري 2', 'midterm' => 'نصف الفصل',
                        'final' => 'النهائي', 'assignment' => 'واجب', 'oral' => 'شفهي',
                    ][$state] ?? $state),
                TextColumn::make('score')
                    ->label('الدرجة')
                    ->formatStateUsing(fn ($state, GradeRecord $record): string => rtrim(rtrim((string) $state, '0'), '.') . ' / ' . rtrim(rtrim((string) $record->max_score, '0'), '.')),
                TextColumn::make('percentage')->label('النسبة %')->state(fn (GradeRecord $r): string => $r->percentage . '%')->badge()->color('gray'),
                TextColumn::make('term')->label('الفصل')->formatStateUsing(fn (string $state): string => $state === 'first' ? 'الأول' : 'الثاني'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('subject_id')->label('المادة')->relationship('subject', 'name')->preload(),
                SelectFilter::make('term')->label('الفصل الدراسي')->options(['first' => 'الفصل الأول', 'second' => 'الفصل الثاني']),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
