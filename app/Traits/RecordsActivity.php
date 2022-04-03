<?php

namespace App\Traits;

use App\Models\Activity;
use App\Models\Project;

trait RecordsActivity
{

    public $oldAttributes = [];

    public static function bootRecordsActivity()
    {

        foreach (self::recordableEvents() as $event) {

            if ($event === 'updated') {
                static::updating(function ($model) {
                    $model->oldAttributes = $model->getOriginal();
                });
            }

            static::$event(function ($model) use ($event) {
                $model->recordsActivity($event);
            });

        }


    }

    protected static function recordableEvents()
    {

        if (isset(static::$recordableEvents)) {
            return static::$recordableEvents;
        }

        return ['created', 'updated'];
    }

    public function recordsActivity($event): void
    {
        $this->activity()->create([
            'type' => $this->getActivityType($event),
            'user_id' => $this->user_id,
            'changes' => $this->activityChanges($event),
            'project_id' => get_class($this) === Project::class ? $this->id : $this->project_id,
        ]);
    }

    protected function activityChanges($event)
    {
        if ($this->wasChanged()) {
            return [
                'before' => array_diff($this->oldAttributes, $this->getAttributes()),
                'after' => $this->getChanges(),
            ];
        }
    }

    public function activity()
    {
        if (get_class($this) === Project::class)
            return $this->hasMany(Activity::class)->latest();

        return $this->morphMany(Activity::class, 'subject');
    }


    protected function getActivityType($event)
    {
        $type = lcfirst((new \ReflectionClass($this))->getShortName());
        return "{$event}_$type";
    }
}
