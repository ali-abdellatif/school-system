<?php

namespace App\Filament\Resources\Grades\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GradesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('الصف')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('level')
                    ->label('الترتيب')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('academicYear.name')
                    ->label('السنة الدراسية')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('sections_count')
                    ->label('الفصول')
                    ->counts('sections')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('students_count')
                    ->label('الطلاب')
                    ->counts('students')
                    ->badge()
                    ->color('info'),
            ])
            ->defaultSort('level')
            ->filters([
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
