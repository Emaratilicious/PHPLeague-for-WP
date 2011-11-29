<?php

/*
 * This file is part of the PHPLeague package.
 *
 * (c) Maxime Dizerens <mdizerens@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if ( ! class_exists('PHPLeague_Sports_Football')) {
    
    /**
     * PHPLeague Sports (Football) library.
     *
     * @category   Sports_Football
     * @package    PHPLeague
     * @author     Maxime Dizerens
     * @copyright  (c) 2011 Mikaweb Design, Ltd
     */
    class PHPLeague_Sports_Football extends PHPLeague_Sports {

        // Player positions
        public static $positions = array(
            1  => 'Goalkeeper',
            2  => 'Left Back',
            3  => 'Centre Back',
            4  => 'Right Back',
            5  => 'Left Midfielder',
            6  => 'Centre Midfielder',
            7  => 'Defensive Midfielder',
            8  => 'Offensive Midfielder',
            9  => 'Right Midfielder',
            10 => 'Winger',
            11 => 'Second Striker',
            12 => 'Striker'
        );
  
        /**
         * Constructor
         *
         * @param  none
         * @return void
         */
        public function __construct()
        {
            parent::__construct();
        }
    }
}