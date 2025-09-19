<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use Auditable, HasFactory;

    protected $fillable = [
        'name',
        'code',
        'contact_person',
        'phone',
        'email',
        'address',
        'city',
        'province',
        'postal_code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Scope for active suppliers
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Relationships
    // public function products()
    // {
    //     return $this->hasMany(Product::class);
    // }

    // public function purchaseOrders()
    // {
    //     return $this->hasMany(PurchaseOrder::class);
    // }
}
