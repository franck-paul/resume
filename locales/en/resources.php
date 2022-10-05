<?php
/**
 * @package Dotclear
 * @subpackage Themes
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */

if (!isset(dcCore::app()->resources['help']['resume'])) {
    dcCore::app()->resources['help']['resume'] = dirname(__FILE__) . '/help/help.html';
}
