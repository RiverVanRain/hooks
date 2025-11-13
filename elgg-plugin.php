<?php

return [
    'plugin' => [
        'name' => 'Elgg Hooks',
        'version' => '5.0.0',
    ],

    'events' => [
        'action:validate' => [
            'plugins/settings/save' => [
                \wZm\Hooks\FilterHooks::class => ['priority' => 800],
            ],
        ],
        'sanitize' => [
            'input' => [
                \Elgg\Input\ValidateInputHandler::class => ['unregister' => true],
                \wZm\Hooks\HtmlawedConfig::class => ['priority' => 1],
            ],
        ],
    ],

    'view_extensions' => [
        'page/elements/head' => [
            'hooks/header_extend' => ['priority' => 1000],
        ],
        'page/elements/foot' => [
            'hooks/footer_extend' => ['priority' => 1000],
        ],
    ],
];
