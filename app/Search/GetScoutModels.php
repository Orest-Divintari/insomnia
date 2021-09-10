<?php
namespace App\Search;

class GetScoutModels
{

    /**
     * Get the records and the associative relationships
     * for each model based on an array of ids
     *
     * @param mixed $model
     * @param int[] $ids
     * @param User $authUser
     * @return Collection
     */
    public static function getById($model, $ids, $authUser)
    {
        return $model->withSearchInfo($authUser)
            ->whereIn($model->getScoutKeyName(), $ids)
            ->get();
    }
}