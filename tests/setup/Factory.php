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
            if (is_array($attribute)) {
                $attribute = [];
            } else {
                $$attribute = null;
            }
        }
    }
}