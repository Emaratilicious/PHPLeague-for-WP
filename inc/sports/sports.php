<?php

/*
 * This file is part of the PHPLeague package.
 *
 * (c) Maxime Dizerens <mdizerens@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if ( ! class_exists('PHPLeague_Sports')) {
    
    /**
     * PHPLeague Sports abstraction library.
     *
     * @category   Sports
     * @package    PHPLeague
     * @author     Maxime Dizerens
     * @copyright  (c) 2011 Mikaweb Design, Ltd
     */
  abstract class PHPLeague_Sports {
        
        // Player positions
        public static $positions = array();

        /**
         * Constructor
         *
         * @param  none
         * @return void
         */
        public function __construct() {}
    }
}