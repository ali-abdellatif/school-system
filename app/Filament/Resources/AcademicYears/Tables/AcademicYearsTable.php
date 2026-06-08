<?php

namespace App\Filament\Resources\AcademicYears\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class AcademicYearsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('السنة الدراسية')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('تاريخ البداية')
                    ->date('Y-m-d')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('تاريخ النهاية')
                    ->date('Y-m-d')
                    ->placeholder('—')
                    ->sortable(),
                ToggleColumn::make('is_current')
                    ->label('السنة الحالية'),
                TextColumn::make('grades_count')
                    ->label('الصفوف')
                    ->counts('grades')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('students_count')
                    ->label('الطلاب')
                    ->counts('students')
                    ->badge()
                    ->color('info'),
            ])
            ->defaultSort('name', 'desc')
            ->recordActions([
                Action::make('setCurrent')
                    ->label('تعيين كسنة حالية')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('تعيين السنة الحالية')
                    ->visible(fn (\App\Models\AcademicYear $record): bool => ! $record->is_current)
                    ->action(function (\App\Models\AcademicYear $record): void {
                        $record->update(['is_current' => true]);

                        Notification::make()
                            ->title('تم تعيين السنة الدراسية الحالية')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
