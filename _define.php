<?php
/**
 * @brief Resume, a theme for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Themes
 *
 * @author Start Bootstrap and Philippe aka amalgame
 *
 * @copyright Philippe Hénaff philippe@dissitou.org
 * @copyright GPL-2.0
 */
if (!defined('DC_RC_PATH')) {
    return;
}

$this->registerModule(
    'Resume',
    'A simple Bootstrap 5 blog theme',
    'Philippe aka amalgame and contributors',
    '3.0',
    [
        'requires'          => [['core', '2.25']],
        'standalone_config' => true,
        'type'              => 'theme',
        'tplset'            => 'dotty',
    ]
);
