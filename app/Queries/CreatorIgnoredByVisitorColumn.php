<?php

namespace App\Queries;

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
                AND    ignorations.ignorable_id=' . $query->model->getTable() . '.user_id' . '
                AND    ignorations.ignorable_type=?
            ) AS ignored_by_visitor', [$authUser->id, User::class]
            );
        });
    }
}