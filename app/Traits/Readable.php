<?php

namespace App\Traits;

use Carbon\Carbon;

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

        if (is_null($query->getQuery()->columns)) {
            $query->addSelect('*');
        }
        return $query->selectRaw(
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
            ->byUser(auth()->user())
            ->first();

        if (is_null($readable)) {
            return true;
        }

        return $this->updated_at > $readable->read_at;
    }

    /**
     * Mark a readable model as read
     *
     * @param User $user
     * @return void
     */
    public function read($user = null)
    {
        $user = $user ?? auth()->user();

        $read = $this->reads()->byUser($user);

        if ($read->exists()) {
            $read->update(['read_at' => Carbon::now()]);
        } else {
            $this->reads()->create([
                'read_at' => Carbon::now(),
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * Mark a readable model as unread
     *
     * @param User $user
     * @return void
     */
    public function unread($user = null)
    {
        $user = $user ?? auth()->user();

        $this->reads()
            ->byUser($user)
            ->update(['read_at' => null]);
    }
}