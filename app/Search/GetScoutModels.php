<?php
namespace App\Search;

class GetScoutModels
{

    /**
     * Get the records for each model based on the ids
     *
     * @param mixed $model
     * @param int[] $modelKeys
     * @return Collection
     */
    public static function getById($model, $modelKeys)
    {
        return $model->withSearchInfo()
            ->whereIn('id', $modelKeys)
            ->get();
    }
}