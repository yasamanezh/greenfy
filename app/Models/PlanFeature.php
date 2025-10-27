<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PlanFeature extends Pivot
{
    use LogsActivity;

    protected $table = 'plan_feature';

    protected $fillable = [
        'plan_id',
        'feature_id',
        'value',
        'is_available',
        'sort_order',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['value', 'is_available'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "ویژگی پلن {$eventName} شد")
            ->useLogName('plan_features');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }
}