<?php

namespace App\Traits;

use App\Activity;
use App\Like;
use App\ProfilePost;
use App\Reply;

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
        $class = class_basename($this);

        $class = ltrim(implode(' ', preg_split('/(?=[A-Z])/', $class)));

        $type = strtolower(implode("-", explode(" ", $class)));

        if (class_basename($this) == 'Reply') {
            if (class_basename($this->repliable_type) == 'ProfilePost') {
                $type = 'comment';
            } elseif (class_basename($this->repliable_type) == 'Thread') {
                $type = 'reply';
            }
        }
        if (get_class($this) == Like::class) {

            if ($this->likeable_type == Reply::class) {
                if (class_basename($this->likeable->repliable_type) == 'ProfilePost') {
                    $type = "comment-like";
                } elseif (class_basename($this->likeable->repliable_type) == 'Thread') {
                    $type = "reply-like";
                }
            } elseif ($this->likeable_type == ProfilePost::class) {
                $type = 'profile-post-like';
            }
        }

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