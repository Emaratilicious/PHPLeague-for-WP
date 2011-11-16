<?php

/*
 * This file is part of the PHPLeague package.
 *
 * (c) Maxime Dizerens <mdizerens@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if ( ! class_exists('PHPLeague_Sports_Soccer')) {
    
    /**
     * PHPLeague Sports (Soccer) library.
     *
     * @category   Sports_Soccer
     * @package    PHPLeague
     * @author     Maxime Dizerens
     * @copyright  (c) 2011 Mikaweb Design, Ltd
     */
    class PHPLeague_Sports_Soccer extends PHPLeague_Sports {

        // Static vars
        public static $positions = array(
            1  => __('Goalkeeper', 'phpleague'),
            2  => __('Left Back', 'phpleague'),
            3  => __('Centre Back', 'phpleague'),
            4  => __('Right Back', 'phpleague'),
            5  => __('Left Midfielder', 'phpleague'),
            6  => __('Centre Midfielder', 'phpleague'),
            7  => __('Defensive Midfielder', 'phpleague'),
            8  => __('Offensive Midfielder', 'phpleague'),
            9  => __('Right Midfielder', 'phpleague'),
            10 => __('Winger', 'phpleague'),
            11 => __('Second Striker', 'phpleague'),
            11 => __('Striker', 'phpleague'),
        );

        public static $events = array(
            1  => __('Goals', 'phpleague'),
            2  => __('Left Back', 'phpleague'),
            3  => __('Centre Back', 'phpleague'),
            4  => __('Right Back', 'phpleague'),
            5  => __('Left Midfielder', 'phpleague'),
            6  => __('Centre Midfielder', 'phpleague'),
            7  => __('Defensive Midfielder', 'phpleague'),
            8  => __('Offensive Midfielder', 'phpleague'),
            9  => __('Right Midfielder', 'phpleague'),
            10 => __('Winger', 'phpleague'),
            11 => __('Second Striker', 'phpleague'),
            11 => __('Striker', 'phpleague'),
        );
  
        /**
         * Constructor
         *
         * @param  none
         * @return void
         */
        public function __construct() {}

        /**
         * Return all events
         *
         * @param  none
         * @return void
         */
        public function get_events()
        {
             
        }

        /**
         * Return all positions
         *
         * @param  none
         * @return void
         */
        public function get_positions()
        {
            
        }
    }
}