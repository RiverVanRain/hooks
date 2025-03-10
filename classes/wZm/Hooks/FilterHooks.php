<?php

/**
 * Elgg Hooks
 * @author Nikolai Shcherbin
 * @package Elgg Hooks
 * @license GNU Affero General Public License version 3
 * @copyright (c) Nikolai Shcherbin 2023
 * @link https://wzm.me
**/

namespace wZm\Hooks;

class FilterHooks
{
    public function __invoke(\Elgg\Event $event)
    {
        elgg_unregister_event_handler('sanitize', 'input', \wZm\Hooks\HtmlawedConfig::class);
    }
}
