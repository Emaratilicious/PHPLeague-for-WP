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
            return '
            <div id="adminpanel">
                <div id="adminpanel-header">
                    <div class="logo"><a href="'.admin_url('admin.php?page=phpleague_overview').'"><img alt="PHPLeague logo" src="'.plugins_url('phpleague/assets/img/logo.png').'" /></a></div>
                    <div class="theme-info">
                        <span class="plugin">'.__('PHPLeague for WordPress', 'phpleague').'</span>
                        <span class="release">'.__('Release: ', 'phpleague').WP_PHPLEAGUE_VERSION.'</span>
                    </div>
                </div>
                <div id="support-links">
                    <ul>
                        <li class="changelog"><a href="http://wordpress.org/extend/plugins/phpleague/changelog/">'.__('Changelog', 'phpleague').'</a></li>
                        <li class="docs"><a href="http://www.phpleague.com/manual/">'.__('Manual', 'phpleague').'</a></li>
                        <li class="help"><a href="http://www.phpleague.com/">'.__('Homepage', 'phpleague').'</a></li>
                    </ul>
                </div>
                <div id="adminpanel-main">';
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

            foreach ($menu as $key => $value) {
                $output .= '
                <li class="adminpanel-menu-li">
                    <a href="'.$value.'" class="adminpanel-menu-link" id="adminpanel-menu-'.strtolower(str_replace(' ', '', $key)).'">
                        '.$key.'
                    </a>
                </li>';
            }

            $output .= '</ul></div><div id="adminpanel-content">';
            
            // Show notification here...
            if ( ! empty($notification) && is_array($notification)) {
                foreach ($notification as $note) {
                    $output .= '<div class="updated"><p>'.esc_html($note).'</p></div>';
                }
            }
            
            // Build the content...
            foreach ($menu as $key => $item) {
                $output .= '<div class="adminpanel-content-box" id="adminpanel-content-'.strtolower(str_replace(' ', '', $key)).'">';
                foreach ($content as $value) {
                    $hidden = (isset($value['hide']) && $value['hide'] === TRUE) ? 'hidden' : '';
                    if ($key == $value['menu']) {
                        $output .= '
                        <div class="section">
                            <h3 class="heading">'.$value['title'].'</h3>
                            <div class="option '.$hidden.'">
                                <div class="'.$value['class'].'">'.$value['text'].'</div>
                                <div class="clear"></div>
                            </div>
                        </div>';
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
            $output = '<div class="postbox-container" style="width: '.$width.'%">';
            $output .= $content;
            $output .= '</div>';

            return $output;
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
            _e('<noscript>For full functionality of PHPLeague, it is necessary to enable Javascript.</noscript>', 'phpleague');
            
            // Page Header
            echo $this->admin_header();
            
            // Initialize libraries
            $db  = new PHPLeague_Database();
            $ctl = new PHPLeague_Admin();
            $fct = new PHPLeague_Tools();

            // Page Wrapper
            switch (trim($_GET['page'])) {
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
    }
}