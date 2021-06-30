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

# Simple menu template functions
$core->tpl->addValue('ResumeSimpleMenu', ['tplResumeSimpleMenu', 'resumeSimpleMenu']);

$core->tpl->addValue('resumeUserColors', ['tplResumeTheme', 'resumeUserColors']);
$core->tpl->addValue('resumeUserImageSrc', ['tplResumeTheme', 'resumeUserImageSrc']);
$core->tpl->addValue('resumeSocialLinks', ['tplResumeTheme', 'resumeSocialLinks']);

class tplResumeSimpleMenu
{
    # Template function
    public static function resumeSimpleMenu($attr)
    {
        global $core;

        if (!(boolean) $core->blog->settings->system->simpleMenu_active) {
            return '';
        }

        $class       = isset($attr['class']) ? trim($attr['class']) : '';
        $id          = isset($attr['id']) ? trim($attr['id']) : '';
        $description = isset($attr['description']) ? trim($attr['description']) : '';

        if (!preg_match('#^(title|span|both|none)$#', $description)) {
            $description = '';
        }

        return '<?php echo tplResumeSimpleMenu::displayMenu(' .
        "'" . addslashes($class) . "'," .
        "'" . addslashes($id) . "'," .
        "'" . addslashes($description) . "'" .
            '); ?>';
    }

    public static function displayMenu($class = '', $id = '', $description = '')
    {
        global $core;

        $ret = '';

        if (!(boolean) $core->blog->settings->system->simpleMenu_active) {
            return $ret;
        }

        $menu = $core->blog->settings->system->simpleMenu;
        if (is_array($menu)) {
            // Current relative URL
            $url     = $_SERVER['REQUEST_URI'];
            $abs_url = http::getHost() . $url;

            // Home recognition var
            $home_url       = html::stripHostURL($core->blog->url);
            $home_directory = dirname($home_url);
            if ($home_directory != '/') {
                $home_directory = $home_directory . '/';
            }

            // Menu items loop
            foreach ($menu as $i => $m) {
                # $href = lien de l'item de menu
                $href = $m['url'];
                $href = html::escapeHTML($href);

                # Cope with request only URL (ie ?query_part)
                $href_part = '';
                if ($href != '' && substr($href, 0, 1) == '?') {
                    $href_part = substr($href, 1);
                }

                $targetBlank = ((isset($m['targetBlank'])) && ($m['targetBlank'])) ? true : false;

                # Active item test
                $active = false;
                if (($url == $href) ||
                    ($abs_url == $href) ||
                    ($_SERVER['URL_REQUEST_PART'] == $href) ||
                    (($href_part != '') && ($_SERVER['URL_REQUEST_PART'] == $href_part)) ||
                    (($_SERVER['URL_REQUEST_PART'] == '') && (($href == $home_url) || ($href == $home_directory)))) {
                    $active = true;
                }
                $title = $span = '';

                if ($m['descr']) {
                    if (($description == 'title' || $description == 'both') && $targetBlank) {
                        $title = html::escapeHTML(__($m['descr'])) . ' (' .
                        __('new window') . ')';
                    } elseif ($description == 'title' || $description == 'both') {
                        $title = html::escapeHTML(__($m['descr']));
                    }
                    if ($description == 'span' || $description == 'both') {
                        $span = ' <span class="simple-menu-descr">' . html::escapeHTML(__($m['descr'])) . '</span>';
                    }
                }

                if (empty($title) && $targetBlank) {
                    $title = __('new window');
                }
                if ($active && !$targetBlank) {
                    $title = (empty($title) ? __('Active page') : $title . ' (' . __('active page') . ')');
                }

                $label = html::escapeHTML(__($m['label']));

                $item = new ArrayObject([
                    'url'    => $href,   // URL
                    'label'  => $label,  // <a> link label
                    'title'  => $title,  // <a> link title (optional)
                    'span'   => $span,   // description (will be displayed after <a> link)
                    'active' => $active, // status (true/false)
                    'class'  => ''      // additional <li> class (optional)
                ]);

                # --BEHAVIOR-- publicSimpleMenuItem
                $core->callBehavior('publicSimpleMenuItem', $i, $item);

                $ret .= '<li class="nav-item li' . ($i + 1) .
                    ($item['active'] ? ' active' : '') .
                    ($i == 0 ? ' li-first' : '') .
                    ($i == count($menu) - 1 ? ' li-last' : '') .
                    ($item['class'] ? ' ' . $item['class'] : '') .
                    '">' .
                    '<a class="nav-link js-scroll-trigger" href="' . $href . '"' .
                    (!empty($item['title']) ? ' title="'. $label . ' - ' . $item['title'] . '"' : '') .
                    (($targetBlank) ? ' target="_blank" rel="noopener noreferrer"' : '') . '>' .
                    '<span class="simple-menu-label">' . $item['label'] . '</span>' .
                    $item['span'] . '</a>' .
                    '</li>';
            }
            // Final rendering
            if ($ret) {
                $ret = '<ul ' . ($id ? 'id="' . $id . '"' : '') . ' class="simple-menu' . ($class ? ' ' . $class : '') . '">' . "\n" . $ret . "\n" . '</ul>';
            }
        }

        return $ret;
    }
}
class tplResumeTheme
{
    public static function resumeUserColors($attr)
    {
        $core = $GLOBALS['core'];

        $s = $core->blog->settings->themes->get($core->blog->settings->system->theme . '_style');
        $s = @unserialize($s);

        if (!is_array($s)) {
            $s = [];
        }
        if (!isset($s['main_color'])) {
            $s['main_color'] = '#bd5d38';
        }

        $resume_user_main_color = $s['main_color'];
        
        if (preg_match('#^http(s)?://#', $core->blog->settings->system->themes_url)) {
            $theme_url = \http::concatURL($core->blog->settings->system->themes_url, '/' . $core->blog->settings->system->theme);
        } else {
            $theme_url = \http::concatURL($core->blog->url, $core->blog->settings->system->themes_url . '/' . $core->blog->settings->system->theme);
        }
        
        $resume_user_colors_css_url = $theme_url ."/css/resume.user.colors.php";

        if ($resume_user_main_color !=='#bd5d38') {
            $resume_user_main_color = substr($resume_user_main_color, 1);
            return
            "<?php\n" .
            "echo \"<link rel='stylesheet' type='text/css' href='". $resume_user_colors_css_url ."?main_color=".$resume_user_main_color."' media='screen' />\"" .
                " ?>\n";
        } else {
            return;
        }
    }

    public static function resumeUserImageSrc($attr)
    {
        $core = $GLOBALS['core'];

        if (preg_match('#^http(s)?://#', $core->blog->settings->system->themes_url)) {
            $theme_url = \http::concatURL($core->blog->settings->system->themes_url, '/' . $core->blog->settings->system->theme);
        } else {
            $theme_url = \http::concatURL($core->blog->url, $core->blog->settings->system->themes_url . '/' . $core->blog->settings->system->theme);
        }
        
        $resume_default_image_url = $theme_url ."/img/profile.jpg";

        $s = $core->blog->settings->themes->get($core->blog->settings->system->theme . '_style');
        $s = @unserialize($s);

        if (!is_array($s)) {
            $s = [];
        }
        if (!isset($s['resume_user_image']) || empty($s['resume_user_image'])) {
            $s['resume_user_image'] = $resume_default_image_url;
        }
        return
            "<?php\n" .
            "echo \"".$s['resume_user_image']."\" ?>\n";
    }

    public static function resumeSocialLinks($attr)
    {
        global $core;
        # Social media links
        $res     = '';

        $s = $core->blog->settings->themes->get($core->blog->settings->system->theme . '_stickers');

        if ($s === null) {
            $default = true;
        } else {
            $s = @unserialize($s);
            
            $s = array_filter($s, 'self::cleanSocialLinks');
                
            $count = 0;
            foreach ($s as $sticker) {
                $res .= self::setSocialLink($count, ($count == count($s)), $sticker['label'], $sticker['url'], $sticker['image']);
                $count++;
            }
        }

        if ($res != '') {
            return $res;
        }
    }
    protected static function setSocialLink($position, $last, $label, $url, $image)
    {
        return
            '<a class="social-icon" title="' . $label . '" href="' . $url . '"><span class="sr-only">' . $label . '</span>' . "\n" .
            '<i class="' . $image . '"></i>' . "\n" .
            '</a>' . "\n";
    }

    protected static function cleanSocialLinks($s)
    {
        if (is_array($s)) {
            if (isset($s['label']) && isset($s['url']) && isset($s['image'])) {
                if ($s['label'] != null && $s['url'] != null && $s['image'] != null) {
                    return true;
                }
            }
        }
        return false;
    }
}
