<?php

namespace App\Filament\Teacher\Resources\LessonMaterials\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LessonMaterialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('عنوان الدرس')->searchable()->sortable(),
                TextColumn::make('subject.name')->label('المادة')->sortable(),
                TextColumn::make('section.name')->label('الفصل')->placeholder('كل الفصول'),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => [
                        'pdf' => 'PDF', 'document' => 'مستند', 'image' => 'صورة', 'video' => 'فيديو', 'link' => 'رابط',
                    ][$state] ?? $state),
                IconColumn::make('is_published')->label('منشور')->boolean(),
                TextColumn::make('created_at')->label('التاريخ')->date('Y-m-d')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('subject_id')->label('المادة')->relationship('subject', 'name')->preload(),
                SelectFilter::make('type')->label('النوع')->options([
                    'pdf' => 'PDF', 'document' => 'مستند', 'image' => 'صورة', 'video' => 'فيديو', 'link' => 'رابط',
                ]),
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
