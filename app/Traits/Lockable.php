<?php

namespace App\Traits;

trait Lockable
{

    /**
     * Lock the lockable item
     *
     * @return void
     */
    public function lock()
    {
        $this->locked = true;
        $this->save();
    }

    /**
     * Unlock the lockable item
     *
     * @return void
     */
    public function unlock()
    {
        $this->locked = false;
        $this->save();
    }
}