<?php

namespace App\Filament\Pages;

use App\Models\HomepageContent;
use App\Support\SchoolConfig;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Exceptions\Halt;
use Throwable;
use UnitEnum;

class ManageHomepage extends Page
{
    use CanUseDatabaseTransactions;

    protected static string|UnitEnum|null $navigationGroup = 'إعدادات الموقع';

    protected static ?string $navigationLabel = 'الصفحة الرئيسية';

    protected static ?string $title = 'محتوى الصفحة الرئيسية';

    protected static ?string $slug = 'homepage-content';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home-modern';

    protected static ?int $navigationSort = 99;

    public ?array $data = [];

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->form->fill(HomepageContent::current()->toArray());
    }

    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();

            HomepageContent::current()->update($this->form->getState());

            SchoolConfig::clearCache();

            $this->commitDatabaseTransaction();
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction()
                ? $this->rollBackDatabaseTransaction()
                : $this->commitDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        Notification::make()
            ->title('تم حفظ محتوى الصفحة الرئيسية')
            ->success()
            ->send();
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->operation('edit')
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('صور الموقع')
                    ->description('أدخل روابط الصور (Unsplash أو صور مرفوعة على الخادم)')
                    ->columns(2)
                    ->schema([
                        TextInput::make('hero_image')->label('صورة الهيدر')->url()->columnSpanFull(),
                        TextInput::make('classroom_image')->label('صورة الفصل')->url(),
                        TextInput::make('lab_image')->label('صورة المختبر')->url(),
                        TextInput::make('library_image')->label('صورة المكتبة')->url(),
                    ]),

                Section::make('الإحصائيات')
                    ->schema([
                        Repeater::make('stats')
                            ->label('الأرقام')
                            ->schema([
                                TextInput::make('count')->label('الرقم')->numeric()->required(),
                                TextInput::make('suffix')->label('لاحقة (+ أو %)'),
                                TextInput::make('label')->label('الوصف')->required(),
                            ])
                            ->columns(3)
                            ->defaultItems(4)
                            ->collapsible(),
                    ]),

                Section::make('الشهادات والاعتمادات')
                    ->schema([
                        Repeater::make('accreditations')
                            ->label('شارات الاعتماد')
                            ->simple(TextInput::make('badge')->label('نص الشارة')->required())
                            ->defaultItems(3),
                    ]),

                Section::make('آراء أولياء الأمور')
                    ->schema([
                        Repeater::make('testimonials')
                            ->label('التقييمات')
                            ->schema([
                                TextInput::make('name')->label('الاسم')->required(),
                                TextInput::make('role')->label('الصفة'),
                                Textarea::make('quote')->label('الرأي')->rows(2)->required(),
                                TextInput::make('rating')->label('التقييم (1-5)')->numeric()->minValue(1)->maxValue(5)->default(5),
                            ])
                            ->columns(2)
                            ->collapsible(),
                    ]),

                Section::make('معرض الصور')
                    ->schema([
                        Repeater::make('gallery')
                            ->label('الصور')
                            ->schema([
                                TextInput::make('title')->label('العنوان')->required(),
                                TextInput::make('category')->label('التصنيف'),
                                TextInput::make('image')->label('رابط الصورة')->url()->required(),
                            ])
                            ->columns(3)
                            ->collapsible(),
                    ]),

                Section::make('الأسئلة الشائعة')
                    ->schema([
                        Repeater::make('faq')
                            ->label('الأسئلة')
                            ->schema([
                                TextInput::make('question')->label('السؤال')->required(),
                                Textarea::make('answer')->label('الإجابة')->rows(3)->required(),
                            ])
                            ->collapsible(),
                    ]),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('حفظ المحتوى')
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('homepage-content-form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make($this->getFormActions())
                            ->alignment(Alignment::Start)
                            ->key('form-actions'),
                    ]),
            ]);
    }
}
