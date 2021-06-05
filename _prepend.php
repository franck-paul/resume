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

class tplResumeThemeAdmin
{
    public static function adminPageHTMLHead()
    {
        global $core;
        if ($core->blog->settings->system->theme != 'resume') {
            return;
        }

        echo "
        <style>
           .img-profile {
            border-radius: 50%;
            margin-left: 1rem;
            max-width: 15rem;
            border: .5rem #c9c9c9 solid;
           }
        </style>";
    }
}
