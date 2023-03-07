<?php
/**
 * @brief Ductile, a theme for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Themes
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */

namespace Dotclear\Theme\ductile;

use dcCore;
use dcNsProcess;
use dcPage;
use http;

class Prepend extends dcNsProcess
{
    public static function init(): bool
    {
        self::$init = defined('DC_CONTEXT_ADMIN');

        return self::$init;
    }

    public static function process(): bool
    {
        if (!self::$init) {
            return false;
        }

        dcCore::app()->addBehavior('adminPageHTMLHead', [self::class, 'adminPageHTMLHead']);
        dcCore::app()->addBehavior('adminPopupMedia', [self::class, 'adminPopupMedia']);
        dcCore::app()->addBehavior('adminPageHTTPHeaderCSP', [self::class, 'adminPageHTTPHeaderCSP']);

        return true;
    }

    public static function adminPageHTMLHead()
    {
        if (dcCore::app()->blog->settings->system->theme !== basename(dirname(__FILE__))) {
            return;
        }

        if (preg_match('#^http(s)?://#', dcCore::app()->blog->settings->system->themes_url)) {
            $theme_url = http::concatURL(dcCore::app()->blog->settings->system->themes_url, '/' . dcCore::app()->blog->settings->system->theme);
        } else {
            $theme_url = http::concatURL(dcCore::app()->blog->url, dcCore::app()->blog->settings->system->themes_url . '/' . dcCore::app()->blog->settings->system->theme);
        }

        echo '<script src="' . $theme_url . '/js/admin.js' . '"></script>' . "\n" .
        '<script src="https://use.fontawesome.com/releases/v5.15.3/js/all.js" crossorigin="anonymous"></script>' . "\n" .
        '<link rel="stylesheet" media="screen" href="' . $theme_url . '/css/admin.css' . '" />' . "\n";

        dcCore::app()->auth->user_prefs->addWorkspace('accessibility');
        if (!dcCore::app()->auth->user_prefs->accessibility->nodragdrop) {
            echo
            dcPage::jsLoad('js/jquery/jquery-ui.custom.js') .
            dcPage::jsLoad('js/jquery/jquery.ui.touch-punch.js');
        }
    }

    public static function adminPopupMedia($editor = '')
    {
        if (empty($editor) || $editor != 'admin.blog.theme') {
            return;
        }
        if (preg_match('#^http(s)?://#', dcCore::app()->blog->settings->system->themes_url)) {
            $theme_url = http::concatURL(dcCore::app()->blog->settings->system->themes_url, '/' . dcCore::app()->blog->settings->system->theme);
        } else {
            $theme_url = http::concatURL(dcCore::app()->blog->url, dcCore::app()->blog->settings->system->themes_url . '/' . dcCore::app()->blog->settings->system->theme);
        }

        return '<script src="' . $theme_url . '/js/popup_media.js' . '"></script>';
    }

    public static function adminPageHTTPHeaderCSP($csp)
    {
        if (dcCore::app()->blog->settings->system->theme !== basename(dirname(__FILE__))) {
            return;
        }

        if (isset($csp['script-src'])) {
            $csp['script-src'] .= ' use.fontawesome.com';
        } else {
            $csp['script-src'] = 'use.fontawesome.com';
        }
    }
}
