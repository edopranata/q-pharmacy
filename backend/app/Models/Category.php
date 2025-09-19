<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use Auditable, HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Scope for active categories
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Relationships
    // public function products()
    // {
    //     return $this->hasMany(Product::class);
    // }
}
