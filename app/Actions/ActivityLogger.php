<?php

namespace App\Actions;

use App\Activity;

class ActivityLogger
{

    protected $subject;
    protected $type;
    protected $description;
    protected $userId;
    protected $guestId;
    protected $subjectId;
    protected $subjectType;

    public function on($subject)
    {
        if ($subject) {
            $this->subjectId = $subject->id;
            $this->subjectType = get_class($subject);
        }
        return $this;
    }

    public function type($type = null)
    {
        if ($type) {
            $this->type = $type;
        }
        return $this;
    }

    public function description($description = null)
    {
        if ($description) {
            $this->description = $description;
        }
        return $this;
    }

    public function by($user = null)
    {
        $this->resetCauser();
        if ($user) {
            $this->userId = $user->id;
        }
        return $this;
    }

    public function byGuest()
    {
        $this->resetCauser();
        $this->guestId = $this->generateGuestId();
        return $this;
    }

    public function log()
    {
        return Activity::create($this->getAttributes());
    }

    private function getAttributes()
    {
        return [
            'user_id' => $this->getUserId(),
            'guest_id' => $this->getGuestId(),
            'description' => $this->description ?? null,
            'type' => $this->type ?? null,
            'subject_id' => $this->subjectId ?? null,
            'subject_type' => $this->subjectType ?? null,
        ];
    }

    private function getUserId()
    {
        if ($this->userId) {
            return $this->userId;
        }
        if (auth()->check() && !$this->guestId) {
            return auth()->id();
        }
        return null;
    }

    private function getGuestId()
    {
        if ($this->guestId) {
            return $this->guestId;
        }
        if (auth()->check()) {
            return null;
        }
        return $this->generateGuestId();
    }

    private function generateGuestId()
    {
        return csrf_token() ?? bcrypt(md5(uniqid(time(), true)));
    }

    private function resetCauser()
    {
        $this->guestId = null;
        $this->userId = null;
    }

}