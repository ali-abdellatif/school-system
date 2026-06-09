<x-filament-panels::page>
    {{-- رأس بيانات الطالب --}}
    <x-filament::section>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                @if ($student->photo)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($student->photo) }}" alt="{{ $student->full_name }}" class="h-16 w-16 rounded-full object-cover">
                @else
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary-100 text-xl font-bold text-primary-700">
                        {{ mb_substr($student->first_name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <h2 class="text-xl font-bold text-gray-950 dark:text-white">{{ $student->full_name }}</h2>
                    <p class="text-sm text-gray-500">
                        {{ $student->section?->grade?->name ?? '—' }} • فصل {{ $student->section?->name ?? '—' }}
                        @if ($student->academicYear) • {{ $student->academicYear->name }} @endif
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <label class="text-sm font-medium text-gray-600">الفصل الدراسي:</label>
                <select wire:model.live="term" class="rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="first">الفصل الأول</option>
                    <option value="second">الفصل الثاني</option>
                </select>
            </div>
        </div>
    </x-filament::section>

    {{-- ملخّص الحضور --}}
    <x-filament::section>
        <x-slot name="heading">ملخّص الحضور لكل مادة</x-slot>

        @if (empty($attendanceSummary))
            <p class="text-sm text-gray-500">لا توجد سجلات حضور بعد.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-gray-500">
                            <th class="p-2 text-start">المادة</th>
                            <th class="p-2 text-center">إجمالي الحصص</th>
                            <th class="p-2 text-center">حضور</th>
                            <th class="p-2 text-center">غياب</th>
                            <th class="p-2 text-center">النسبة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attendanceSummary as $row)
                            <tr class="border-b border-gray-100">
                                <td class="p-2 font-medium">{{ $row['subject'] }}</td>
                                <td class="p-2 text-center">{{ $row['total'] }}</td>
                                <td class="p-2 text-center text-green-600">{{ $row['present'] }}</td>
                                <td class="p-2 text-center text-red-600">{{ $row['absent'] }}</td>
                                <td class="p-2 text-center font-bold {{ \App\Filament\Pages\StudentReport::attendanceColor($row['percentage']) }}">
                                    {{ $row['percentage'] }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>

    {{-- الدرجات --}}
    <x-filament::section>
        <x-slot name="heading">الدرجات حسب المادة</x-slot>

        @if (empty($gradesBySubject))
            <p class="text-sm text-gray-500">لا توجد درجات مسجّلة لهذا الفصل الدراسي.</p>
        @else
            <div class="space-y-5">
                @foreach ($gradesBySubject as $subject)
                    <div class="rounded-xl border border-gray-100 p-4">
                        <div class="mb-3 flex items-center justify-between">
                            <h3 class="font-bold text-gray-800 dark:text-gray-100">{{ $subject['subject'] }}</h3>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-500">المعدل: <span class="font-bold text-primary-600">{{ $subject['average'] }}%</span></span>
                                <x-filament::badge>{{ $subject['letter'] }}</x-filament::badge>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($subject['records'] as $rec)
                                <span class="inline-flex items-center gap-1 rounded-lg bg-gray-50 px-3 py-1 text-xs dark:bg-white/5">
                                    <span class="text-gray-500">{{ $rec['exam_type'] }}:</span>
                                    <span class="font-semibold">{{ rtrim(rtrim((string) $rec['score'], '0'), '.') }} / {{ rtrim(rtrim((string) $rec['max_score'], '0'), '.') }}</span>
                                    <span class="text-gray-400">({{ $rec['percentage'] }}%)</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 border-t pt-4">
                <span class="text-lg font-bold text-gray-700">المعدل العام (GPA) للفصل:</span>
                <span class="text-2xl font-extrabold text-primary-600">{{ $gpa !== null ? $gpa . '%' : '—' }}</span>
                @if ($gpa !== null)
                    <x-filament::badge size="lg">{{ \App\Filament\Pages\StudentReport::letterForPercentage($gpa) }}</x-filament::badge>
                @endif
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
