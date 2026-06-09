<?php

namespace App\Filament\Resources\Subjects\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('اسم المادة')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label('الرمز')
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                TextColumn::make('grade.name')
                    ->label('الصف')
                    ->sortable(),
                TextColumn::make('teacher.user.name')
                    ->label('المعلم المسؤول')
                    ->placeholder('غير محدد'),
                TextColumn::make('weekly_hours')
                    ->label('الحصص الأسبوعية')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sections_count')
                    ->label('عدد الفصول')
                    ->counts('sections')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                SelectFilter::make('grade_id')
                    ->label('الصف')
                    ->relationship('grade', 'name')
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
