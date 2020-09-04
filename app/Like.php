<?php

namespace App;

use App\Traits\FormatsDate;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use FormatsDate;

    protected $guarded = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['date_created'];

    /**
     * Fetch the reply that was liked
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reply()
    {
        return $this->belongsTo(Reply::class);
    }

}