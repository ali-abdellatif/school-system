<?php

namespace App\Filament\Resources\Teachers\Schemas;

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الحساب')
                    ->description('اربط المعلم بمستخدم موجود أو أنشئ حسابًا جديدًا له.')
                    ->visibleOn('create')
                    ->columns(2)
                    ->schema([
                        Radio::make('account_mode')
                            ->label('طريقة الحساب')
                            ->options([
                                'existing' => 'اختيار مستخدم موجود',
                                'new' => 'إنشاء حساب جديد',
                            ])
                            ->default('existing')
                            ->inline()
                            ->live()                            ->columnSpanFull(),

                        Select::make('user_id')
                            ->label('المستخدم')
                            ->options(fn () => User::query()
                                ->whereDoesntHave('teacher')
                                ->orderBy('name')
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->required(fn (Get $get): bool => $get('account_mode') !== 'new')
                            ->visible(fn (Get $get): bool => $get('account_mode') !== 'new')
                            ->columnSpanFull(),

                        TextInput::make('name')
                            ->label('الاسم')                            ->required(fn (Get $get): bool => $get('account_mode') === 'new')
                            ->visible(fn (Get $get): bool => $get('account_mode') === 'new'),
                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()                            ->unique(table: User::class, column: 'email')
                            ->required(fn (Get $get): bool => $get('account_mode') === 'new')
                            ->visible(fn (Get $get): bool => $get('account_mode') === 'new'),
                        TextInput::make('password')
                            ->label('كلمة المرور')
                            ->password()
                            ->revealable()
                            ->minLength(8)                            ->required(fn (Get $get): bool => $get('account_mode') === 'new')
                            ->visible(fn (Get $get): bool => $get('account_mode') === 'new')
                            ->columnSpanFull(),
                    ]),

                Section::make('البيانات المهنية')
                    ->columns(2)
                    ->schema([
                        TextInput::make('employee_number')
                            ->label('رقم الموظف')
                            ->unique(ignoreRecord: true)
                            ->helperText('يُولّد تلقائيًا (EMP-001) إذا تُرك فارغًا.')
                            ->maxLength(255),
                        TextInput::make('specialization')
                            ->label('التخصص')
                            ->required()
                            ->maxLength(255),
                        Select::make('qualification')
                            ->label('المؤهل الدراسي')
                            ->options([
                                'دبلوم' => 'دبلوم',
                                'بكالوريوس' => 'بكالوريوس',
                                'ماجستير' => 'ماجستير',
                                'دكتوراه' => 'دكتوراه',
                            ]),
                        DatePicker::make('hire_date')
                            ->label('تاريخ التعيين')
                            ->native(false),
                        TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->maxLength(255),
                        Select::make('status')
                            ->label('الحالة')
                            ->required()
                            ->default('active')
                            ->options([
                                'active' => 'نشط',
                                'inactive' => 'غير نشط',
                                'on_leave' => 'في إجازة',
                            ]),
                        Textarea::make('address')
                            ->label('العنوان')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('المواد والفصول')
                    ->visibleOn('edit')
                    ->schema([
                        CheckboxList::make('subjects')
                            ->label('المواد التي يُدرّسها')
                            ->relationship('subjects', 'name')
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(2),
                        \Filament\Forms\Components\Placeholder::make('assigned_sections')
                            ->label('الفصول المُعيَّن لها (مادة — فصل)')
                            ->content(function (?Teacher $record): HtmlString|string {
                                if (! $record) {
                                    return '—';
                                }

                                $items = $record->sections()->get()->map(function ($section): string {
                                    $subject = Subject::find($section->pivot->subject_id);

                                    return ($subject?->name ?? 'مادة') . ' — ' . ($section->grade?->name ? $section->grade->name . ' ' : '') . $section->name;
                                });

                                if ($items->isEmpty()) {
                                    return 'لا توجد تعيينات بعد. استخدم إجراء «تعيين مادة».';
                                }

                                return new HtmlString(
                                    '<ul class="list-disc space-y-1 ps-5">' .
                                    $items->map(fn (string $i): string => '<li>' . e($i) . '</li>')->implode('') .
                                    '</ul>'
                                );
                            }),
                    ]),
            ]);
    }
}
