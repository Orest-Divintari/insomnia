<?php

namespace Tests\Setup;

abstract class Factory
{
    /**
     * Reset all attributes
     *
     * @return void
     */
    protected function resetAttributes()
    {
        foreach (get_object_vars($this) as $attribute => $value) {
            if ($attribute == 'faker') {
                continue;
            }
            if (is_array($attribute)) {
                $this->$attribute = [];
            } else {
                $this->$attribute = null;
            }
        }
    }
}