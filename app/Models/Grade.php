<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    protected $fillable = [
        'name',
        'level',
    ];

    /**
     * طلبات القبول المقدمة لهذا الصف.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'grade_applying_for');
    }
}
