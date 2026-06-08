<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('البيانات الشخصية')
                    ->columns(2)
                    ->schema([
                        ImageEntry::make('photo')->label('الصورة')->circular()->columnSpanFull(),
                        TextEntry::make('full_name')->label('الاسم الكامل'),
                        TextEntry::make('national_id')->label('الرقم القومي')->placeholder('—'),
                        TextEntry::make('birth_date')->label('تاريخ الميلاد')->date('Y-m-d'),
                        TextEntry::make('age')->label('العمر')->suffix(' سنة'),
                        TextEntry::make('gender')
                            ->label('الجنس')
                            ->formatStateUsing(fn (string $state): string => $state === 'male' ? 'ذكر' : 'أنثى'),
                        TextEntry::make('phone')->label('الهاتف')->placeholder('—'),
                        TextEntry::make('address')->label('العنوان')->placeholder('—')->columnSpanFull(),
                    ]),

                Section::make('البيانات الأكاديمية')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('academicYear.name')->label('السنة الدراسية')->placeholder('—'),
                        TextEntry::make('section.grade.name')->label('الصف')->placeholder('—'),
                        TextEntry::make('section.name')->label('الفصل')->placeholder('—'),
                        TextEntry::make('status')
                            ->label('الحالة')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'active' => 'نشط',
                                'inactive' => 'غير نشط',
                                'graduated' => 'متخرج',
                                'transferred' => 'محوّل',
                                default => $state,
                            }),
                    ]),

                Section::make('ولي الأمر')
                    ->schema([
                        TextEntry::make('parentUser.name')->label('ولي الأمر')->placeholder('غير مرتبط'),
                    ]),
            ]);
    }
}
