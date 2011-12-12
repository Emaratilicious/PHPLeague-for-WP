<?php

/*
 * This file is part of the PHPLeague package.
 *
 * (c) Maxime Dizerens <mdizerens@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if ( ! class_exists('PHPLeague_AJAX')) {
    
    /**
     * AJAX library
     *
     * @category   AJAX
     * @package    PHPLeague
     * @author     Maxime Dizerens
     * @copyright  (c) 2011 Mikaweb Design, Ltd
     */
    class PHPLeague_AJAX {

        /**
         * Constructor
         *
         * @param  none
         * @return void
         */
        public function __construct() {}
        
        /**
         * Delete team in player's history
         *
         * @param  none
         * @return string
         */
        public function delete_player_history_team()
        {
            global $wpdb;
            $db = new PHPLeague_Database;
            
            $id_player_team = (int) $_POST['id_player_team'];
            $db->delete_player_history_team($id_player_team);
            $db->delete_player_team_data($id_player_team);

            _e('Team deleted successfully from the player history with all the data associated.', 'phpleague');
            die();
        }
    }
}