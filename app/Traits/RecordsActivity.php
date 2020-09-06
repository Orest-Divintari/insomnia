<?php

namespace App\Traits;

use App\Activity;

trait RecordsActivity
{
    /**
     * Boot the trait
     */
    public static function bootRecordsActivity()
    {
        if (!auth()->check()) {
            return;
        }

        $recordableEvents = self::recordableEvents();
        foreach ($recordableEvents as $event) {
            static::$event(function ($model) use ($event) {
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
        if (self::firstReply()) {
            return;
        };

        $this->activity()->create([
            'user_id' => auth()->id(),
            'type' => $this->getActivityType($event),
        ]);
    }

    /**
     * Determine if it is a reply
     * If it is a reply, check if it is the first one
     *
     * The first reply consists the body of the thread (it is not an actual reply)
     * Thus the activity should not be recorded
     *
     * @return boolean
     */
    public function firstReply()
    {
        return class_basename($this) == 'Reply' && $this->position == 1;
    }

    /**
     * Determine the activity type
     *
     * @param string $event
     * @return string
     */
    public function getActivityType($event)
    {

        $type = strtolower(class_basename($this));

        if (class_basename($this) == 'Reply') {
            if (class_basename($this->repliable_type) == 'ProfilePost') {
                $type = 'comment';
            } elseif (class_basename($this->repliable_type) == 'Thread') {
                $type = 'reply';
            }
        }

        return "{$event}_{$type}_activity";
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
}