<?php

$entity = elgg_extract('entity', $vars);

echo elgg_view_field([
    '#type' => 'fieldset',
    'fields' => [
        [
            '#type' => 'plaintext',
            '#label' => elgg_echo('settings:head_extend'),
            'name' => 'params[head_extend]',
            'value' => $entity->head_extend,
            '#help' => elgg_echo('settings:head_extend:help'),
        ],
        [
            '#type' => 'plaintext',
            '#label' => elgg_echo('settings:footer_extend'),
            'name' => 'params[footer_extend]',
            'value' => $entity->footer_extend,
            '#help' => elgg_echo('settings:footer_extend:help'),
        ],
        //Htmlawed
        [
            '#type' => 'text',
            '#label' => elgg_echo('settings:htmlawed_deny_attribute'),
            '#help' => elgg_echo('settings:htmlawed_deny_attribute:help'),
            'name' => 'params[htmlawed_deny_attribute]',
            'value' => $entity->htmlawed_deny_attribute ?: 'class',
        ],
    ],
]);
