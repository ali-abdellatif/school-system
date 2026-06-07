<div class="page-shell">
    <x-page-hero
        :title="'طلب الالتحاق بـ' . config('school.name')"
        description="املأ البيانات الأساسية، راجع الطلب، ثم احتفظ برقم الطلب لمتابعة الحالة لاحقاً."
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => '/'],
            ['label' => 'التقديم للقبول', 'active' => true],
        ]"
    >
        <strong class="text-accent">نصيحة سريعة:</strong>
        تأكد من صحة رقم هاتف ولي الأمر لأنه سيُستخدم في متابعة الطلب.
    </x-page-hero>

    <section class="page-content">
        @if ($submitted)
            <x-content-card class="mx-auto max-w-3xl p-8 text-center md:p-12">
                <div class="state-icon state-icon--success">
                    <svg class="h-11 w-11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="font-display text-3xl font-black text-brand">تم إرسال الطلب بنجاح</h2>
                <p class="mx-auto mt-3 max-w-xl font-normal leading-8 text-muted">
                    استلمنا طلبكم وسيتم مراجعته من فريق القبول. احتفظ برقم الطلب التالي لمتابعة الحالة.
                </p>
                <div class="mx-auto mt-8 inline-flex flex-col rounded-3xl bg-surface px-10 py-6 ring-1 ring-slate-100">
                    <span class="text-sm font-bold text-muted">رقم الطلب</span>
                    <span class="mt-2 font-display text-5xl font-black text-brand">#{{ $applicationId }}</span>
                </div>
                <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                    <x-button variant="brand" href="{{ route('application.status') }}">متابعة حالة الطلب</x-button>
                    <x-button variant="ghost" href="{{ route('apply') }}">تقديم طلب آخر</x-button>
                </div>
            </x-content-card>
        @else
            <div class="grid gap-8 lg:grid-cols-[1fr_340px] lg:items-start">
                <x-content-card>
                    <div class="content-card-header">
                        <x-step-wizard
                            :steps="[1 => 'بيانات الطالب', 2 => 'ولي الأمر', 3 => 'المراجعة']"
                            :current="$currentStep"
                        />
                    </div>

                    <div class="content-card-body">
                        @if ($currentStep === 1)
                            <div class="mb-7">
                                <h2 class="form-section-title">بيانات الطالب</h2>
                                <p class="form-section-subtitle">أدخل البيانات كما تظهر في الأوراق الرسمية.</p>
                            </div>
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <label class="form-label">اسم الطالب <span>*</span></label>
                                    <input type="text" wire:model="first_name" @class(['form-input', 'border-red-400' => $errors->has('first_name')]) />
                                    @error('first_name') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="form-label">الاسم الأخير <span>*</span></label>
                                    <input type="text" wire:model="last_name" @class(['form-input', 'border-red-400' => $errors->has('last_name')]) />
                                    @error('last_name') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="form-label">تاريخ الميلاد <span>*</span></label>
                                    <input type="date" wire:model="birth_date" max="{{ now()->toDateString() }}" @class(['form-input', 'border-red-400' => $errors->has('birth_date')]) />
                                    @error('birth_date') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="form-label">الجنس <span>*</span></label>
                                    <select wire:model="gender" @class(['form-input', 'border-red-400' => $errors->has('gender')])>
                                        <option value="">اختر...</option>
                                        <option value="male">ذكر</option>
                                        <option value="female">أنثى</option>
                                    </select>
                                    @error('gender') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="form-label">الجنسية</label>
                                    <input type="text" wire:model="nationality" class="form-input" />
                                    @error('nationality') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="form-label">المدرسة السابقة</label>
                                    <input type="text" wire:model="previous_school" class="form-input" />
                                    @error('previous_school') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="form-label">الصف المطلوب</label>
                                    <select wire:model="grade_applying_for" class="form-input">
                                        <option value="">اختر الصف...</option>
                                        @foreach ($this->grades as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('grade_applying_for') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif

                        @if ($currentStep === 2)
                            <div class="mb-7">
                                <h2 class="form-section-title">بيانات ولي الأمر</h2>
                                <p class="form-section-subtitle">هذه البيانات تساعد فريق القبول على التواصل معكم بسرعة.</p>
                            </div>
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <label class="form-label">اسم ولي الأمر <span>*</span></label>
                                    <input type="text" wire:model="parent_name" @class(['form-input', 'border-red-400' => $errors->has('parent_name')]) />
                                    @error('parent_name') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="form-label">رقم الهاتف <span>*</span></label>
                                    <input type="tel" wire:model="parent_phone" @class(['form-input', 'border-red-400' => $errors->has('parent_phone')]) />
                                    @error('parent_phone') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="form-label">البريد الإلكتروني</label>
                                    <input type="email" wire:model="parent_email" @class(['form-input', 'border-red-400' => $errors->has('parent_email')]) />
                                    @error('parent_email') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="form-label">صلة القرابة <span>*</span></label>
                                    <select wire:model="parent_relation" @class(['form-input', 'border-red-400' => $errors->has('parent_relation')])>
                                        <option value="">اختر...</option>
                                        <option value="father">الأب</option>
                                        <option value="mother">الأم</option>
                                        <option value="guardian">ولي أمر</option>
                                    </select>
                                    @error('parent_relation') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="form-label">العنوان</label>
                                    <textarea wire:model="address" rows="3" class="form-input"></textarea>
                                    @error('address') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif

                        @if ($currentStep === 3)
                            <div class="mb-7">
                                <h2 class="form-section-title">مراجعة وتأكيد</h2>
                                <p class="form-section-subtitle">راجع البيانات قبل إرسال الطلب النهائي.</p>
                            </div>
                            <div class="space-y-5">
                                <div class="review-card">
                                    <h3>بيانات الطالب</h3>
                                    <dl>
                                        <div><dt>اسم الطالب</dt><dd>{{ trim($first_name . ' ' . $last_name) ?: 'غير محدد' }}</dd></div>
                                        <div><dt>تاريخ الميلاد</dt><dd>{{ $birth_date ?: 'غير محدد' }}</dd></div>
                                        <div><dt>الجنس</dt><dd>{{ $this->genderLabel() }}</dd></div>
                                        <div><dt>الجنسية</dt><dd>{{ $nationality ?: 'غير محدد' }}</dd></div>
                                        <div><dt>المدرسة السابقة</dt><dd>{{ $previous_school ?: 'غير محدد' }}</dd></div>
                                        <div><dt>الصف المطلوب</dt><dd>{{ $this->gradeLabel() }}</dd></div>
                                    </dl>
                                </div>
                                <div class="review-card">
                                    <h3>بيانات ولي الأمر</h3>
                                    <dl>
                                        <div><dt>اسم ولي الأمر</dt><dd>{{ $parent_name ?: 'غير محدد' }}</dd></div>
                                        <div><dt>رقم الهاتف</dt><dd>{{ $parent_phone ?: 'غير محدد' }}</dd></div>
                                        <div><dt>البريد الإلكتروني</dt><dd>{{ $parent_email ?: 'غير محدد' }}</dd></div>
                                        <div><dt>صلة القرابة</dt><dd>{{ $this->relationLabel() }}</dd></div>
                                        <div class="sm:col-span-2"><dt>العنوان</dt><dd>{{ $address ?: 'غير محدد' }}</dd></div>
                                    </dl>
                                </div>
                                <div>
                                    <label class="form-label">ملاحظات إضافية</label>
                                    <textarea wire:model="notes" rows="3" class="form-input" placeholder="أي معلومات إضافية ترغب في إضافتها..."></textarea>
                                    @error('notes') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif

                        <div class="mt-8 flex items-center justify-between gap-3 border-t border-slate-100 pt-6">
                            <div>
                                @if ($currentStep > 1)
                                    <button type="button" wire:click="previousStep" class="btn-ghost px-6 py-3">السابق</button>
                                @endif
                            </div>
                            <div class="{{ $currentStep === 3 ? 'flex-1' : '' }}">
                                @if ($currentStep < 3)
                                    <button type="button" wire:click="nextStep" class="btn-brand px-8 py-3">التالي</button>
                                @else
                                    <button type="button" wire:click="submit" wire:loading.attr="disabled" class="btn-success w-full px-8 py-4 text-lg disabled:opacity-60">
                                        <span wire:loading.remove wire:target="submit">إرسال الطلب</span>
                                        <span wire:loading wire:target="submit">جاري الإرسال...</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </x-content-card>

                <aside class="info-panel">
                    <h3 class="font-display text-lg font-black text-brand">ما الذي يحدث بعد الإرسال؟</h3>
                    <div class="mt-5 space-y-3">
                        <div class="sidebar-step"><span>1</span><p>يصل الطلب إلى فريق القبول للمراجعة.</p></div>
                        <div class="sidebar-step"><span>2</span><p>يتم التواصل مع ولي الأمر عند الحاجة لمعلومات إضافية.</p></div>
                        <div class="sidebar-step"><span>3</span><p>يمكنك متابعة الحالة باستخدام رقم الطلب ورقم الهاتف.</p></div>
                    </div>
                    <x-button variant="ghost" href="{{ route('application.status') }}" class="mt-6 w-full">لدي رقم طلب بالفعل</x-button>
                </aside>
            </div>
        @endif
    </section>
</div>
