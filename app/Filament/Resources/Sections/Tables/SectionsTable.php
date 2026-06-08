<?php

namespace App\Filament\Resources\Sections\Tables;

use App\Models\Section;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->withCount('students'))
            ->columns([
                TextColumn::make('name')
                    ->label('الفصل')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('grade.name')
                    ->label('الصف')
                    ->sortable(),
                TextColumn::make('academicYear.name')
                    ->label('السنة الدراسية')
                    ->placeholder('—'),
                TextColumn::make('homeroomTeacher.name')
                    ->label('مربّي الفصل')
                    ->placeholder('غير محدد'),
                TextColumn::make('capacity')
                    ->label('الإشغال')
                    ->badge()
                    ->state(fn (Section $record): string => $record->students_count . ' / ' . $record->max_students)
                    ->color(fn (Section $record): string => match (true) {
                        $record->max_students > 0 && ($record->students_count / $record->max_students) >= 0.9 => 'danger',
                        $record->max_students > 0 && ($record->students_count / $record->max_students) >= 0.7 => 'warning',
                        default => 'success',
                    }),
                TextColumn::make('max_students')
                    ->label('السعة القصوى')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('grade_id')
                    ->label('الصف')
                    ->relationship('grade', 'name')
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
