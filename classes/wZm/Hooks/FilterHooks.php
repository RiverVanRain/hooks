<?php

namespace wZm\Hooks;

class FilterHooks {

	public function __invoke(\Elgg\Hook $hook) {
		elgg_unregister_plugin_hook_handler('validate', 'input', \wZm\Hooks\HtmlawedHooks::class, 1);
	}
}