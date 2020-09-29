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
     * @return Collection
     */
    public static function getById($model, $ids)
    {
        return $model->withSearchInfo()
            ->whereIn(
                $model->getScoutKeyName(),
                $ids
            )->get();
    }
}