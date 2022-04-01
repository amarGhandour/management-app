<?php

namespace App\Traits;

use App\Models\Activity;

trait RecordsActivity
{

    protected static function bootRecordsActivity()
    {

        foreach (static::getActivitiesEvent() as $event) {
            static::$event(function ($model) use ($event) {
                $model->recordsActivity($event);
            });

            static::deleted(function ($model) {
                $model->activity()->delete();
            });
        }

    }

    protected static function getActivitiesEvent()
    {
        return ['created'];
    }

    protected function recordsActivity($event): void
    {
        $this->activity()->create([
            'type' => $this->getActivityType($event),
            'user_id' => $this->user_id,
        ]);
    }

    public function activity()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    protected function getActivityType($event)
    {
        $type = lcfirst((new \ReflectionClass($this))->getShortName());
        return "{$event}_$type";
    }
}
