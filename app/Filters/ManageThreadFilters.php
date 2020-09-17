<?php

use Dotenv\Validator;

namespace App\Filters;

class ManageThreadFilters
{

    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    /**
     * Get the filter keys and values passed in the request
     *
     * @return array
     */
    public function getThreadFilters()
    {
        $filters = $this->findFilters();

        $filters = $this->castValues($filters);

        $this->validateFilters($filters->toArray());

        return $filters;
    }

    /**
     * Find the the supported filters
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findFilters()
    {
        return collect($this->request->all())
            ->filter(function ($value, $key) {
                return in_array($key, $this->filters);
            });
    }

    /**
     * Cast the requested filter values to boolean
     *
     * @param \Illuminate\Database\Eloquent\Collection $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function castValues($filters)
    {
        return $filters->map(function ($value, $key) {

            if ($value == 'true') {
                $value = true;
            } elseif ($value == 'false') {
                $value = false;
            }
            return $value;
        });
    }

    /**
     * Validate the requested filters
     *
     * @param array $filters
     * @return void
     */
    public function validateFilters($filters)
    {
        return Validator::make($filters, [
            'postedBy' => "sometimes|required|string|exists:users,name",
            'contributed' => "sometimes|required|string|exists:users,name",
            'lastUpdated' => 'sometimes|required|integer|min:0',
            'lastCreated' => 'sometimes|required|integer|min:0',
            'newThreads' => 'sometimes|required|boolean',
            'newPosts' => 'sometimes|required|boolean',
            'watched' => 'sometimes|required|boolean',
            'unanswered' => 'sometimes|required|boolean',
            'trending' => 'sometimes|required|boolean',
            'numberOfReplies' => 'required|integer|min:0',
        ]);
    }

}