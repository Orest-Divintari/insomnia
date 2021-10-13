<?php declare (strict_types = 1);

return [
    'hosts' => [
        env('ELASTIC_HOST', 'elasticsearch:9200'),
    ],
];