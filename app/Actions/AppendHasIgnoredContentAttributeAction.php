<?php

namespace App\Actions;

class AppendHasIgnoredContentAttributeAction
{
    public function execute($dataset)
    {
        $hasIgnoredContent = collect([
            'has_ignored_content' => to_bool(
                collect($dataset->items())->contains(function ($item) {
                    return $item['creator_ignored_by_visitor'] === true || $item['ignored_by_visitor'] === true;
                })
            ),
        ]);

        return $hasIgnoredContent->merge($dataset);
    }
}