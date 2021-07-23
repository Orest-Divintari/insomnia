<?php

namespace App\Traits;

use App\Models\Ignoration;
use App\Models\User;

trait Ignorable
{
    /**
     * It can be ignored
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function ignorations()
    {
        return $this->morphMany(Ignoration::class, 'ignorable');
    }

    /**
     * Determine whether it is ignored by the given user
     *
     * @param User $user
     * @return boolean
     */
    public function isIgnored(User $user)
    {
        return $this->ignorations()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user is not ignored by the given user
     *
     * @param User $user
     * @return boolean
     */
    public function isNotIgnored(User $user)
    {
        return !$this->isIgnored($user);
    }

    /**
     * Determine whether it is ignored by the authenticated user
     *
     * @param Builder $query
     * @param Bool $authenticated
     * @return Builder
     */
    public function scopeWithIgnoredByVisitor($query, $authUser)
    {
        return $query->when(isset($authUser), function ($query) use ($authUser) {
            if (is_null($query->getQuery()->columns)) {
                $query->addSelect('*');
            }
            return $query->selectRaw('EXISTS
            (
                SELECT *
                FROM   ignorations
                WHERE  ignorations.user_id=?
                AND    ignorations.ignorable_id=' . $this->getTable() . '.id' . '
                AND    ignorations.ignorable_type=?
            ) AS ignored_by_visitor', [$authUser->id, get_class($this)]
            );
        });
    }
}
