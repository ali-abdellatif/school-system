<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeacherInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات المعلم')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('user.name')->label('الاسم'),
                        TextEntry::make('employee_number')->label('رقم الموظف')->badge(),
                        TextEntry::make('user.email')->label('البريد الإلكتروني')->placeholder('—'),
                        TextEntry::make('phone')->label('الهاتف')->placeholder('—'),
                        TextEntry::make('specialization')->label('التخصص')->placeholder('—'),
                        TextEntry::make('qualification')->label('المؤهل')->placeholder('—'),
                        TextEntry::make('hire_date')->label('تاريخ التعيين')->date('Y-m-d')->placeholder('—'),
                        TextEntry::make('status')
                            ->label('الحالة')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'active' => 'نشط',
                                'inactive' => 'غير نشط',
                                'on_leave' => 'في إجازة',
                                default => $state,
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'inactive' => 'danger',
                                'on_leave' => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('address')->label('العنوان')->placeholder('—')->columnSpanFull(),
                    ]),

                Section::make('المواد التي يُدرّسها')
                    ->schema([
                        TextEntry::make('subjects.name')
                            ->label('المواد')
                            ->badge()
                            ->placeholder('لا توجد مواد'),
                    ]),
            ]);
    }
}
