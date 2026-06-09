<?php

namespace App\Filament\Resources\Attendances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendancesTable
{
    /** @var array<string, string> */
    protected static array $statusLabels = [
        'present' => 'حاضر',
        'absent' => 'غائب',
        'late' => 'متأخر',
        'excused' => 'بعذر',
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
                TextColumn::make('section.name')
                    ->label('الفصل والصف')
                    ->formatStateUsing(fn ($state, $record): string => trim(($record->section?->grade?->name ?? '') . ' ' . ($state ?? '')))
                    ->placeholder('—'),
                TextColumn::make('date')
                    ->label('التاريخ')
                    ->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => static::$statusLabels[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'present' => 'success',
                        'absent' => 'danger',
                        'late' => 'warning',
                        'excused' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('note')
                    ->label('ملاحظة')
                    ->limit(30)
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('recordedBy.name')
                    ->label('سُجّل بواسطة')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Filter::make('date_range')
                    ->schema([
                        DatePicker::make('from')->label('من تاريخ')->native(false),
                        DatePicker::make('until')->label('إلى تاريخ')->native(false),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $d) => $q->whereDate('date', '>=', $d))
                        ->when($data['until'] ?? null, fn (Builder $q, $d) => $q->whereDate('date', '<=', $d))),
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(static::$statusLabels),
                SelectFilter::make('section_id')
                    ->label('الفصل')
                    ->relationship('section', 'name')
                    ->preload(),
                SelectFilter::make('subject_id')
                    ->label('المادة')
                    ->relationship('subject', 'name')
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
