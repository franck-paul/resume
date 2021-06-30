<?php
/**
 * @brief Resume, a theme for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Themes
 *
 * @copyright Philippe aka amalgame
 * @copyright GPL-2.0-only
 */

namespace themes\resume;

if (!defined('DC_RC_PATH')) {
    return;
}
// public part below

if (!defined('DC_CONTEXT_ADMIN')) {
    return false;
}
// admin part below

# Behaviors
$GLOBALS['core']->addBehavior('adminPageHTMLHead', [__NAMESPACE__ . '\tplResumeThemeAdmin', 'adminPageHTMLHead']);
$GLOBALS['core']->addBehavior('adminPopupMedia', [__NAMESPACE__ . '\tplResumeThemeAdmin', 'adminPopupMedia']);
$GLOBALS['core']->addBehavior('adminPageHTTPHeaderCSP', [__NAMESPACE__ . '\tplResumeThemeAdmin','adminPageHTTPHeaderCSP']);

class tplResumeThemeAdmin
{
    public static function adminPageHTMLHead()
    {
        global $core;
        if ($core->blog->settings->system->theme != 'resume') {
            return;
        }

        if (preg_match('#^http(s)?://#', $core->blog->settings->system->themes_url)) {
            $theme_url = \http::concatURL($core->blog->settings->system->themes_url, '/' . $core->blog->settings->system->theme);
        } else {
            $theme_url = \http::concatURL($core->blog->url, $core->blog->settings->system->themes_url . '/' . $core->blog->settings->system->theme);
        }

        echo '<script src="' . $theme_url . '/js/admin.js' . '"></script>'."\n".
        '<script src="https://use.fontawesome.com/releases/v5.15.3/js/all.js" crossorigin="anonymous"></script>'."\n".
        '<link rel="stylesheet" media="screen" href="' . $theme_url . '/css/admin.css'. '" />'."\n";
        
        echo "\n" . '<!-- Header directives for Magalogue configuration -->' . "\n";
        $core->auth->user_prefs->addWorkspace('accessibility');
        if (!$core->auth->user_prefs->accessibility->nodragdrop) {
            echo
            \dcPage::jsLoad('js/jquery/jquery-ui.custom.js') .
            \dcPage::jsLoad('js/jquery/jquery.ui.touch-punch.js');
            echo <<<EOT
<script>
$(function() {
    $('#stickerslist').sortable({'cursor':'move'});
    $('#stickerslist tr').hover(function () {
        $(this).css({'cursor':'move'});
    }, function () {
        $(this).css({'cursor':'auto'});
    });
    $('#theme_config').submit(function() {
        var order=[];
        $('#stickerslist tr td input.position').each(function() {
            order.push(this.name.replace(/^order\[([^\]]+)\]$/,'$1'));
        });
        $('input[name=ds_order]')[0].value = order.join(',');
        return true;
    });
    $('#stickerslist tr td input.position').hide();
    $('#stickerslist tr td.handle').addClass('handler');
});
</script>
<style>
.linkimg img {
    padding: 3px;
    background-color: #fff;
}
</style>
EOT;
        }
    }
    
    public static function adminPopupMedia($editor = '')
    {
        $core = $GLOBALS['core'];

        if (empty($editor) || $editor != 'admin.blog.theme') {
            return;
        }
        if (preg_match('#^http(s)?://#', $core->blog->settings->system->themes_url)) {
            $theme_url = \http::concatURL($core->blog->settings->system->themes_url, '/' . $core->blog->settings->system->theme);
        } else {
            $theme_url = \http::concatURL($core->blog->url, $core->blog->settings->system->themes_url . '/' . $core->blog->settings->system->theme);
        }

        return '<script src="' . $theme_url . '/js/popup_media.js' . '"></script>';
    }

    public static function adminPageHTTPHeaderCSP($csp)
    {
        if (isset($csp['script-src'])) {
            $csp['script-src'] .= ' use.fontawesome.com';
        } else {
            $csp['script-src'] = 'use.fontawesome.com';
        }
    }
}
