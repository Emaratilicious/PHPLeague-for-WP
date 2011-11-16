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
        public function __construct()
        {
            parent::WP_Widget(
                'phpleague_widget',
                'PHPLeague Table',
                array('description' => 'Display a ranking table...')
            );
        }

        /**
         * Widget method
         *
         * @param  mixed $args
         * @param  mixed $instance
         * @return void
         */
        public function widget($args, $instance)
        {
            extract($args);

            $select = $instance['select'];
            
            echo $before_widget;
            if ($select)
                echo $before_title.$title.$after_title;
            echo $after_widget;
        }

        /**
         * Update method
         *
         * @param  mixed $new_instance
         * @param  mixed $old_instance
         * @return void
         */
        public function update($new_instance, $old_instance)
        {
            $instance = $old_instance;
            $instance['select'] = strip_tags($new_instance['select']);
            return $instance;
        }

        /**
         * Form method
         *
         * @param  mixed $instance
         * @return void
         */
        public function form($instance)
        {
            global $wpdb;

            //$select  = esc_attr($instance['select']);
            $leagues = $wpdb->get_results($wpdb->prepare("SELECT `id`, `name`, `year` FROM {$wpdb->league} ORDER BY `year` DESC, `name` ASC"));

            echo '<select id="league_id" name="league_id">';
            if ($leagues) {
                foreach($leagues as $league) {
                    $year  = intval($league->year);
                    echo '<option value="'.$league->id.'" >'.esc_html($league->name).' '.$year.'/'.substr($year + 1, 2).'</option>'."\n";
                }
            }
            echo '</select>';
        }
            
        /**
         * Get PHPLeague latest news...
         *
         * @return string
         */
        public static function dashboard()
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
        
        /**
         * Display Table
         *
         * @param  integer $id_league
         * @return string
         */
        public static function table($id_league)
        {
            
        }
    }
}