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

class HtmlawedConfig {
	
	public function __invoke(\Elgg\Hook $hook) {
		$var = $hook->getValue();
		
		if ((!is_string($var) && !is_array($var)) || empty($var) || isset($var['params']['head_extend']) || isset($var['params']['footer_extend'])) {
			return $var;
		}
		
		$htmlawed_deny_attribute = elgg_get_plugin_setting('htmlawed_deny_attribute', 'hooks') ?: 'class';
	
		$config = [
			// seems to handle about everything we need.
			'safe' => true,
	
			// remove comments/CDATA instead of converting to text
			'comment' => 1,
			'cdata' => 1,
			
			// do not check for unique ids as the full input stack could be checked multiple times
			// @see https://github.com/Elgg/Elgg/issues/12934
			'unique_ids' => 0,
			'elements' => '*-applet-button-form-input-textarea-script-style-embed-object+iframe+audio+video',
			'deny_attribute' => "on*, formaction, {$htmlawed_deny_attribute}",
			'hook_tag' => '_elgg_htmlawed_tag_post_processor',
			'schemes' => '*:http,https,ftp,news,mailto,rtsp,teamspeak,gopher,mms,callto,git,svn,rtmp,steam,nntp,sftp,ssh,tel,telnet,magnet,bitcoin,data',
			// apparent this doesn't work.
			// 'style:color,cursor,text-align,font-size,font-weight,font-style,border,margin,padding,float'
		];
	
		// add nofollow to all links on output
		if (!elgg_in_context('input')) {
			$config['anti_link_spam'] = ['/./', ''];
		}
	
		$config = elgg_trigger_plugin_hook('config', 'htmlawed', null, $config);
		$spec = elgg_trigger_plugin_hook('spec', 'htmlawed', null, '');
	
		if (!is_array($var)) {
			return \Htmlawed::filter($var, $config, $spec);
		} else {
			$callback = function (&$v, $k, $config_spec) {
				if (!is_string($v) || empty($v)) {
					return;
				}
				
				list ($config, $spec) = $config_spec;
				$v = \Htmlawed::filter($v, $config, $spec);
			};
			
			array_walk_recursive($var, $callback, [$config, $spec]);
			
			return $var;
		}
	}
}
