<div class="page-shell">
    <x-page-hero
        title="متابعة حالة طلب القبول"
        description="أدخل رقم الطلب ورقم هاتف ولي الأمر لعرض آخر حالة مسجلة لدى فريق القبول."
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => '/'],
            ['label' => 'متابعة الطلب', 'active' => true],
        ]"
    />

    <section class="page-content">
        <div class="grid gap-8 lg:grid-cols-[400px_1fr] lg:items-start">
            <x-content-card class="p-6 md:p-8">
                <h2 class="font-display text-2xl font-black text-brand">بيانات الاستعلام</h2>
                <p class="mt-2 text-sm font-normal leading-7 text-muted">
                    استخدم نفس رقم الهاتف الذي تم إدخاله أثناء التقديم.
                </p>

                <form wire:submit="check" class="mt-7 grid gap-5">
                    <div>
                        <label class="form-label">رقم الطلب <span>*</span></label>
                        <input type="number" wire:model="application_id" placeholder="مثال: 1024"
                               @class(['form-input', 'border-red-400' => $errors->has('application_id')]) />
                        @error('application_id') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="form-label">رقم هاتف ولي الأمر <span>*</span></label>
                        <input type="tel" wire:model="parent_phone" placeholder="010xxxxxxxx"
                               @class(['form-input', 'border-red-400' => $errors->has('parent_phone')]) />
                        @error('parent_phone') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" wire:loading.attr="disabled" class="btn-brand w-full px-7 py-4 disabled:opacity-60">
                        <span wire:loading.remove wire:target="check">عرض حالة الطلب</span>
                        <span wire:loading wire:target="check">جاري البحث...</span>
                    </button>
                </form>

                <div class="info-tip mt-7">
                    إذا فقدت رقم الطلب، تواصل مع شؤون الطلاب على
                    <span class="font-bold text-brand">{{ config('school.phone') }}</span>
                    أو
                    <span class="font-bold text-brand">{{ config('school.email') }}</span>.
                </div>
            </x-content-card>

            <x-content-card class="p-6 md:p-8">
                @if ($searched)
                    @if ($application)
                        @php
                            $meta = $statusMeta[$application->status] ?? ['label' => $application->status, 'description' => ''];
                            $badgeClass = match ($application->status) {
                                'pending' => 'status-badge--pending',
                                'reviewing' => 'status-badge--reviewing',
                                'approved' => 'status-badge--approved',
                                'rejected' => 'status-badge--rejected',
                                default => 'status-badge--pending',
                            };
                            $statusOrder = ['pending' => 1, 'reviewing' => 2, 'approved' => 3, 'rejected' => 3];
                            $currentOrder = $statusOrder[$application->status] ?? 1;
                        @endphp

                        <div class="flex flex-col gap-5 border-b border-slate-100 pb-7 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <span class="text-sm font-bold text-muted">طلب رقم #{{ $application->id }}</span>
                                <h2 class="mt-2 font-display text-3xl font-black text-brand">{{ $application->full_name }}</h2>
                                <p class="mt-2 text-sm text-muted">تاريخ التقديم: {{ $application->created_at->locale('ar')->translatedFormat('d F Y') }}</p>
                            </div>
                            <span class="status-badge {{ $badgeClass }}">{{ $meta['label'] }}</span>
                        </div>

                        <div class="mt-7 rounded-2xl border border-slate-100 bg-surface p-6">
                            <h3 class="font-black text-brand">رسالة فريق القبول</h3>
                            <p class="mt-2 font-normal leading-8 text-muted">{{ $meta['description'] }}</p>
                            @if ($application->status === 'rejected' && $application->rejection_reason)
                                <div class="mt-4 rounded-2xl border border-red-100 bg-red-50 p-4">
                                    <span class="block text-sm font-black text-red-700">سبب الرفض</span>
                                    <p class="mt-1 text-sm leading-7 text-red-600">{{ $application->rejection_reason }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="mt-8">
                            <h3 class="mb-5 font-black text-brand">مراحل الطلب</h3>
                            <div class="space-y-4">
                                @foreach ([
                                    ['order' => 1, 'title' => 'تم استلام الطلب', 'desc' => 'وصل الطلب إلى نظام القبول.'],
                                    ['order' => 2, 'title' => 'قيد المراجعة', 'desc' => 'يقوم فريق القبول بمراجعة البيانات.'],
                                    ['order' => 3, 'title' => $application->status === 'rejected' ? 'نتيجة الطلب' : 'القرار النهائي', 'desc' => 'سيتم تحديث الحالة النهائية هنا.'],
                                ] as $step)
                                    <div class="flex gap-4 rounded-xl border border-slate-100 p-4 transition hover:bg-surface">
                                        <span @class([
                                            'mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl text-sm font-black',
                                            'bg-emerald-500 text-white shadow-md shadow-emerald-500/20' => $currentOrder >= $step['order'] && $application->status !== 'rejected',
                                            'bg-red-500 text-white' => $application->status === 'rejected' && $step['order'] === 3,
                                            'bg-slate-100 text-slate-400' => $currentOrder < $step['order'],
                                        ])>{{ $step['order'] }}</span>
                                        <div>
                                            <h4 class="font-black text-slate-800">{{ $step['title'] }}</h4>
                                            <p class="mt-1 text-sm font-normal leading-7 text-muted">{{ $step['desc'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="flex min-h-[400px] flex-col items-center justify-center text-center">
                            <div class="state-icon state-icon--error">
                                <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                </svg>
                            </div>
                            <h2 class="font-display text-2xl font-black text-slate-800">لم يتم العثور على طلب مطابق</h2>
                            <p class="mt-3 max-w-md font-normal leading-8 text-muted">
                                راجع رقم الطلب ورقم الهاتف ثم حاول مرة أخرى.
                            </p>
                            <button type="button" wire:click="reset_search" class="btn-ghost mt-6 px-6 py-3">إدخال بيانات جديدة</button>
                        </div>
                    @endif
                @else
                    <div class="grid min-h-[400px] place-items-center text-center">
                        <div>
                            <div class="state-icon state-icon--waiting">
                                <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5h6M9 3h6a2 2 0 0 1 2 2v14l-5-3-5 3V5a2 2 0 0 1 2-2Z"/>
                                </svg>
                            </div>
                            <h2 class="font-display text-2xl font-black text-brand">حالة الطلب ستظهر هنا</h2>
                            <p class="mx-auto mt-3 max-w-md font-normal leading-8 text-muted">
                                بعد إدخال البيانات الصحيحة ستظهر حالة الطلب، رسالة القبول، ومراحل المراجعة.
                            </p>
                        </div>
                    </div>
                @endif
            </x-content-card>
        </div>
    </section>
</div>
