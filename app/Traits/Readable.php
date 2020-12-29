<?php

namespace App\Traits;

trait Readable
{
    /**
     * Add a column which determines whether the readable has been updated
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithHasBeenUpdated($query)
    {
        $readable = get_class($this);
        $readableTable = $this->getTable();
        $read = '(
            SELECT reads.read_at
            FROM   `reads`
            WHERE  reads.readable_id = ' . $readableTable . '.id
                AND reads.readable_type = ?
                AND reads.user_id = ?
        )';

        return $query->select()->selectRaw(
            'CASE
                WHEN ' . $read . ' >= ' . $readableTable . '.updated_at THEN 0
                WHEN ' . $read . ' IS NULL THEN 1
                ELSE 1
            END as has_been_updated',
            [
                $readable,
                auth()->id(),
                $readable,
                auth()->id(),
            ]
        );
    }

    /**
     * Determine whether the readable has been updated
     *
     * @return boolean
     */
    public function hasBeenUpdated()
    {
        if (!auth()->check()) {
            return true;
        }

        $readable = $this->reads()
            ->where('user_id', auth()->id())
            ->first();

        if (is_null($readable)) {
            return true;
        }

        return $this->updated_at > $readable->read_at;
    }
}