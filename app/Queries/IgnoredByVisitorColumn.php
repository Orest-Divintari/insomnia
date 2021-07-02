<?php

namespace App\Queries;

class IgnoredByVisitorColumn
{
    public function addSelect($query, $authUser)
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
                AND    ignorations.ignorable_id=' . $query->model->getTable() . '.id' . '
                AND    ignorations.ignorable_type=?
            ) AS ignored_by_visitor', [$authUser->id, get_class($this)]
            );
        });
    }
}