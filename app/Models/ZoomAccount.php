<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZoomAccount extends Model
{
    protected $fillable = [
        'name',
        'email',
        'account_id',
        'client_id',
        'client_secret',
        'is_active',
        'description',
        'max_meetings_per_day',
        'max_participants',
        'features',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'features' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // العلاقات
    public function teachers(): HasMany
    {
        return $this->hasMany(User::class, 'zoom_account_id');
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(ZoomMeeting::class, 'zoom_account_id');
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors
    public function getStatusTextAttribute(): string
    {
        return $this->is_active ? 'نشط' : 'غير نشط';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
    }

    public function getFeaturesTextAttribute(): string
    {
        if (!$this->features) {
            return 'لا توجد ميزات خاصة';
        }

        $features = [];
        if (in_array('recording', $this->features)) {
            $features[] = 'تسجيل';
        }
        if (in_array('transcription', $this->features)) {
            $features[] = 'نسخ نصي';
        }
        if (in_array('breakout_rooms', $this->features)) {
            $features[] = 'غرف منفصلة';
        }
        if (in_array('polling', $this->features)) {
            $features[] = 'استطلاعات';
        }

        return empty($features) ? 'لا توجد ميزات خاصة' : implode(', ', $features);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
                    ->whereDoesntHave('teachers', function ($q) {
                        $q->whereNotNull('zoom_account_id');
                    });
    }

    public function scopeByFeature($query, $feature)
    {
        return $query->whereJsonContains('features', $feature);
    }
}
