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

namespace Dotclear\Theme\Resume;

use ArrayObject;
use dcCore;
use dcNsProcess;
use dcThemeConfig;
use files;
use l10n;

class Frontend extends dcNsProcess
{
    public static function init(): bool
    {
        self::$init = defined('DC_RC_PATH');

        return self::$init;
    }

    public static function process(): bool
    {
        if (!self::$init) {
            return false;
        }

        l10n::set(__DIR__ . '/../locales/' . dcCore::app()->lang . '/main');

        # Templates
        dcCore::app()->tpl->addValue('ResumeSimpleMenu', [self::class, 'resumeSimpleMenu']);
        dcCore::app()->tpl->addValue('resumeUserColors', [self::class, 'resumeUserColors']);
        dcCore::app()->tpl->addValue('resumeUserImageSrc', [self::class, 'resumeUserImageSrc']);
        dcCore::app()->tpl->addValue('resumeSocialLinks', [self::class, 'resumeSocialLinks']);

        return true;
    }

    public static function resumeSimpleMenu(ArrayObject $attr): string
    {
        if (!(bool) dcCore::app()->blog->settings->system->simpleMenu_active) {
            return '';
        }

        $class       = isset($attr['class']) ? trim($attr['class']) : '';
        $id          = isset($attr['id']) ? trim($attr['id']) : '';
        $description = isset($attr['description']) ? trim($attr['description']) : '';

        if (!preg_match('#^(title|span|both|none)$#', $description)) {
            $description = '';
        }

        return '<?php echo ' . self::class . '::displayMenu(' .
        "'" . addslashes($class) . "'," .
        "'" . addslashes($id) . "'," .
        "'" . addslashes($description) . "'" .
            '); ?>';
    }

    public static function displayMenu(string $class = '', string $id = '', string $description = ''): string
    {
        $ret = '';

        if (!(bool) dcCore::app()->blog->settings->system->simpleMenu_active) {
            return $ret;
        }

        $menu = dcCore::app()->blog->settings->system->simpleMenu;
        if (is_array($menu)) {
            // Current relative URL
            $url     = $_SERVER['REQUEST_URI'];
            $abs_url = http::getHost() . $url;

            // Home recognition var
            $home_url       = html::stripHostURL(dcCore::app()->blog->url);
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
                if (($url == $href) || ($abs_url == $href) || ($_SERVER['URL_REQUEST_PART'] == $href) || (($href_part != '') && ($_SERVER['URL_REQUEST_PART'] == $href_part)) || (($_SERVER['URL_REQUEST_PART'] == '') && (($href == $home_url) || ($href == $home_directory)))) {
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
                    'class'  => '',      // additional <li> class (optional)
                ]);

                # --BEHAVIOR-- publicSimpleMenuItem
                dcCore::app()->callBehavior('publicSimpleMenuItem', $i, $item);

                $ret .= '<li class="nav-item li' . ($i + 1) .
                    ($item['active'] ? ' active' : '') .
                    ($i == 0 ? ' li-first' : '') .
                    ($i == count($menu) - 1 ? ' li-last' : '') .
                    ($item['class'] ? ' ' . $item['class'] : '') .
                    '">' .
                    '<a class="nav-link js-scroll-trigger" href="' . $href . '"' .
                    (!empty($item['title']) ? ' title="' . $label . ' - ' . $item['title'] . '"' : '') .
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

    public static function resumeUserColors($attr)
    {
        return '<?php echo ' . self::class . '::resumeUserColorsHelper(); ?>';
    }

    public static function resumeUserColorsHelper()
    {
        $style = dcCore::app()->blog->settings->themes->get(dcCore::app()->blog->settings->system->theme . '_style');
        $style = $style ? (unserialize($style) ?: []) : [];

        if (!is_array($style)) {
            $style = [];
        }
        if (!isset($style['main_color'])) {
            $style['main_color'] = '#bd5d38';
        }

        $resume_user_main_color = $style['main_color'];

        if (preg_match('#^http(s)?://#', dcCore::app()->blog->settings->system->themes_url)) {
            $theme_url = http::concatURL(dcCore::app()->blog->settings->system->themes_url, '/' . dcCore::app()->blog->settings->system->theme);
        } else {
            $theme_url = http::concatURL(dcCore::app()->blog->url, dcCore::app()->blog->settings->system->themes_url . '/' . dcCore::app()->blog->settings->system->theme);
        }

        $resume_user_colors_css_url = $theme_url . '/css/resume.user.colors.php';

        if ($resume_user_main_color != '#bd5d38') {
            $resume_user_main_color = substr($resume_user_main_color, 1);

            return '<link rel="stylesheet" type="text/css" href="' . $resume_user_colors_css_url . '?main_color=' . $resume_user_main_color . '" media="screen" />';
        }

    }

    //
    
    public static function ductileNbEntryPerPage(ArrayObject $attr): string
    {
        $nb = $attr['nb'] ?? null;

        return '<?php ' . self::class . '::ductileNbEntryPerPageHelper(' . strval((int) $nb) . '); ?>';
    }

    public static function ductileNbEntryPerPageHelper(int $nb)
    {
        $nb_other = $nb_first = 0;

        $s = dcCore::app()->blog->settings->themes->get(dcCore::app()->blog->settings->system->theme . '_entries_counts');
        if ($s !== null) {
            $s = @unserialize($s);
            if (is_array($s)) {
                switch (dcCore::app()->url->type) {
                    case 'default':
                    case 'default-page':
                        if (isset($s['default'])) {
                            $nb_first = $nb_other = (int) $s['default'];
                        }
                        if (isset($s['default-page'])) {
                            $nb_other = (int) $s['default-page'];
                        }

                        break;
                    default:
                        if (isset($s[dcCore::app()->url->type])) {
                            // Nb de billets par page défini par la config du thème
                            $nb_first = $nb_other = (int) $s[dcCore::app()->url->type];
                        }

                        break;
                }
            }
        }

        if ($nb_other == 0 && $nb) {
            // Nb de billets par page défini par défaut dans le template
            $nb_other = $nb_first = $nb;
        }

        if ($nb_other > 0) {
            dcCore::app()->ctx->nb_entry_per_page = $nb_other;
        }
        if ($nb_first > 0) {
            dcCore::app()->ctx->nb_entry_first_page = $nb_first;
        }
    }

    public static function EntryIfContentIsCut(ArrayObject $attr, string $content): string
    {
        if (empty($attr['cut_string']) || !empty($attr['full'])) {
            return '';
        }

        $urls = '0';
        if (!empty($attr['absolute_urls'])) {
            $urls = '1';
        }

        $short              = dcCore::app()->tpl->getFilters($attr);
        $cut                = $attr['cut_string'];
        $attr['cut_string'] = 0;
        $full               = dcCore::app()->tpl->getFilters($attr);
        $attr['cut_string'] = $cut;

        return '<?php if (strlen(' . sprintf($full, 'dcCore::app()->ctx->posts->getContent(' . $urls . ')') . ') > ' .
        'strlen(' . sprintf($short, 'dcCore::app()->ctx->posts->getContent(' . $urls . ')') . ')) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    public static function ductileEntriesList(ArrayObject $attr): string
    {
        $tpl_path   = __DIR__ . '/../tpl/';
        $list_types = ['title', 'short', 'full'];

        // Get all _entry-*.html in tpl folder of theme
        $list_types_templates = files::scandir($tpl_path);
        if (is_array($list_types_templates)) {
            foreach ($list_types_templates as $v) {
                if (preg_match('/^_entry\-(.*)\.html$/', $v, $m) && isset($m[1]) && !in_array($m[1], $list_types)) {
                    // template not already in full list
                    $list_types[] = $m[1];
                }
            }
        }

        $default = isset($attr['default']) ? trim((string) $attr['default']) : 'short';
        $ret     = '<?php ' . "\n" .
        'switch (' . self::class . '::ductileEntriesListHelper(\'' . $default . '\')) {' . "\n";

        foreach ($list_types as $v) {
            $ret .= '   case \'' . $v . '\':' . "\n" .
            '?>' . "\n" .
            dcCore::app()->tpl->includeFile(['src' => '_entry-' . $v . '.html']) . "\n" .
                '<?php ' . "\n" .
                '       break;' . "\n";
        }

        $ret .= '}' . "\n" .
            '?>';

        return $ret;
    }

    public static function ductileEntriesListHelper(?string $default): string
    {
        $s = dcCore::app()->blog->settings->themes->get(dcCore::app()->blog->settings->system->theme . '_entries_lists');
        if ($s !== null) {
            $s = @unserialize($s);
            if (is_array($s) && isset($s[dcCore::app()->url->type])) {
                return $s[dcCore::app()->url->type];
            }
        }

        return $default;
    }

    public static function ductileLogoSrc(): string
    {
        return '<?php echo ' . self::class . '::ductileLogoSrcHelper(); ?>';
    }

    public static function ductileLogoSrcHelper(): string
    {
        $img_url = dcCore::app()->blog->settings->system->themes_url . '/' . dcCore::app()->blog->settings->system->theme . '/img/logo.png';

        $s = dcCore::app()->blog->settings->themes->get(dcCore::app()->blog->settings->system->theme . '_style');
        if ($s === null) {
            // no settings yet, return default logo
            return $img_url;
        }
        $s = @unserialize($s);
        if (!is_array($s)) {
            // settings error, return default logo
            return $img_url;
        }

        if (isset($s['logo_src']) && $s['logo_src'] !== null && $s['logo_src'] != '') {
            if ((substr($s['logo_src'], 0, 1) == '/') || (parse_url($s['logo_src'], PHP_URL_SCHEME) != '')) {
                // absolute URL
                $img_url = $s['logo_src'];
            } else {
                // relative URL (base = img folder of ductile theme)
                $img_url = dcCore::app()->blog->settings->system->themes_url . '/' . dcCore::app()->blog->settings->system->theme . '/img/' . $s['logo_src'];
            }
        }

        return $img_url;
    }

    public static function IfPreviewIsNotMandatory(ArrayObject $attr, string $content): string
    {
        $s = dcCore::app()->blog->settings->themes->get(dcCore::app()->blog->settings->system->theme . '_style');
        if ($s !== null) {
            $s = @unserialize($s);
            if (is_array($s) && isset($s['preview_not_mandatory']) && $s['preview_not_mandatory']) {
                return $content;
            }
        }

        return '';
    }

    public static function publicInsideFooter(dcCore $core = null)
    {
        $res     = '';
        $default = false;
        $img_url = dcCore::app()->blog->settings->system->themes_url . '/' . dcCore::app()->blog->settings->system->theme . '/img/';

        $s = dcCore::app()->blog->settings->themes->get(dcCore::app()->blog->settings->system->theme . '_stickers');

        if ($s === null) {
            $default = true;
        } else {
            $s = @unserialize($s);
            if (!is_array($s)) {
                $default = true;
            } else {
                $s = array_filter($s, [self::class, 'cleanStickers']);
                if (count($s) == 0) {
                    $default = true;
                } else {
                    $count = 1;
                    foreach ($s as $sticker) {
                        $res .= self::setSticker($count, ($count == count($s)), $sticker['label'], $sticker['url'], $img_url . $sticker['image']);
                        $count++;
                    }
                }
            }
        }

        if ($default || $res == '') {
            $res = self::setSticker(1, true, __('Subscribe'), dcCore::app()->blog->url .
                dcCore::app()->url->getURLFor('feed', 'atom'), $img_url . 'sticker-feed.png');
        }

        if ($res != '') {
            $res = '<ul id="stickers">' . "\n" . $res . '</ul>' . "\n";
            echo $res;
        }
    }

    protected static function cleanStickers(array $s): bool
    {
        if (is_array($s) && isset($s['label']) && isset($s['url']) && isset($s['image']) && $s['label'] != null && $s['url'] != null && $s['image'] != null) {
            return true;
        }

        return false;
    }

    protected static function setSticker(int $position, bool $last, ?string $label = '', ?string $url = '', ?string $image = ''): string
    {
        return '<li id="sticker' . $position . '"' . ($last ? ' class="last"' : '') . '>' . "\n" .
            '<a href="' . $url . '">' . "\n" .
            '<img alt="" src="' . $image . '" />' . "\n" .
            '<span>' . $label . '</span>' . "\n" .
            '</a>' . "\n" .
            '</li>' . "\n";
    }

    public static function publicHeadContent()
    {
        echo
        '<style type="text/css">' . "\n" .
        '/* ' . __('Additionnal style directives') . ' */' . "\n" .
        self::ductileStyleHelper() .
            "</style>\n";

        echo
        '<script src="' .
        dcCore::app()->blog->settings->system->themes_url . '/' . dcCore::app()->blog->settings->system->theme .
            '/ductile.js"></script>' . "\n";

        echo self::ductileWebfontHelper();
    }

    public static function ductileWebfontHelper()
    {
        $s = dcCore::app()->blog->settings->themes->get(dcCore::app()->blog->settings->system->theme . '_style');

        if ($s === null) {
            return;
        }

        $s = @unserialize($s);
        if (!is_array($s)) {
            return;
        }

        $ret = '';
        $css = [];
        $uri = [];
        if (!isset($s['body_font']) || ($s['body_font'] == '') && isset($s['body_webfont_api']) && isset($s['body_webfont_family']) && isset($s['body_webfont_url'])) {
            // See if webfont defined for main font
            $uri[] = $s['body_webfont_url'];
            switch ($s['body_webfont_api']) {
                case 'js':
                    $ret .= sprintf('<script src="%s"></script>', $s['body_webfont_url']) . "\n";

                    break;
                case 'css':
                    $ret .= sprintf('<link type="text/css" href="%s" rel="stylesheet" />', $s['body_webfont_url']) . "\n";

                    break;
            }
            # Main font
            $selectors = 'body, .supranav li a span, #comments.me, a.comment-number';
            dcThemeConfig::prop($css, $selectors, 'font-family', $s['body_webfont_family']);
        }
        if (!isset($s['alternate_font']) || ($s['alternate_font'] == '') && isset($s['alternate_webfont_api']) && isset($s['alternate_webfont_family']) && isset($s['alternate_webfont_url'])) {
            // See if webfont defined for secondary font
            if (!in_array($s['alternate_webfont_url'], $uri)) {
                switch ($s['alternate_webfont_api']) {
                    case 'js':
                        $ret .= sprintf('<script src="%s"></script>', $s['alternate_webfont_url']) . "\n";

                        break;
                    case 'css':
                        $ret .= sprintf('<link type="text/css" href="%s" rel="stylesheet" />', $s['alternate_webfont_url']) . "\n";

                        break;
                }
            }
            # Secondary font
            $selectors = '#blogdesc, .supranav, #content-info, #subcategories, #comments-feed, #sidebar h2, #sidebar h3, #footer';
            dcThemeConfig::prop($css, $selectors, 'font-family', $s['alternate_webfont_family']);
        }
        # Style directives
        $res = '';
        foreach ($css as $selector => $values) {
            $res .= $selector . " {\n";
            foreach ($values as $k => $v) {
                $res .= $k . ':' . $v . ";\n";
            }
            $res .= "}\n";
        }
        if ($res != '') {
            $ret .= '<style type="text/css">' . "\n" . $res . '</style>' . "\n";
        }

        return $ret;
    }

    public static function ductileStyleHelper()
    {
        $s = dcCore::app()->blog->settings->themes->get(dcCore::app()->blog->settings->system->theme . '_style');

        if ($s === null) {
            return;
        }

        $s = @unserialize($s);
        if (!is_array($s)) {
            return;
        }

        $css = [];

        # Properties

        # Blog description
        $selectors = '#blogdesc';
        if (isset($s['subtitle_hidden'])) {
            dcThemeConfig::prop($css, $selectors, 'display', ($s['subtitle_hidden'] ? 'none' : null));
        }

        # Main font
        $selectors = 'body, .supranav li a span, #comments.me, a.comment-number';
        if (isset($s['body_font'])) {
            dcThemeConfig::prop($css, $selectors, 'font-family', self::fontDef($s['body_font']));
        }

        # Secondary font
        $selectors = '#blogdesc, .supranav, #content-info, #subcategories, #comments-feed, #sidebar h2, #sidebar h3, #footer';
        if (isset($s['alternate_font'])) {
            dcThemeConfig::prop($css, $selectors, 'font-family', self::fontDef($s['alternate_font']));
        }

        # Inside posts links font weight
        $selectors = '.post-excerpt a, .post-content a';
        if (isset($s['post_link_w'])) {
            dcThemeConfig::prop($css, $selectors, 'font-weight', ($s['post_link_w'] ? 'bold' : 'normal'));
        }

        # Inside posts links colors (normal, visited)
        $selectors = '.post-excerpt a:link, .post-excerpt a:visited, .post-content a:link, .post-content a:visited';
        if (isset($s['post_link_v_c'])) {
            dcThemeConfig::prop($css, $selectors, 'color', $s['post_link_v_c']);
        }

        # Inside posts links colors (hover, active, focus)
        $selectors = '.post-excerpt a:hover, .post-excerpt a:active, .post-excerpt a:focus, .post-content a:hover, .post-content a:active, .post-content a:focus';
        if (isset($s['post_link_f_c'])) {
            dcThemeConfig::prop($css, $selectors, 'color', $s['post_link_f_c']);
        }

        # Style directives
        $res = '';
        foreach ($css as $selector => $values) {
            $res .= $selector . " {\n";
            foreach ($values as $k => $v) {
                $res .= $k . ':' . $v . ";\n";
            }
            $res .= "}\n";
        }

        # Large screens
        $css_large = [];

        # Blog title font weight
        $selectors = 'h1, h1 a:link, h1 a:visited, h1 a:hover, h1 a:visited, h1 a:focus';
        if (isset($s['blog_title_w'])) {
            dcThemeConfig::prop($css_large, $selectors, 'font-weight', ($s['blog_title_w'] ? 'bold' : 'normal'));
        }

        # Blog title font size
        $selectors = 'h1';
        if (isset($s['blog_title_s'])) {
            dcThemeConfig::prop($css_large, $selectors, 'font-size', $s['blog_title_s']);
        }

        # Blog title color
        $selectors = 'h1 a:link, h1 a:visited, h1 a:hover, h1 a:visited, h1 a:focus';
        if (isset($s['blog_title_c'])) {
            dcThemeConfig::prop($css_large, $selectors, 'color', $s['blog_title_c']);
        }

        # Post title font weight
        $selectors = 'h2.post-title, h2.post-title a:link, h2.post-title a:visited, h2.post-title a:hover, h2.post-title a:visited, h2.post-title a:focus';
        if (isset($s['post_title_w'])) {
            dcThemeConfig::prop($css_large, $selectors, 'font-weight', ($s['post_title_w'] ? 'bold' : 'normal'));
        }

        # Post title font size
        $selectors = 'h2.post-title';
        if (isset($s['post_title_s'])) {
            dcThemeConfig::prop($css_large, $selectors, 'font-size', $s['post_title_s']);
        }

        # Post title color
        $selectors = 'h2.post-title a:link, h2.post-title a:visited, h2.post-title a:hover, h2.post-title a:visited, h2.post-title a:focus';
        if (isset($s['post_title_c'])) {
            dcThemeConfig::prop($css_large, $selectors, 'color', $s['post_title_c']);
        }

        # Simple title color (title without link)
        $selectors = '#content-info h2, .post-title, .post h3, .post h4, .post h5, .post h6, .arch-block h3';
        if (isset($s['post_simple_title_c'])) {
            dcThemeConfig::prop($css_large, $selectors, 'color', $s['post_simple_title_c']);
        }

        # Style directives for large screens
        if (count($css_large)) {
            $res .= '@media only screen and (min-width: 481px) {' . "\n";
            foreach ($css_large as $selector => $values) {
                $res .= $selector . " {\n";
                foreach ($values as $k => $v) {
                    $res .= $k . ':' . $v . ";\n";
                }
                $res .= "}\n";
            }
            $res .= "}\n";
        }

        # Small screens
        $css_small = [];

        # Blog title font weight
        $selectors = 'h1, h1 a:link, h1 a:visited, h1 a:hover, h1 a:visited, h1 a:focus';
        if (isset($s['blog_title_w_m'])) {
            dcThemeConfig::prop($css_small, $selectors, 'font-weight', ($s['blog_title_w_m'] ? 'bold' : 'normal'));
        }

        # Blog title font size
        $selectors = 'h1';
        if (isset($s['blog_title_s_m'])) {
            dcThemeConfig::prop($css_small, $selectors, 'font-size', $s['blog_title_s_m']);
        }

        # Blog title color
        $selectors = 'h1 a:link, h1 a:visited, h1 a:hover, h1 a:visited, h1 a:focus';
        if (isset($s['blog_title_c_m'])) {
            dcThemeConfig::prop($css_small, $selectors, 'color', $s['blog_title_c_m']);
        }

        # Post title font weight
        $selectors = 'h2.post-title, h2.post-title a:link, h2.post-title a:visited, h2.post-title a:hover, h2.post-title a:visited, h2.post-title a:focus';
        if (isset($s['post_title_w_m'])) {
            dcThemeConfig::prop($css_small, $selectors, 'font-weight', ($s['post_title_w_m'] ? 'bold' : 'normal'));
        }

        # Post title font size
        $selectors = 'h2.post-title';
        if (isset($s['post_title_s_m'])) {
            dcThemeConfig::prop($css_small, $selectors, 'font-size', $s['post_title_s_m']);
        }

        # Post title color
        $selectors = 'h2.post-title a:link, h2.post-title a:visited, h2.post-title a:hover, h2.post-title a:visited, h2.post-title a:focus';
        if (isset($s['post_title_c_m'])) {
            dcThemeConfig::prop($css_small, $selectors, 'color', $s['post_title_c_m']);
        }

        # Style directives for small screens
        if (count($css_small)) {
            $res .= '@media only screen and (max-width: 480px) {' . "\n";
            foreach ($css_small as $selector => $values) {
                $res .= $selector . " {\n";
                foreach ($values as $k => $v) {
                    $res .= $k . ':' . $v . ";\n";
                }
                $res .= "}\n";
            }
            $res .= "}\n";
        }

        return $res;
    }

    protected static $fonts = [
        // Theme standard
        'Ductile body'      => '"Century Schoolbook", "Century Schoolbook L", Georgia, serif',
        'Ductile alternate' => '"Franklin gothic medium", "arial narrow", "DejaVu Sans Condensed", "helvetica neue", helvetica, sans-serif',

        // Serif families
        'Times New Roman' => 'Cambria, "Hoefler Text", Utopia, "Liberation Serif", "Nimbus Roman No9 L Regular", Times, "Times New Roman", serif',
        'Georgia'         => 'Constantia, "Lucida Bright", Lucidabright, "Lucida Serif", Lucida, "DejaVu Serif", "Bitstream Vera Serif", "Liberation Serif", Georgia, serif',
        'Garamond'        => '"Palatino Linotype", Palatino, Palladio, "URW Palladio L", "Book Antiqua", Baskerville, "Bookman Old Style", "Bitstream Charter", "Nimbus Roman No9 L", Garamond, "Apple Garamond", "ITC Garamond Narrow", "New Century Schoolbook", "Century Schoolbook", "Century Schoolbook L", Georgia, serif',

        // Sans-serif families
        'Helvetica/Arial' => 'Frutiger, "Frutiger Linotype", Univers, Calibri, "Gill Sans", "Gill Sans MT", "Myriad Pro", Myriad, "DejaVu Sans Condensed", "Liberation Sans", "Nimbus Sans L", Tahoma, Geneva, "Helvetica Neue", Helvetica, Arial, sans-serif',
        'Verdana'         => 'Corbel, "Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", "DejaVu Sans", "Bitstream Vera Sans", "Liberation Sans", Verdana, "Verdana Ref", sans-serif',
        'Trebuchet MS'    => '"Segoe UI", Candara, "Bitstream Vera Sans", "DejaVu Sans", "Bitstream Vera Sans", "Trebuchet MS", Verdana, "Verdana Ref", sans-serif',

        // Cursive families
        'Impact' => 'Impact, Haettenschweiler, "Franklin Gothic Bold", Charcoal, "Helvetica Inserat", "Bitstream Vera Sans Bold", "Arial Black", sans-serif',

        // Monospace families
        'Monospace' => 'Consolas, "Andale Mono WT", "Andale Mono", "Lucida Console", "Lucida Sans Typewriter", "DejaVu Sans Mono", "Bitstream Vera Sans Mono", "Liberation Mono", "Nimbus Mono L", Monaco, "Courier New", Courier, monospace',
    ];

    protected static function fontDef($c)
    {
        return self::$fonts[$c] ?? null;
    }
}
