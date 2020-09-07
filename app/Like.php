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
     * Relationships to always eager-load
     *
     * @var array
     */

    protected $with = ['reply'];
    /**
     * Fetch the reply that was liked
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reply()
    {
        return $this->belongsTo(Reply::class);
    }

    /**
     * Get the activities of the like
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

}