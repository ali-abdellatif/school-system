<?php

namespace App\Filament\Resources\Students\Schemas;

use App\Models\AcademicYear;
use App\Models\Application;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Student;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section as FormSection;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormSection::make('البيانات الشخصية')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('photo')
                            ->label('الصورة الشخصية')
                            ->image()
                            ->avatar()
                            ->imageEditor()
                            ->directory('students/photos')
                            ->visibility('public')
                            ->columnSpanFull(),
                        TextInput::make('first_name')
                            ->label('الاسم الأول')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->label('الاسم الأخير')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('national_id')
                            ->label('الرقم القومي')
                            ->unique(ignoreRecord: true)
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
                        TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->maxLength(255),
                        Textarea::make('address')
                            ->label('العنوان')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                FormSection::make('البيانات الأكاديمية')
                    ->columns(2)
                    ->schema([
                        Select::make('academic_year_id')
                            ->label('السنة الدراسية')
                            ->relationship('academicYear', 'name')
                            ->default(fn () => AcademicYear::current()->value('id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set): void {
                                $set('grade_id', null);
                                $set('section_id', null);
                            }),
                        Select::make('grade_id')
                            ->label('الصف')
                            ->options(fn (Get $get) => Grade::query()
                                ->when($get('academic_year_id'), fn ($q, $year) => $q->where('academic_year_id', $year))
                                ->orderBy('level')
                                ->pluck('name', 'id'))
                            ->live()
                            ->dehydrated(false) // حقل مساعد فقط (لا يُحفظ — لا يوجد عمود grade_id للطالب)
                            ->afterStateHydrated(function (Set $set, $state, ?Student $record): void {
                                if (! $state && $record?->section) {
                                    $set('grade_id', $record->section->grade_id);
                                }
                            })
                            ->afterStateUpdated(fn (Set $set) => $set('section_id', null)),
                        Select::make('section_id')
                            ->label('الفصل')
                            ->options(fn (Get $get) => Section::query()
                                ->when($get('grade_id'), fn ($q, $grade) => $q->where('grade_id', $grade))
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                        Select::make('status')
                            ->label('الحالة')
                            ->required()
                            ->default('active')
                            ->options([
                                'active' => 'نشط',
                                'inactive' => 'غير نشط',
                                'graduated' => 'متخرج',
                                'transferred' => 'محوّل',
                            ]),
                        Select::make('application_id')
                            ->label('طلب القبول المرتبط')
                            ->options(fn () => Application::query()
                                ->latest('id')
                                ->limit(100)
                                ->get()
                                ->mapWithKeys(fn (Application $a) => [$a->id => "#{$a->id} — {$a->full_name}"]))
                            ->searchable()
                            ->placeholder('بدون')
                            ->columnSpanFull(),
                    ]),

                FormSection::make('ولي الأمر')
                    ->schema([
                        Select::make('parent_user_id')
                            ->label('ولي الأمر')
                            ->relationship('parentUser', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('غير مرتبط'),
                    ]),
            ]);
    }
}
