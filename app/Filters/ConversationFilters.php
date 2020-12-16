<?php

namespace App\Filters;

use App\Actions\StringToArrayAction;
use App\Conversation;
use App\Filters\FilterInterface;
use App\User;
use Carbon\Carbon;

class ConversationFilters implements FilterInterface
{

    /**
     * Builder on which the filters are applied
     *
     * @var Illuminate\Database\Eloquent\Builder
     */
    protected $builder;

    /**
     * Supported filters for conversations
     *
     * @var string[]
     */
    public $filters = [
        'unread',
        'startedBy',
        'receivedBy',
        'recentAndUnread',
        'starred',
    ];

    /**
     * Return the builder
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function builder()
    {
        return $this->builder;
    }

    /**
     * Set the builder
     *
     * @param Illuminate\Database\Eloquent\Builder $builder
     * @return void
     */
    public function setBuilder($builder)
    {
        $this->builder = $builder;
    }

    /**
     * Get the unread conversations
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function unread()
    {
        return $this->builder
            ->whereHas('reads', function ($query) {
                $query->where('reads.user_id', auth()->id())
                    ->where(function ($query) {
                        $query
                            ->whereColumn('reads.read_at', '<', 'conversations.updated_at')
                            ->orWhereNull('reads.read_at');
                    });
            });
    }

    /**
     * Get the conversations that are started by the given user
     *
     * @param string $username
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function startedBy($usernames)
    {
        $namesArray = (new StringToArrayAction($usernames))->execute();

        $userIds = User::whereIn('name', $namesArray)->pluck('id');

        return $this->builder->whereIn('conversations.user_id', $userIds);
    }

    /**
     * Get the conversations that are received by the given usernames
     *
     * @param string $usernames
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function receivedBy($usernames)
    {
        $namesArray = (new StringToArrayAction($usernames))->execute();

        $userIds = User::whereIn('name', $namesArray)->pluck('id');

        return $this->builder
            ->whereHas('participants', function ($query) use ($userIds) {
                $query->whereIn('user_id', $userIds);
            });
    }

    /**
     * Get the unread and the last week conversations
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function recentAndUnread()
    {
        return $this->unread()
            ->orWhere(
                'conversations.updated_at',
                '>=',
                Carbon::now()->subWeek()->startOfDay()
            );
    }

    /**
     * Get the starred conversations
     *
     * @return Builder
     */
    public function starred()
    {
        return $this->builder->whereHas('participants', function ($query) {
            $query->whereColumn('conversation_participants.conversation_id', 'conversations.id')
                ->where('user_id', auth()->id())
                ->where('starred', true);
        });
    }
}