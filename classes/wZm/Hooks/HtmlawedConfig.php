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

class HtmlawedConfig
{
    public function __invoke(\Elgg\Event $event)
    {
        $var = $event->getValue();

        if ((!is_string($var) && !is_array($var)) || empty($var) || isset($var['params']['head_extend']) || isset($var['params']['footer_extend'])) {
            return $var;
        }

        $htmlawed_deny_attribute = (string) elgg_get_plugin_setting('htmlawed_deny_attribute', 'hooks', '');

        $config = [
            // seems to handle about everything we need.
            'safe' => true,

            // remove comments/CDATA instead of converting to text
            'comment' => 1,
            'cdata' => 1,

            // do not check for unique ids as the full input stack could be checked multiple times
            // @see https://github.com/Elgg/Elgg/issues/12934
            'unique_ids' => 0,
            'elements' => '*-applet-button-form-input-textarea-script-style-embed-object+iframe+audio+video+(tiny-math-inline)+(tiny-math-block)+maction+math+maligngroup+malignmark+menclose+merror+mfenced+mfrac+mi+mmultiscripts+mn+mo+mover+mpadded+mphantom+mprescripts+mroot+mrow+ms+mspace+msqrt+mstyle+msub+msubsup+msup+mtable+mtd+mtext+mtr+munder+munderover+semantics+annotation+(annotation-xml)+mscarries+mscarry+msgroup+msline+msrow',
            'deny_attribute' => "on*, formaction, {$htmlawed_deny_attribute}",
            'hook_tag' => '_elgg_htmlawed_tag_post_processor',
            'schemes' => '*:http,https,ftp,news,mailto,rtsp,teamspeak,gopher,mms,callto,git,svn,rtmp,steam,nntp,sftp,ssh,tel,telnet,magnet,bitcoin,data',
            'child' => [
                'math' => '*',
            ],
        ];

        // add nofollow to all links on output
        if (!elgg_in_context('input')) {
            $config['anti_link_spam'] = ['/./', ''];
        }

        $config = elgg_trigger_event_results('config', 'htmlawed', [], $config);
        $spec = elgg_trigger_event_results('spec', 'htmlawed', [], '');

        if (!is_array($var)) {
            return $this->htmlawed($var, $config, $spec);
        }

        $callback = function (&$v, $k, $config_spec) {
            if (!is_string($v) || empty($v)) {
                return;
            }

            list ($config, $spec) = $config_spec;
            $v = $this->htmlawed($v, $config, $spec);
        };

        array_walk_recursive($var, $callback, [$config, $spec]);

        return $var;
    }

    /**
     * Filters the HTML
     *
     * @param string     $value  HTML
     * @param array|null $config configuration option.
     * @param mixed      $spec   specification option.
     *
     * @return string
     *
     * @see htmLawed()
     */
    protected function htmlawed(string $value, ?array $config = null, $spec = null): string
    {
        if ($config === null) {
            $config = [
                'anti_link_spam' => ['`.`', ''],
                'balance' => 1,
                'cdata' => 3,
                'safe' => 1,
                'comment' => 1,
                'css_expression' => 0,
                'deny_attribute' => 'on*,style',
                'direct_list_nest' => 1,
                'elements' => '*-applet-button-form-input-textarea-iframe-script-style-embed-object',
                'keep_bad' => 0,
                'schemes' => 'classid:clsid; href: aim, feed, file, ftp, gopher, http, https, irc, mailto, news, nntp, sftp, ssh, telnet; style: nil; *:file, http, https', // clsid allowed in class
                'unique_ids' => 0,
                'valid_xhtml' => 0,
            ];
        }

        if (isset($config['spec']) && !$spec) {
            $spec = $config['spec'];
        }

        if ($spec === null) {
            $spec = [
                'object=-classid-type, -codebase',
                'embed=type(oneof=application/x-shockwave-flash)'
            ];
        }

        return htmLawed($value, $config, $spec);
    }

    /**
     * Sanitizes style attribute
     *
     * This function triggers the 'allowed_styles', 'htmlawed' event
     *
     * @param \Elgg\Event $event 'attributes', 'htmlawed'
     *
     * @return void|array
     */
    public static function sanitizeStyles(\Elgg\Event $event)
    {
        $attributes = $event->getValue();
        $style = elgg_extract('style', $attributes);
        if (empty($style)) {
            return;
        }

        $allowed_styles = [
            'color', 'cursor', 'text-align', 'vertical-align', 'font-size',
            'font-weight', 'font-style', 'border', 'border-top', 'background-color',
            'border-bottom', 'border-left', 'border-right',
            'margin', 'margin-top', 'margin-bottom', 'margin-left',
            'margin-right', 'padding', 'float', 'text-decoration',
        ];

        $allowed_styles = elgg_trigger_event_results('allowed_styles', 'htmlawed', ['tag' => $event->getParam('tag')], $allowed_styles);

        $styles = explode(';', $style);

        $style_str = '';
        foreach ($styles as $style) {
            if (!trim($style) || !str_contains($style, ':')) {
                continue;
            }

            list($style_attr, $style_value) = explode(':', trim($style));
            $style_attr = trim($style_attr);
            $style_value = trim($style_value);

            if (in_array($style_attr, $allowed_styles)) {
                $style_str .= "{$style_attr}: {$style_value}; ";
            }
        }

        if (empty($style_str)) {
            unset($attributes['style']);
        } else {
            $attributes['style'] = trim($style_str);
        }

        return $attributes;
    }
}
