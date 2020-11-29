<?php

namespace App\Filters;

class ModelFilterChain
{

    /**
     * The array that stores the chain of filters
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Add a filter in the chain
     *
     * @param mixed $filter
     * @return void
     */
    public function addFilter($filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * Get the chain
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Add ConversationFilters to the chain
     *
     * @return void
     */
    public function withConversationFilters()
    {
        $this->addFilter(ConversationFilters::class);
    }

    /**
     * Add ThreadFilters to the chain
     *
     * @return void
     */
    public function withThreadFilters()
    {
        $this->addFilter(ThreadFilters::class);
    }

    /**
     * Add ReplyFilters to the chain
     *
     * @return void
     */
    public function withReplyFilters()
    {
        $this->addFilter(ReplyFilters::class);
    }
    /**
     * Add ProfilePostFilters to the chain
     *
     * @return void
     */
    public function withProfilePostFilters()
    {
        $this->addFilter(ProfilePostFilters::class);
    }

    /**
     * Chain ThreadFilters and ProfilePostFilters to the chain
     *
     * @return void
     */
    public function withAllPostsFilters()
    {
        $this->addFilter(ThreadFilters::class);
        $this->addFilter(ProfilePostFilters::class);
    }
}