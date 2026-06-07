<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'birth_date',
        'gender',
        'nationality',
        'previous_school',
        'grade_applying_for',
        'parent_name',
        'parent_phone',
        'parent_email',
        'parent_relation',
        'address',
        'notes',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'reviewed_at' => 'datetime',
            'status' => 'string',
        ];
    }

    /**
     * الاسم الكامل للطالب.
     */
    protected function fullName(): Attribute
    {
        return Attribute::get(
            fn (): string => trim("{$this->first_name} {$this->last_name}"),
        );
    }

    /**
     * المراجِع (المستخدم الذي راجع الطلب).
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * الصف المطلوب الالتحاق به.
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'grade_applying_for');
    }
}
