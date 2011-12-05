<?php

/*
 * This file is part of the PHPLeague package.
 *
 * (c) Maxime Dizerens <mdizerens@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if ( ! class_exists('PHPLeague_Widgets_Ranking')) {
    
    /**
     * Manage the PHPLeague widgets.
     *
     * @category   Widgets_Ranking
     * @package    PHPLeague
     * @author     Maxime Dizerens
     * @copyright  (c) 2011 Mikaweb Design, Ltd
     */
    class PHPLeague_Widgets_Ranking extends WP_Widget {

        /**
         * Constructor
         *
         * @param  none
         * @return void
         */
        public function __construct()
        {
            parent::WP_Widget(
                'phpleague_widget_ranking',
                'PHPLeague Ranking Table',
                array('description' => 'Display a PHPLeague Ranking Table...')
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
            // PHPLeague_Database
            $db = new PHPLeague_Database;

            // Extract arguments
            extract($args);

            // Get the league ID
            $league = ( ! empty($instance['league_id']) ? (int) $instance['league_id'] : 1);
            
            // Show what we want before the widget...
            echo $before_widget;

            // Widget title
            $title = $db->return_league_name($league);

            // Display title if not null
            if ($title)
                echo $before_title.$title.$after_title;

            // Display the ranking table
            if ($league) {
                $front = new PHPLeague_Front;
                echo $front->widget_ranking_table($league);
            }

            // Show what we want after the widget...
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
            $instance['league_id'] = strip_tags($new_instance['league_id']);
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
            // PHPLeague_Tools
            $tools = new PHPLeague_Tools;

            // Get all leagues in the database
            global $wpdb;
            $leagues = $wpdb->get_results(
                $wpdb->prepare("SELECT id, name, year FROM $wpdb->league ORDER BY year DESC, name ASC")
            );

            // Get leagues list
            $leagues_list = array();
            foreach ($leagues as $item) {
                $year = (int) $item->year;
                $leagues_list[$item->name][$item->id] = $year.'/'.substr($year + 1, 2);
            }

            // Display the dropdown list with selected league
            echo $tools->select(
                esc_attr($this->get_field_name('league_id')),
                $leagues_list,
                (int) $instance['league_id'],
                array('id' => esc_attr($this->get_field_id('league_id')))
            );
        }
    }
}