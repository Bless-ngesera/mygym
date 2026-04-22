<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'slug',
        'description',
        'currency',
        'billing_period',
        'duration_days',
        'features',
        'max_classes_per_week',
        'has_personal_trainer',
        'is_popular',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'has_personal_trainer' => 'boolean'
    ];

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(MemberSubscription::class, 'plan_id');
    }

    public function getFormattedPriceAttribute()
    {
        return ($this->currency ?? 'UGX') . ' ' . number_format($this->price, 0);
    }

    public function getFeatureListAttribute()
    {
        if (is_array($this->features)) {
            return $this->features;
        }
        return json_decode($this->features, true) ?? [];
    }
}
