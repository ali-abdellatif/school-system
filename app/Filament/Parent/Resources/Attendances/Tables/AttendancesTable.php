<?php

namespace App\Filament\Parent\Resources\Attendances\Tables;

use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendancesTable
{
    /** @var array<string, string> */
    protected static array $statusLabels = [
        'present' => 'حاضر', 'absent' => 'غائب', 'late' => 'متأخر', 'excused' => 'بعذر',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.full_name')->label('الابن')->sortable(),
                TextColumn::make('subject.name')->label('المادة')->sortable(),
                TextColumn::make('date')->label('التاريخ')->date('Y-m-d')->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => static::$statusLabels[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'present' => 'success', 'absent' => 'danger', 'late' => 'warning', 'excused' => 'info', default => 'gray',
                    }),
                TextColumn::make('note')->label('ملاحظة')->placeholder('—')->toggleable(),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('student_id')
                    ->label('الابن')
                    ->options(fn (): array => auth()->user()?->students()->get()->mapWithKeys(fn ($s) => [$s->id => $s->full_name])->all() ?? []),
                SelectFilter::make('status')->label('الحالة')->options(static::$statusLabels),
                SelectFilter::make('subject_id')->label('المادة')->relationship('subject', 'name')->preload(),
                Filter::make('date_range')
                    ->schema([
                        DatePicker::make('from')->label('من')->native(false),
                        DatePicker::make('until')->label('إلى')->native(false),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $d) => $q->whereDate('date', '>=', $d))
                        ->when($data['until'] ?? null, fn (Builder $q, $d) => $q->whereDate('date', '<=', $d))),
            ]);
    }
}
