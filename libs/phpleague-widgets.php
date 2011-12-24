<?php

/*
 * This file is part of the PHPLeague package.
 *
 * (c) M. Dizerens <mikaweb@gunners.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if ( ! class_exists('PHPLeague_Widgets')) {
    
    /**
     * Manage the widgets in the dashboard area.
     *
     * @category   Widgets
     * @package    PHPLeague
     * @author     Maxime Dizerens
     * @copyright  (c) 2011 Mikaweb Design, Ltd
     */
    class PHPLeague_Widgets {
            
        /**
         * Get latest news from PHPLeague.com
         *
         * @param  none
         * @return string
         */
        public static function latest_news()
        {
            echo '<div class="rss-widget">';

            wp_widget_rss_output(array(
                'url'          => 'http://www.phpleague.com/category/annoucements/feed/',
                'title'        => __('Latest News from PHPLeague...', 'phpleague'),
                'items'        => 5,
                'show_summary' => 0,
                'show_author'  => 0,
                'show_date'    => 1
            ));

           echo '</div>';
        }
    }
}

// Ranking Widget
require_once WP_PHPLEAGUE_PATH.'inc/widgets/ranking.php';

// Register each widget
function phpleague_register_widgets()
{
    register_widget('PHPLeague_Widgets_Ranking');
}

// Add all widgets in the WP process
add_action('widgets_init', 'phpleague_register_widgets');