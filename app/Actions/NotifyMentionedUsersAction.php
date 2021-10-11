<?php

namespace App\Actions;

use App\Helpers\ModelType;
use App\Models\User;

class NotifyMentionedUsersAction
{
    /**
     * The name of the model as it is stored in the notification data column
     *
     * @param string $modelType
     */

    /**
     * Create a new instance
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param mixed $event
     * @param string $modelType
     * @param \Illuminate\Notifications\Notification $notification
     */
    public function __construct(protected $model, protected $event, protected $notification)
    {
        $this->eventType = get_class($event);
        $this->notificationType = get_class($notification);
        $this->modelType = ModelType::toCamelCase($model);
    }

    public function execute()
    {
        $mentionedUsersQuery = User::query()
            ->findByName($this->model->mentionedUsers())
            ->verified()
            ->except($this->event->poster)
            ->notIgnoring($this->event->poster);

        if ($this->isUpdatedEvent()) {
            $mentionedUsersQuery = $this->excludeAlreadyMentionedUsers($mentionedUsersQuery);
        }

        $mentionedUsersQuery->get()
            ->each(function ($mentionedUser) {
                $mentionedUser->notify($this->notification);
            });
    }

    /**
     * Filter out the users that have already been mentioned in the given model
     *
     * @param Builder $query
     * @return Builder
     */
    private function excludeAlreadyMentionedUsers($query)
    {
        return $query->whereDoesntHave('notifications', function ($query) {
            $query->where('type', $this->notificationType)
                ->whereJsonContains("data->{$this->modelType}->id", $this->model->id);
        });
    }

    /**
     * Determine whether the given event is an updated event
     *
     * @return boolean
     */
    private function isUpdatedEvent()
    {
        return str_ends_with(strtolower($this->eventType), 'updated');
    }
}