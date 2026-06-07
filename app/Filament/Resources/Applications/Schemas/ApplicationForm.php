<?php

namespace App\Filament\Resources\Applications\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الطالب')
                    ->columns(2)
                    ->schema([
                        TextInput::make('first_name')
                            ->label('اسم الطالب')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->label('الاسم الأخير')
                            ->required()
                            ->maxLength(255),
                        DatePicker::make('birth_date')
                            ->label('تاريخ الميلاد')
                            ->required()
                            ->native(false)
                            ->maxDate(now()),
                        Select::make('gender')
                            ->label('الجنس')
                            ->required()
                            ->options([
                                'male' => 'ذكر',
                                'female' => 'أنثى',
                            ]),
                        TextInput::make('nationality')
                            ->label('الجنسية')
                            ->maxLength(255),
                        TextInput::make('previous_school')
                            ->label('المدرسة السابقة')
                            ->maxLength(255),
                        Select::make('grade_applying_for')
                            ->label('الصف المطلوب')
                            ->relationship('grade', 'name')
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make('بيانات ولي الأمر')
                    ->columns(2)
                    ->schema([
                        TextInput::make('parent_name')
                            ->label('اسم ولي الأمر')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('parent_phone')
                            ->label('رقم هاتف ولي الأمر')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('parent_email')
                            ->label('البريد الإلكتروني لولي الأمر')
                            ->email()
                            ->maxLength(255),
                        Select::make('parent_relation')
                            ->label('صلة القرابة')
                            ->required()
                            ->options([
                                'father' => 'الأب',
                                'mother' => 'الأم',
                                'guardian' => 'ولي أمر',
                            ]),
                        Textarea::make('address')
                            ->label('العنوان')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('ملاحظات')
                    ->schema([
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('حالة الطلب')
                    ->visibleOn('edit')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('الحالة')
                            ->required()
                            ->live()
                            ->options([
                                'pending' => 'قيد الانتظار',
                                'reviewing' => 'قيد المراجعة',
                                'approved' => 'مقبول',
                                'rejected' => 'مرفوض',
                            ]),
                        DateTimePicker::make('reviewed_at')
                            ->label('تاريخ المراجعة')
                            ->native(false)
                            ->disabled(),
                        Textarea::make('rejection_reason')
                            ->label('سبب الرفض')
                            ->rows(3)
                            ->columnSpanFull()
                            ->visible(fn (Get $get): bool => $get('status') === 'rejected'),
                    ]),
            ]);
    }
}
