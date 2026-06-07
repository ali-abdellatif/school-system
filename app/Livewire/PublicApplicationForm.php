<?php

namespace App\Livewire;

use App\Models\Application;
use App\Models\Grade;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.public')]
#[Title('تقديم طلب القبول')]
class PublicApplicationForm extends Component
{
    public int $currentStep = 1;

    public bool $submitted = false;

    public ?int $applicationId = null;

    // الخطوة 1 — بيانات الطالب
    public string $first_name = '';
    public string $last_name = '';
    public ?string $birth_date = null;
    public ?string $gender = null;
    public ?string $nationality = null;
    public ?string $previous_school = null;
    public ?string $grade_applying_for = null;

    // الخطوة 2 — بيانات ولي الأمر
    public string $parent_name = '';
    public string $parent_phone = '';
    public ?string $parent_email = null;
    public ?string $parent_relation = null;
    public ?string $address = null;

    // الخطوة 3 — مراجعة وتأكيد
    public ?string $notes = null;

    /**
     * الحقول التابعة لكل خطوة (تُستخدم للتحقق التدريجي).
     *
     * @var array<int, array<int, string>>
     */
    protected array $stepFields = [
        1 => ['first_name', 'last_name', 'birth_date', 'gender', 'nationality', 'previous_school', 'grade_applying_for'],
        2 => ['parent_name', 'parent_phone', 'parent_email', 'parent_relation', 'address'],
        3 => ['notes'],
    ];

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'previous_school' => ['nullable', 'string', 'max:255'],
            'grade_applying_for' => ['nullable', 'exists:grades,id'],

            'parent_name' => ['required', 'string', 'max:255'],
            'parent_phone' => ['required', 'string', 'max:50'],
            'parent_email' => ['nullable', 'email', 'max:255'],
            'parent_relation' => ['required', 'in:father,mother,guardian'],
            'address' => ['nullable', 'string', 'max:1000'],

            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'first_name' => 'اسم الطالب',
            'last_name' => 'الاسم الأخير',
            'birth_date' => 'تاريخ الميلاد',
            'gender' => 'الجنس',
            'nationality' => 'الجنسية',
            'previous_school' => 'المدرسة السابقة',
            'grade_applying_for' => 'الصف المطلوب',
            'parent_name' => 'اسم ولي الأمر',
            'parent_phone' => 'رقم الهاتف',
            'parent_email' => 'البريد الإلكتروني',
            'parent_relation' => 'صلة القرابة',
            'address' => 'العنوان',
            'notes' => 'ملاحظات',
        ];
    }

    #[Computed]
    public function grades()
    {
        return Grade::orderBy('level')->orderBy('name')->pluck('name', 'id');
    }

    public function nextStep(): void
    {
        $this->validateStep($this->currentStep);

        $this->currentStep = min($this->currentStep + 1, 3);
    }

    public function previousStep(): void
    {
        $this->currentStep = max($this->currentStep - 1, 1);
    }

    protected function validateStep(int $step): void
    {
        $rules = collect($this->rules())
            ->only($this->stepFields[$step] ?? [])
            ->all();

        if ($rules !== []) {
            $this->validate($rules);
        }
    }

    public function submit(): void
    {
        // التحقق من جميع الخطوات قبل الحفظ.
        $validated = $this->validate();

        $application = Application::create(array_merge($validated, [
            'status' => 'pending',
        ]));

        $this->applicationId = $application->id;
        $this->submitted = true;
    }

    /**
     * تسميات للعرض في خطوة المراجعة.
     */
    public function genderLabel(): string
    {
        return match ($this->gender) {
            'male' => 'ذكر',
            'female' => 'أنثى',
            default => '—',
        };
    }

    public function relationLabel(): string
    {
        return match ($this->parent_relation) {
            'father' => 'الأب',
            'mother' => 'الأم',
            'guardian' => 'ولي أمر',
            default => '—',
        };
    }

    public function gradeLabel(): string
    {
        if (! $this->grade_applying_for) {
            return '—';
        }

        return $this->grades[$this->grade_applying_for] ?? '—';
    }

    public function render()
    {
        return view('livewire.public-application-form');
    }
}
