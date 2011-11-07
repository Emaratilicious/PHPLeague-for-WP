<?php

/*
 * This file is part of the PHPLeague package.
 *
 * (c) Maxime Dizerens <mdizerens@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if ( ! class_exists('PHPLeague_Widgets')) {
    
    /**
     * Manage the PHPLeague widgets.
     *
     * @category   Widgets
     * @package    PHPLeague
     * @author     Maxime Dizerens
     * @copyright  (c) 2011 Mikaweb Design, Ltd
     */
    class PHPLeague_Widgets extends WP_Widget {

        /**
         * Constructor
         *
         * @param  none
         * @return void
         */
        public function __construct() {}
            
        /**
         * Show PHPLeague latest news...
         *
         * @return string
         */
        public static function dashboard()
        {
            echo '<div class="rss-widget">';

            wp_widget_rss_output(array(
                'url'          => 'http://www.phpleague.com/category/annoucements/feed/',
                'title'        => 'Latest News from PHPLeague...',
                'items'        => 5,
                'show_summary' => 0,
                'show_author'  => 0,
                'show_date'    => 1
            ));

           echo '</div>';
        }
        
        /**
         * Display Table
         *
         * @param  integer $id_league
         * @return string
         */
        public static function table()
        {
            
        }
    }
}