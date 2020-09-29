<?php

namespace App\Traits;

trait FormatsDate
{

    /**
     * Transform the date that it was updated to readable format
     *
     * @return string
     */
    public function getDateUpdatedAttribute()
    {
        return $this->updated_at->calendar();
    }

    /**
     * Transform the date that it was created to readable format
     *
     * @return string
     */
    public function getDateCreatedAttribute()
    {
        return $this->created_at->calendar();
    }

}