<?php

namespace App\Queries;

use App\Models\User;

class CreatorIgnoredByVisitorColumn
{
    public function addSelect($query, $authUser)
    {
        return $query->when(isset($authUser), function ($query) use ($authUser) {
            return $query->select()->selectRaw('EXISTS
            (
                SELECT *
                FROM   ignorations
                WHERE  ignorations.user_id=?
                AND    ignorations.ignorable_id=' . $query->getModel()->getTable() . '.user_id' . '
                AND    ignorations.ignorable_type=?
            ) AS creator_ignored_by_visitor', [$authUser->id, User::class]
            );
        });
    }
}
