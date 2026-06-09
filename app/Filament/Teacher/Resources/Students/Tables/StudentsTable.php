<?php

namespace App\Filament\Teacher\Resources\Students\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')->label('الصورة')->circular()->defaultImageUrl(asset('favicon.svg')),
                TextColumn::make('full_name')->label('الطالب')->searchable(['first_name', 'last_name'])->sortable(),
                TextColumn::make('section.grade.name')->label('الصف')->placeholder('—'),
                TextColumn::make('section.name')->label('الفصل')->placeholder('—'),
                TextColumn::make('gender')->label('الجنس')->formatStateUsing(fn (string $state): string => $state === 'male' ? 'ذكر' : 'أنثى'),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'نشط', 'inactive' => 'غير نشط', 'graduated' => 'متخرج', 'transferred' => 'محوّل', default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success', 'inactive' => 'warning', 'graduated' => 'info', default => 'gray',
                    }),
            ])
            ->recordActions([
                ViewAction::make()->label('عرض'),
            ]);
    }
}
