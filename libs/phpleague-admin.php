<?php

/*
 * This file is part of the PHPLeague package.
 *
 * (c) Maxime Dizerens <mdizerens@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if ( ! class_exists('PHPLeague_Admin')) {
    
    /**
     * Manage the rendering in the back-end.
     *
     * @category   Admin
     * @package    PHPLeague
     * @author     Maxime Dizerens
     * @copyright  (c) 2011 Mikaweb Design, Ltd
     */
    class PHPLeague_Admin {

        /**
         * Constructor
         *
         * @param  none
         * @return void
         */
        public function __construct() {}
        
        /**
         * Backend header
         *
         * @param  none
         * @return string
         */
        public function admin_header()
        {
            return '<div id="adminpanel"><div id="adminpanel-header"><div class="logo"><a href="'
                    .admin_url('admin.php?page=phpleague_overview').'"><img alt="" src="'
                    .plugins_url('phpleague/assets/img/logo.png').'" /></a></div><div class="theme-info">'
                    .'<span class="plugin">'.__('PHPLeague for WordPress', 'phpleague').'</span>'
                    .'<span class="release">'.__('Release: ', 'phpleague').WP_PHPLEAGUE_VERSION.'</span>'
                    .'</div></div><div id="support-links"><ul><li class="changelog">'
                    .'<a href="http://wordpress.org/extend/plugins/phpleague/changelog/">'
                    .__('Changelog', 'phpleague').'</a></li><li class="docs">'
                    .'<a href="http://www.phpleague.com/manual/">'.__('Manual', 'phpleague').'</a></li>'
                    .'<li class="help"><a href="http://www.phpleague.com/">'.__('Homepage', 'phpleague')
                    .'</a></li></ul></div><div id="adminpanel-main">';
        }

        /**
         * Page Container
         *
         * @param  array  $menu
         * @param  array  $content
         * @param  array  $notification
         * @return string
         */
        public function admin_container($menu = array(), $content = array(), $notification = array())
        {
            // Build the menu...
            $output = '<div id="adminpanel-menu"><ul>';

            foreach ($menu as $key => $value)
            {
                $output .= '<li class="adminpanel-menu-li">'
                        .'<a href="'.$value.'" class="adminpanel-menu-link" id="adminpanel-menu-'
                        .strtolower(str_replace(' ', '', $key)).'">'.$key.'</a></li>';
            }

            $output .= '</ul></div><div id="adminpanel-content">';
            
            // Show notifications...
            if ( ! empty($notification) && is_array($notification))
            {
                $output .= '<div class="updated">';
                foreach ($notification as $note)
                {
                    $output .= '<p>'.esc_html($note).'</p>';
                }
                $output .= '</div>';
            }
            
            // Build the content...
            foreach ($menu as $key => $item)
            {
                $output .= '<div class="adminpanel-content-box" id="adminpanel-content-'
                        .strtolower(str_replace(' ', '', $key)).'">';

                foreach ($content as $value)
                {
                    if ($key == $value['menu'])
                    {
                        $output .= '<div class="section"><h3 class="heading">'.$value['title'].'</h3>'
                                .'<div class="option"><div class="'.$value['class'].'">'
                                .$value['text'].'</div><div class="clear"></div></div></div>';
                    }
                }
                
                $output .= '</div>';
            }
            
            $output .= '</div>';

            return $output;
        }

        /**
         * Page wrapper
         *
         * @param  integer $width
         * @param  string  $content
         * @return string
         */
        public function admin_wrapper($width = 98, $content = NULL)
        {
            return '<div class="postbox-container" style="width: '.$width.'%">'.$content.'</div>';
        }
    
        /**
         * Backend footer
         *
         * @param  none
         * @return string
         */
        public function admin_footer()
        {
            return '<div class="clear"></div></div><div id="adminpanel-footer"></div></div>';
        }

        /**
         * Backend pages handler
         *
         * @param  none
         * @return string
         */
        public function admin_page()
        {
            // JS must be enabled to use properly PHPLeague...
            _e('<noscript>Javascript must be enabled, thank you.</noscript>', 'phpleague');
            
            // Page Header
            echo $this->admin_header();
            
            // Initialize libraries
            $db  = new PHPLeague_Database;
            $ctl = new PHPLeague_Admin;
            $fct = new PHPLeague_Tools;

            // Page Wrapper
            switch (trim($_GET['page']))
            {
                case 'phpleague_club' :
                    require_once WP_PHPLEAGUE_PATH.'inc/admin/club.php';
                    break;
                case 'phpleague_player' :
                    require_once WP_PHPLEAGUE_PATH.'inc/admin/player.php';
                    break;
                case 'phpleague_about' :
                    require_once WP_PHPLEAGUE_PATH.'inc/admin/about.php';
                    break;
                case 'phpleague_overview' :
                default :
                    require_once WP_PHPLEAGUE_PATH.'inc/admin/overview.php';
                    break;
            }
            
            // Page Footer
            echo $this->admin_footer();
        }

        /**
         * Add's new global menu, if $href is false menu is added
         * but registred as submenuable
         *
         * @return void
         */
        protected static function add_root_menu($name, $id, $href = FALSE)
        {
            global $wp_admin_bar;
            if ( ! is_super_admin() || ! is_admin_bar_showing())
              return;

            $wp_admin_bar->add_menu(array(
                'id'    => $id,
                'title' => $name,
                'href'  => $href
            ));
        }

        /**
         * Add's new submenu where additinal $meta specifies class,
         * id, target or onclick parameters
         *
         * @return void
         */
        protected static function add_sub_menu($name, $link, $parent, $id, $meta = FALSE)
        {
            global $wp_admin_bar;
            if ( ! is_super_admin() || ! is_admin_bar_showing())
                return;
            
            $wp_admin_bar->add_menu(array(
                'parent' => $parent,
                'title'  => $name,
                'href'   => $link,
                'meta'   => $meta,
                'id'     => $id
            ));
        }

        /**
         * Render the admin bar
         *
         * @return void
         */
        public static function render_adminbar_links()
        {
            // Root
            PHPLeague_Admin::add_root_menu(
                __('PHPLeague', 'phpleague'),
                'phpleague',
                admin_url('admin.php?page=phpleague_overview')
            );

            PHPLeague_Admin::add_sub_menu(
                __('Dashboard', 'phpleague'),
                admin_url('admin.php?page=phpleague_overview'),
                'phpleague',
                'phpleague-dashboard'
            );

            PHPLeague_Admin::add_sub_menu(
                __('Clubs', 'phpleague'),
                admin_url('admin.php?page=phpleague_club'),
                'phpleague',
                'phpleague-clubs'
            );
            
            PHPLeague_Admin::add_sub_menu(
                __('Players', 'phpleague'),
                admin_url('admin.php?page=phpleague_player'),
                'phpleague',
                'phpleague-players'
            );

            PHPLeague_Admin::add_sub_menu(
                __('About', 'phpleague'),
                admin_url('admin.php?page=phpleague_about'),
                'phpleague',
                'phpleague-about'
            );
        }

        /**
         * Add the admin styles
         *
         * @param  none
         * @return void
         */
        public static function print_admin_styles()
        {
            // Execute this only when we are on a PHPLeague page
            if (isset($_GET['page']))
            {
                // We quit if the current page isn't one of PHPLeague
                if ( ! in_array(trim($_GET['page']), PHPLeague::$pages))
                    return;

                wp_register_style('phpleague-backend', plugins_url('phpleague/assets/css/phpleague-admin.css'));
                wp_enqueue_style('phpleague-backend');
            }
        }
        
        /**
         * Add the admin scripts
         *
         * @param  none
         * @return void
         */
        public static function print_admin_scripts()
        {
            // Execute this only when we are on a PHPLeague page
            if (isset($_GET['page']))
            {
                // We quit if the current page isn't one of PHPLeague
                if ( ! in_array(trim($_GET['page']), PHPLeague::$pages))
                    return;
                
                // Make sure to use the latest version of jQuery...
                wp_deregister_script('jquery');
                wp_register_script('jquery', ('http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js'), FALSE, NULL, TRUE);
                wp_enqueue_script('jquery');

                wp_register_script('phpleague', plugins_url('phpleague/assets/js/admin.js'), array('jquery'));
                wp_register_script('phpleague-mask', plugins_url('phpleague/assets/js/jquery.maskedinput.js'), array('jquery'));
                wp_enqueue_script('phpleague-mask');
                wp_enqueue_script('phpleague');
            }
        }
        
        /**
         * Admin menu generation
         *
         * @param  none
         * @return void
         */
        public static function admin_menu()
        {
            $instance = new PHPLeague_Admin;
            $parent   = 'phpleague_overview';
            
            if (function_exists('add_menu_page'))
            {
                add_menu_page(
                    __('Dashboard (PHPLeague)', 'phpleague'),
                    __('PHPLeague', 'phpleague'),
                    PHPLeague::$access,
                    $parent,
                    array($instance, 'admin_page'),
                    plugins_url('phpleague/assets/img/league.png')
                );
            }
            
            if (function_exists('add_submenu_page'))
            {
                add_submenu_page(
                    $parent,
                    __('Dashboard (PHPLeague)', 'phpleague'),
                    __('Dashboard', 'phpleague'),
                    PHPLeague::$access,
                    $parent,
                    array($instance, 'admin_page')
                );

                add_submenu_page(
                    $parent,
                    __('Clubs (PHPLeague)', 'phpleague'),
                    __('Clubs', 'phpleague'),
                    PHPLeague::$access,
                    'phpleague_club',
                    array($instance, 'admin_page')
                );
                
                add_submenu_page(
                    $parent,
                    __('Players (PHPLeague)', 'phpleague'),
                    __('Players', 'phpleague'),
                    PHPLeague::$access,
                    'phpleague_player',
                    array($instance, 'admin_page')
                );
                
                add_submenu_page(
                    $parent,
                    __('About (PHPLeague)', 'phpleague'),
                    __('About', 'phpleague'),
                    PHPLeague::$access,
                    'phpleague_about',
                    array($instance, 'admin_page')
                );
            }
        }

        /**
         * Register admin widgets
         *
         * @param  none
         * @return void
         */
        public static function register_admin_widgets()
        {
            wp_add_dashboard_widget(
                'phpleague_dashboard',
                __('PHPLeague Latest News', 'phpleague'),
                array(
                    'PHPLeague_Widgets',
                    'latest_news'
                )
            );
        }

        /**
         * Add TinyMCE Button
         *
         * @param  none
         * @return void
         */
        public static function add_editor_button()
        {
            // Don't bother doing this stuff if the current user lacks permissions
            if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages')) return;

            // Check for PHPLeague capability
            if ( ! current_user_can('phpleague')) return;

            // Add only in Rich Editor mode
            if (get_user_option('rich_editing') == 'true')
            {
                add_filter('mce_external_plugins', array('PHPLeague_Admin', 'add_editor_plugin'));
                add_filter('mce_buttons', array('PHPLeague_Admin', 'register_editor_button'));
            }
        }
        
        /**
         * Add TinyMCE plugin
         *
         * @param  array $plugin_array
         * @return array
         */
        public function add_editor_plugin($plugin_array)
        {
            $plugin_array['PHPLeague'] = plugins_url('phpleague/assets/js/tinymce/editor_plugin.js');
            return $plugin_array;
        }
        
        /**
         * Register TinyMCE button
         *
         * @param  array $buttons
         * @return array
         */
        public function register_editor_button($buttons)
        {
            array_push($buttons, 'separator', 'PHPLeague');
            return $buttons;
        }
    }
}