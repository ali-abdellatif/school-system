<?php

namespace App\Filament\Parent\Resources\LessonMaterials\Tables;

use App\Models\LessonMaterial;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class LessonMaterialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('عنوان الدرس')->searchable()->sortable(),
                TextColumn::make('subject.name')->label('المادة')->sortable(),
                TextColumn::make('teacher.user.name')->label('المعلم')->placeholder('—'),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => [
                        'pdf' => 'PDF', 'document' => 'مستند', 'image' => 'صورة', 'video' => 'فيديو', 'link' => 'رابط',
                    ][$state] ?? $state),
                TextColumn::make('created_at')->label('التاريخ')->date('Y-m-d')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('subject_id')->label('المادة')->relationship('subject', 'name')->preload(),
            ])
            ->recordActions([
                Action::make('open')
                    ->label('فتح / تحميل')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->url(function (LessonMaterial $record): ?string {
                        if ($record->external_url) {
                            return $record->external_url;
                        }

                        return $record->file_path ? Storage::url($record->file_path) : null;
                    }, shouldOpenInNewTab: true)
                    ->visible(fn (LessonMaterial $record): bool => filled($record->external_url) || filled($record->file_path)),
            ]);
    }
}
