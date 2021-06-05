<?php
/**
 * @brief resume, a theme for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Themes
 *
 * @copyright Philippe aka amalgame
 * @copyright GPL-2.0-only
 */


if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

l10n::set(dirname(__FILE__) . '/locales/' . $_lang . '/admin');

$standalone_config = (boolean) $core->themes->moduleInfo($core->blog->settings->system->theme, 'standalone_config');
$resume_default_image_url = $GLOBALS['core']->blog->settings->system->themes_url."/".$GLOBALS['core']->blog->settings->system->theme."/img/profile.jpg";

$s = $GLOBALS['core']->blog->settings->themes->get($GLOBALS['core']->blog->settings->system->theme . '_style');
$s = @unserialize($s);

if (!is_array($s)) {
    $s = [];
}
if (!isset($s['resume_user_image']) || empty($s['resume_user_image'])) {
    $s['resume_user_image'] = $resume_default_image_url;
}

if (!isset($s['main_color'])) {
    $s['main_color'] = '#BD5D38';
}

// Load contextual help
if (file_exists(dirname(__FILE__) . '/locales/' . $_lang . '/resources.php')) {
    require dirname(__FILE__) . '/locales/' . $_lang . '/resources.php';
}

if (!empty($_POST)) {
    try {
        # HTML
        if (!empty($_POST['resume_user_image'])) {
            $s['resume_user_image'] = $_POST['resume_user_image'];
        } else {
            $s['resume_user_image'] = $resume_default_image_url;
        }
        $s['main_color'] = $_POST['main_color'];
        
        $core->blog->settings->addNamespace('themes');
        $core->blog->settings->themes->put($core->blog->settings->system->theme . '_style', serialize($s));

        // Blog refresh
        $core->blog->triggerBlog();

        // Template cache reset
        $core->emptyTemplatesCache();

        dcPage::success(__('Theme configuration upgraded.'), true, true);
    } catch (Exception $e) {
        $core->error->add($e->getMessage());
    }
}

// Legacy mode
if (!$standalone_config) {
    echo '</form>';
}

echo '<form id="theme_config" action="' . $core->adminurl->get('admin.blog.theme', ['conf' => '1']) .
    '" method="post" enctype="multipart/form-data">';

echo '<h4 class="pretty-title">' . __('Profile image') . '</h4>';

echo '<p> ' .
    '<img alt="' . __('Image URL:') . ' " src="'. $s['resume_user_image'] .'" class="img-profile" />' .
    '</p>';

echo '<p><label for="resume_user_image" class="classic">' . __('Image URL:') . '</label> ' .
    form::field('resume_user_image', 30, 255, html::escapeHTML($s['resume_user_image'])) .
    '</p>';

echo '<h4 class="pretty-title">' . __('Colors') . '</h4>';

echo '<p class="field maximal"><label for="main_color">' . __('Main color:') . '</label> ' .
    form::color('main_color', 30, 255, $s['main_color']) . '</p>' ;

echo '<p class="clear"><input type="submit" value="' . __('Save') . '" />' . $core->formNonce() . '</p>';
echo '</form>';


dcPage::helpBlock('resume');

// Legacy mode
if (!$standalone_config) {
    echo '<form style="display:none">';
}
