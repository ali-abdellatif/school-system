<?php

namespace App\Livewire;

use App\Models\Application;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.public')]
#[Title('الاستعلام عن حالة الطلب')]
class ApplicationStatus extends Component
{
    public ?string $application_id = null;

    public string $parent_phone = '';

    public bool $searched = false;

    public ?Application $application = null;

    /**
     * @var array<string, array{label: string, color: string, description: string}>
     */
    public array $statusMeta = [
        'pending' => ['label' => 'قيد الانتظار', 'color' => 'amber', 'description' => 'تم استلام الطلب وهو في انتظار المراجعة.'],
        'reviewing' => ['label' => 'قيد المراجعة', 'color' => 'blue', 'description' => 'يقوم فريق القبول بمراجعة الطلب حاليًا.'],
        'approved' => ['label' => 'مقبول', 'color' => 'green', 'description' => 'تهانينا! تم قبول الطلب. سيتم التواصل معكم لإكمال الإجراءات.'],
        'rejected' => ['label' => 'مرفوض', 'color' => 'red', 'description' => 'نأسف، لم يتم قبول الطلب.'],
    ];

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'application_id' => ['required', 'integer'],
            'parent_phone' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'application_id' => 'رقم الطلب',
            'parent_phone' => 'رقم الهاتف',
        ];
    }

    public function check(): void
    {
        $this->validate();

        $this->application = Application::query()
            ->where('id', $this->application_id)
            ->where('parent_phone', $this->parent_phone)
            ->first();

        $this->searched = true;
    }

    public function reset_search(): void
    {
        $this->reset(['application_id', 'parent_phone', 'searched', 'application']);
    }

    public function render()
    {
        return view('livewire.application-status');
    }
}
