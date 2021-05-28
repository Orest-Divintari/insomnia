<?php

namespace App;

use App\Traits\FormatsDate;
use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use FormatsDate, RecordsActivity;

    protected $guarded = [];

    /**
     * Boot the Model
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($like) {
            $like->activities->each->delete();
        });
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['date_created'];

    /**
     * Get the activities of the like
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    /**
     * It has a likeable model
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function likeable()
    {
        return $this->morphTo();
    }

    /**
     * Determine if the activity for this model should be recorded
     *
     * @return boolean
     */
    public function shouldBeRecordable()
    {
        if ($this->likeable->repliable_type == 'App\Conversation') {
            return false;
        }
        return true;
    }

}