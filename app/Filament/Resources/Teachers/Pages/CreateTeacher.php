<?php

namespace App\Filament\Resources\Teachers\Pages;

use App\Filament\Resources\Teachers\TeacherResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateTeacher extends CreateRecord
{
    protected static string $resource = TeacherResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // إنشاء حساب مستخدم جديد عند اختيار هذا الوضع
        if (($data['account_mode'] ?? null) === 'new') {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $data['user_id'] = $user->id;
        }

        unset($data['account_mode'], $data['name'], $data['email'], $data['password']);

        return $data;
    }
}
