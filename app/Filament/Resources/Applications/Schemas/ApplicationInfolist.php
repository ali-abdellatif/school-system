<?php

namespace App\Filament\Resources\Applications\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ApplicationInfolist
{
    /**
     * @var array<string, string>
     */
    protected static array $statusLabels = [
        'pending' => 'قيد الانتظار',
        'reviewing' => 'قيد المراجعة',
        'approved' => 'مقبول',
        'rejected' => 'مرفوض',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الطالب')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('full_name')->label('اسم الطالب'),
                        TextEntry::make('birth_date')->label('تاريخ الميلاد')->date('Y-m-d'),
                        TextEntry::make('gender')
                            ->label('الجنس')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'male' => 'ذكر',
                                'female' => 'أنثى',
                                default => $state,
                            }),
                        TextEntry::make('nationality')->label('الجنسية')->placeholder('—'),
                        TextEntry::make('previous_school')->label('المدرسة السابقة')->placeholder('—'),
                        TextEntry::make('grade.name')->label('الصف المطلوب')->placeholder('—'),
                    ]),

                Section::make('بيانات ولي الأمر')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('parent_name')->label('اسم ولي الأمر'),
                        TextEntry::make('parent_phone')->label('رقم الهاتف'),
                        TextEntry::make('parent_email')->label('البريد الإلكتروني')->placeholder('—'),
                        TextEntry::make('parent_relation')
                            ->label('صلة القرابة')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'father' => 'الأب',
                                'mother' => 'الأم',
                                'guardian' => 'ولي أمر',
                                default => $state,
                            }),
                        TextEntry::make('address')->label('العنوان')->placeholder('—')->columnSpanFull(),
                    ]),

                Section::make('ملاحظات')
                    ->schema([
                        TextEntry::make('notes')->label('ملاحظات')->placeholder('—')->columnSpanFull(),
                    ]),

                Section::make('حالة الطلب')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')
                            ->label('الحالة')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => static::$statusLabels[$state] ?? $state)
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'reviewing' => 'info',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('reviewedBy.name')->label('روجع بواسطة')->placeholder('—'),
                        TextEntry::make('reviewed_at')->label('تاريخ المراجعة')->dateTime('Y-m-d H:i')->placeholder('—'),
                        TextEntry::make('rejection_reason')
                            ->label('سبب الرفض')
                            ->placeholder('—')
                            ->columnSpanFull()
                            ->visible(fn ($record): bool => $record->status === 'rejected'),
                        TextEntry::make('created_at')->label('تاريخ التقديم')->dateTime('Y-m-d H:i'),
                    ]),
            ]);
    }
}
