<?php

namespace App\Filament\Parent\Resources\GradeRecords\Tables;

use App\Models\GradeRecord;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GradeRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->groups([
                \Filament\Tables\Grouping\Group::make('subject.name')->label('المادة'),
            ])
            ->defaultGroup('subject.name')
            ->columns([
                TextColumn::make('student.full_name')->label('الابن')->sortable(),
                TextColumn::make('subject.name')->label('المادة')->sortable(),
                TextColumn::make('exam_type')
                    ->label('التقييم')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => [
                        'monthly1' => 'شهري 1', 'monthly2' => 'شهري 2', 'midterm' => 'نصف الفصل',
                        'final' => 'النهائي', 'assignment' => 'واجب', 'oral' => 'شفهي',
                    ][$state] ?? $state),
                TextColumn::make('score')
                    ->label('الدرجة')
                    ->formatStateUsing(fn ($state, GradeRecord $r): string => rtrim(rtrim((string) $state, '0'), '.') . ' / ' . rtrim(rtrim((string) $r->max_score, '0'), '.')),
                TextColumn::make('percentage')->label('النسبة %')->state(fn (GradeRecord $r): string => $r->percentage . '%')->badge()->color('gray'),
                TextColumn::make('letter_grade')
                    ->label('التقدير')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ممتاز', 'جيد جداً' => 'success', 'جيد' => 'info', 'مقبول' => 'warning', default => 'danger',
                    }),
                TextColumn::make('term')->label('الفصل')->formatStateUsing(fn (string $state): string => $state === 'first' ? 'الأول' : 'الثاني'),
            ])
            ->filters([
                SelectFilter::make('student_id')
                    ->label('الابن')
                    ->options(fn (): array => auth()->user()?->students()->get()->mapWithKeys(fn ($s) => [$s->id => $s->full_name])->all() ?? []),
                SelectFilter::make('subject_id')->label('المادة')->relationship('subject', 'name')->preload(),
                SelectFilter::make('term')->label('الفصل')->options(['first' => 'الفصل الأول', 'second' => 'الفصل الثاني']),
            ]);
    }
}
