<?php

namespace App\Traits;

use App\Helpers\ModelType;
use App\Models\Activity;

trait RecordsActivity
{
    /**
     * Boot the trait
     */
    public static function bootRecordsActivity()
    {
        foreach (self::recordableEvents() as $event) {
            static::$event(function ($model) use ($event) {

                if (!$model->shouldBeRecordable()) {
                    return;
                }

                $model->recordActivity($event);
            });
        }
    }

    /**
     * Record the activity
     *
     * @param string $activityType
     * @return void
     */
    public function recordActivity($event)
    {
        if (!auth()->check()) {
            return;
        }

        $this->activity()->create([
            'user_id' => auth()->id(),
            'type' => $this->getActivityType($event),
        ]);
    }

    /**
     * Determine the activity type
     *
     * @param string $event
     * @return string
     */
    public function getActivityType($event)
    {
        $type = ModelType::get($this);

        return "{$event}-{$type}";
    }

    /**
     * Get all the recording events for the model
     *
     * @return array
     */
    public static function recordableEvents()
    {
        // if(isset(static::recordableEvents)){
        //     $recordableEvents = self::recordableEvents;
        // }
        // else{
        //     $recordableEvents = ['created'];
        // }

        // return $recordableEvents;
        return ['created'];
    }

    /**
     * Get the activity relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activity()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    /**
     * Determine if the model should be recordable
     *
     * @return boolean
     */
    public function shouldBeRecordable()
    {
        return true;
    }
}
