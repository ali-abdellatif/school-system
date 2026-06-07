<?php

namespace App\Filament\Pages;

use App\Models\SchoolSetting;
use App\Support\SchoolConfig;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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

class ManageSchoolSettings extends Page
{
    use CanUseDatabaseTransactions;

    protected static string|UnitEnum|null $navigationGroup = 'إعدادات الموقع';

    protected static ?string $navigationLabel = 'الإعدادات الأساسية';

    protected static ?string $title = 'الإعدادات الأساسية و SEO';

    protected static ?string $slug = 'school-settings';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 98;

    public ?array $data = [];

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->form->fill(SchoolSetting::current()->toArray());
    }

    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();

            SchoolSetting::current()->update($this->form->getState());

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
            ->title('تم حفظ الإعدادات الأساسية')
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
                Section::make('معلومات التواصل')
                    ->description('هوية المدرسة ووسائل التواصل الأساسية')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->label('اسم المدرسة')->required(),
                        TextInput::make('tagline')->label('الشعار الفرعي'),
                        TextInput::make('phone')->label('الهاتف')->tel(),
                        TextInput::make('whatsapp')->label('واتساب (بدون +)'),
                        TextInput::make('email')->label('البريد الإلكتروني')->email(),
                        TextInput::make('address')->label('العنوان'),
                        Textarea::make('map_embed_url')->label('رابط خريطة Google Embed')->rows(2)->columnSpanFull(),
                        Toggle::make('admission_open')->label('التقديم مفتوح')->columnSpanFull(),
                    ]),

                Section::make('تحسين محركات البحث (SEO)')
                    ->description('تظهر هذه البيانات في نتائج البحث وعند مشاركة رابط الموقع')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('عنوان الميتا (Meta Title)')
                            ->maxLength(60)
                            ->helperText('يُفضّل ألا يتجاوز 60 حرفًا.'),
                        Textarea::make('meta_description')
                            ->label('وصف الميتا (Meta Description)')
                            ->rows(2)
                            ->maxLength(160)
                            ->helperText('يُفضّل ألا يتجاوز 160 حرفًا.'),
                        Textarea::make('meta_keywords')
                            ->label('الكلمات المفتاحية (Meta Keywords)')
                            ->rows(2)
                            ->helperText('كلمات مفصولة بفواصل، مثل: مدرسة، قبول إلكتروني، التحاق.'),
                    ]),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('حفظ الإعدادات')
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('school-settings-form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make($this->getFormActions())
                            ->alignment(Alignment::Start)
                            ->key('form-actions'),
                    ]),
            ]);
    }
}
