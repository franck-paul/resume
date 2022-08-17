<?php
/**
 * @brief Resume, a theme for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Themes
 *
 * @author Philippe aka amalgame and contributors
 * @copyright GPL-2.0
 */

if (!defined('DC_RC_PATH')) {
    return;
}

$this->registerModule(
    'Resume',                                           // Name
    'A simple Bootstrap 5 blog theme',                  // Description
    'Philippe aka amalgame and contributors',           // Author
    '2.4',                                            // Version
    [                                                   // Properties
        'requires' => [['core', '2.19']], 		        // Dependencies
        'standalone_config' => true,
        'type' => 'theme',
        'tplset' => 'dotty'
    ]
);
