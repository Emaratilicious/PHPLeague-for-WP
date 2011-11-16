<?php

/*  
    Copyright 2011  Maxime Dizerens  (email : mdizerens@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/*
Plugin Name: PHPLeague for WordPress
Plugin URI: http://www.phpleague.com/
Description: PHPLeague for WordPress is the best companion to manage your championships.
Version: 1.4.0
Author: Maxime Dizerens
Author URI: http://www.phpleague.com/
*/

if ( ! class_exists('PHPLeague')) {
    
    /**
     * PHPLeague library.
     *
     * @package    PHPLeague
     * @author     Maxime Dizerens
     * @copyright  (c) 2011 Mikaweb Design, Ltd
     */
    class PHPLeague {

        // Vars
        public $longname  = 'PHPLeague for WordPress';
        public $shortname = 'PHPLeague for WP';
        public $homepage  = 'http://www.phpleague.com/';
        public $feed      = 'http://www.phpleague.com/feed/';
        public $edition   = 'Ultimate Edition';
        public $access    = '';
        public $pages     = array(
            'phpleague_about',
            'phpleague_club',
            'phpleague_overview',
            'phpleague_player',
            'phpleague_setting',
        );

        /**
         * Constructor
         *
         * @param  none
         * @return void
         */
        public function __construct()
        {
            // Load the constants
            $this->define_constants();
            
            // PHPLeague Translation
            load_plugin_textdomain('phpleague', FALSE, 'phpleague/i18n');

            // Core files
            require_once WP_PHPLEAGUE_PATH.'libs/phpleague-tools.php';
            require_once WP_PHPLEAGUE_PATH.'libs/phpleague-database.php';
            require_once WP_PHPLEAGUE_PATH.'libs/phpleague-widgets.php';
            
            // Load our tables
            $this->define_tables();
            
            if (is_admin()) {
                // Specific actions
                register_activation_hook(__FILE__, array('PHPLeague', 'activate'));
                register_uninstall_hook(__FILE__, array('PHPLeague', 'uninstall'));
                
                // We need to be administrator to manage PHPLeague backend
                $this->access = 'administrator';
                
                // Load the backend controller system
                require_once WP_PHPLEAGUE_PATH.'libs/phpleague-admin.php';
                
                add_action('init', array(&$this, 'add_editor_button'));
                add_action('admin_init', array(&$this, 'plugin_admin_init'));
                add_action('admin_init', array(&$this, 'plugin_check_upgrade'));
                add_action('admin_menu', array(&$this, 'admin_menu'));
                add_action('admin_print_styles', array(&$this, 'print_admin_styles'));
                add_action('admin_print_scripts', array(&$this, 'print_admin_scripts'));
                add_action('wp_dashboard_setup', array(&$this, 'register_widgets'));
                
                // AJAX library
                require_once WP_PHPLEAGUE_PATH.'libs/phpleague-ajax.php';
                
                // Ajax request
                add_action('wp_ajax_delete_player_history_team', array('PHPLeague_AJAX', 'delete_player_history_team'));

                // Register PHPLeague_Widgets
                //add_action('widgets_init', create_function('', 'register_widget("PHPLeague_Widgets");'));
            } else {
                // Load the frontend controller system
                require_once WP_PHPLEAGUE_PATH.'libs/phpleague-front.php';
                
                add_action('wp_print_styles', array(&$this, 'print_front_styles'));
                add_shortcode('phpleague', array(&$this, 'display_phpleague'));
            }
        }
        
        /**
         * Define constants
         *
         * @param  none
         * @return void
         */
        public function define_constants()
        {
            define('WP_PHPLEAGUE_VERSION', '1.4.0');
            define('WP_PHPLEAGUE_DB_VERSION', '1.3.0');
            define('WP_PHPLEAGUE_EDITION', $this->edition);
            define('WP_PHPLEAGUE_PATH', plugin_dir_path(__FILE__));
            define('WP_PHPLEAGUE_UPLOADS_PATH', ABSPATH.'wp-content/uploads/phpleague/');
        }
        
        // -- Backend methods -- //
        
        /**
         * Admin initializer
         *
         * @param  none
         * @return void
         */
        public function plugin_admin_init()
        {
            // Just after the activation, users are automatically redirected to PHPLeague...
            if (get_option('phpleague_do_activation_redirect', FALSE)) {
                delete_option('phpleague_do_activation_redirect');
                wp_redirect(get_option('siteurl').'/wp-admin/admin.php?page=phpleague_overview&activation=1');
                exit();
            }

            // Set capabilities
            $role = get_role($this->access);
            $role->add_cap('manage_phpleague');
            $role->add_cap('phpleague');

            // Give everyone access to the editor button
            $role = get_role('editor');
            $role->add_cap('phpleague');
            
            // Make sure using this only on PHPLeague pages
            if (isset($_GET['page']) && in_array(trim($_GET['page']), $this->pages)) {
                if ( ! current_user_can('manage_phpleague'))
                    wp_die(__('Permission insufficient to run PHPLeague!', 'phpleague'));
            }
        }
        
        /**
         * Add all widgets here...
         *
         * @param  none
         * @return void
         */
        public function register_widgets()
        {
             wp_add_dashboard_widget('phpleague_dashboard', 'PHPLeague Latest News', array('PHPLeague_Widgets', 'dashboard'));
        }
        
        /**
         * Plugin upgrade handler
         *
         * @param  none
         * @return void
         */
        public function plugin_check_upgrade()
        {
            global $wpdb;
            
            // Get the data from the database
            $version = get_option('phpleague_version');
            $current_version = isset($version) ? $version : 0;
            
            $db_version = get_option('phpleague_db_version');
            $current_db_version = isset($db_version) ? $db_version : 0;

            // You're already using the latest version
            if ($current_version == WP_PHPLEAGUE_VERSION || $current_version == 0 || $current_db_version == 0)
                return;

            // Some people encounter issues because they could not deal
            // with a NOT DEFAULT without any value
            if ($current_db_version < '1.2') {
                // ALTER tables
                $wpdb->query("ALTER TABLE $wpdb->country MODIFY name VARCHAR(100) DEFAULT NULL;");
                $wpdb->query("ALTER TABLE $wpdb->club MODIFY venue VARCHAR(100) DEFAULT NULL;");
                $wpdb->query("ALTER TABLE $wpdb->club MODIFY coach VARCHAR(100) DEFAULT NULL;");
                $wpdb->query("ALTER TABLE $wpdb->club MODIFY logo_big VARCHAR(255) DEFAULT NULL;");
                $wpdb->query("ALTER TABLE $wpdb->club MODIFY logo_mini VARCHAR(255) DEFAULT NULL;");
            }
            
            // We add the 4 UK members
            if ($current_db_version < '1.2.1') {
                $countries = array(
                    238 => 'England',
                    239 => 'Wales',
                    240 => 'Northern Ireland',
                    241 => 'Scotland'
                );

                // Update the country table with all of the above
                foreach ($countries as $key => $country) {
                    $wpdb->query("INSERT INTO $wpdb->country VALUES($key, '".$country."');");
                }
                
                // Penalty is not unsigned anymore
                $wpdb->query("ALTER TABLE $wpdb->team MODIFY penalty TINYINT(1) NOT NULL DEFAULT '0';");
            }
            
            // Drop constraints
            if ($current_db_version < '1.2.2') {
                // ALTER tables
                $wpdb->query("ALTER TABLE $wpdb->fixture DROP FOREIGN KEY phpleague_fixture_ibfk_1;");
                $wpdb->query("ALTER TABLE $wpdb->match DROP FOREIGN KEY phpleague_match_ibfk_1;");
                $wpdb->query("ALTER TABLE $wpdb->table_cache DROP FOREIGN KEY phpleague_table_cache_ibfk_1;");          
                $wpdb->query("ALTER TABLE $wpdb->team DROP FOREIGN KEY phpleague_team_ibfk_1;");
            }
            
            // Few modifications
            if ($current_db_version < '1.2.3') {
                // ALTER tables
                $wpdb->query("ALTER TABLE $wpdb->league MODIFY id_favorite smallint(4) unsigned NOT NULL DEFAULT '0';");
                $wpdb->query("ALTER TABLE $wpdb->club ADD creation varchar(4) NOT NULL DEFAULT '0000' AFTER coach;");
                $wpdb->query("ALTER TABLE $wpdb->club ADD website varchar(255) DEFAULT NULL AFTER creation;");
                $wpdb->query("ALTER TABLE $wpdb->league ADD team_link enum('no','yes') NOT NULL DEFAULT 'no' AFTER nb_leg;");
                $wpdb->query("ALTER TABLE $wpdb->league ADD default_time time NOT NULL DEFAULT '17:00:00' AFTER team_link;");
                
                // Set the edition name in the database...
                add_option('phpleague_edition', WP_PHPLEAGUE_EDITION);
            }
            
            // Few modifications
            if ($current_db_version < '1.3.0') {
                // Not required anymore...
                delete_option('phpleague_edition');

                // Add new fields and table here...
            }

            if ($current_version < WP_PHPLEAGUE_VERSION) {
                // Do stuff in order to upgrade PHPLeague
                update_option('phpleague_version', WP_PHPLEAGUE_VERSION);
                update_option('phpleague_db_version', WP_PHPLEAGUE_DB_VERSION);
            }
        }
        
        /**
         * Define every table
         *
         * @param  none
         * @return void
         */
        public function define_tables()
        {       
            global $wpdb;

            // All Tables
            $wpdb->club        = $wpdb->prefix.'phpleague_club';
            $wpdb->country     = $wpdb->prefix.'phpleague_country';
            $wpdb->fixture     = $wpdb->prefix.'phpleague_fixture';
            $wpdb->league      = $wpdb->prefix.'phpleague_league';
            $wpdb->match       = $wpdb->prefix.'phpleague_match';
            $wpdb->table_cache = $wpdb->prefix.'phpleague_table_cache';
            $wpdb->team        = $wpdb->prefix.'phpleague_team';
            $wpdb->player      = $wpdb->prefix.'phpleague_player';
            $wpdb->player_team = $wpdb->prefix.'phpleague_player_team';
            $wpdb->player_data = $wpdb->prefix.'phpleague_player_data';
            $wpdb->table_chart = $wpdb->prefix.'phpleague_table_chart';
            $wpdb->table_predi = $wpdb->prefix.'phpleague_table_prediction';
        }
        
        /**
         * Create or update the tables
         *
         * @param   string   table name
         * @param   string   sql
         * @param   integer  db version
         * @return  object
         */
        public static function run_install_or_upgrade($table_name, $sql, $db_version)
        {
            global $wpdb;
            
            // Table does not exist, creates it!
            // We use InnoDB AND UTF-8
            if ($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name) {
                $create = "CREATE TABLE ".$table_name." ( ".$sql." ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;";
                require_once ABSPATH.'wp-admin/includes/upgrade.php';
                dbDelta($create);
            }
        }
        
        /**
         * Plugin activation method
         *
         * @param  none
         * @return void
         */
        public static function activate()
        {
            global $wpdb;
            $db_version = WP_PHPLEAGUE_DB_VERSION;
                        
            // Club table
            $sql = "id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL DEFAULT '',
                id_country smallint(4) unsigned NOT NULL,
                venue VARCHAR(100) DEFAULT NULL,
                coach VARCHAR(100) DEFAULT NULL,
                logo_big VARCHAR(255) DEFAULT NULL,
                logo_mini VARCHAR(255) DEFAULT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY name (name)";

            PHPLeague::run_install_or_upgrade($wpdb->club, $sql, $db_version);
            
            // Country table
            $sql = "id tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
                name VARCHAR(100) DEFAULT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY name (name)";

            PHPLeague::run_install_or_upgrade($wpdb->country, $sql, $db_version);
            
            // Fixture table
            $sql = "number tinyint(3) unsigned NOT NULL DEFAULT '0',
                scheduled date NOT NULL DEFAULT '0000-00-00',
                id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
                id_league smallint(5) unsigned NOT NULL DEFAULT '0',
                PRIMARY KEY (id),
                KEY id_league (id_league)";

            PHPLeague::run_install_or_upgrade($wpdb->fixture, $sql, $db_version);
            
            // League table
            $sql = "id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
                name VARCHAR(100) NOT NULL DEFAULT '',
                year year(4) NOT NULL,
                pt_victory tinyint(3) unsigned NOT NULL DEFAULT '3',
                pt_draw tinyint(3) unsigned NOT NULL DEFAULT '1',
                pt_defeat tinyint(3) unsigned NOT NULL DEFAULT '0',
                promotion tinyint(3) unsigned NOT NULL DEFAULT '4',
                qualifying tinyint(3) unsigned NOT NULL DEFAULT '2',
                relegation tinyint(3) unsigned NOT NULL DEFAULT '3',
                id_favorite smallint(4) unsigned NOT NULL DEFAULT '0',
                nb_leg tinyint(1) NOT NULL DEFAULT '2',
                nb_teams tinyint(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (id)";

            PHPLeague::run_install_or_upgrade($wpdb->league, $sql, $db_version);
            
            // Match table
            $sql = "id mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
                id_team_home smallint(5) unsigned DEFAULT NULL,
                id_team_away smallint(5) unsigned DEFAULT NULL,
                played datetime DEFAULT NULL,
                id_fixture smallint(5) unsigned DEFAULT NULL,
                goal_home tinyint(1) unsigned DEFAULT NULL,
                goal_away tinyint(1) unsigned DEFAULT NULL,
                PRIMARY KEY (id),
                KEY id_fixture (id_fixture),
                KEY id_team_away (id_team_away),
                KEY id_team_home (id_team_home)";

            PHPLeague::run_install_or_upgrade($wpdb->match, $sql, $db_version);
            
            // Table Cache table
            $sql = "club_name VARCHAR(255) DEFAULT NULL,
                points smallint(4) unsigned DEFAULT NULL,
                played tinyint(3) unsigned DEFAULT NULL,
                victory tinyint(3) unsigned DEFAULT NULL,
                draw tinyint(3) unsigned DEFAULT NULL,
                defeat tinyint(3) unsigned DEFAULT NULL,
                goal_for smallint(4) unsigned DEFAULT NULL,
                goal_against smallint(4) unsigned DEFAULT NULL,
                diff smallint(4) DEFAULT NULL,
                pen tinyint(2) DEFAULT NULL,
                home_points smallint(4) unsigned DEFAULT NULL,
                home_played tinyint(3) unsigned DEFAULT NULL,
                home_v tinyint(3) unsigned DEFAULT NULL,
                home_d tinyint(3) unsigned DEFAULT NULL,
                home_l tinyint(3) unsigned DEFAULT NULL,
                home_g_for smallint(4) unsigned DEFAULT NULL,
                home_g_against smallint(4) unsigned DEFAULT NULL,
                home_diff smallint(4) DEFAULT NULL,
                away_points smallint(4) unsigned DEFAULT NULL,
                away_played tinyint(3) unsigned DEFAULT NULL,
                away_v tinyint(3) unsigned DEFAULT NULL,
                away_d tinyint(3) unsigned DEFAULT NULL,
                away_l tinyint(3) unsigned DEFAULT NULL,
                away_g_for smallint(4) unsigned DEFAULT NULL,
                away_g_against smallint(4) unsigned DEFAULT NULL,
                away_diff tinyint(4) DEFAULT NULL,
                id_team smallint(5) unsigned NOT NULL DEFAULT '0',
                id_league smallint(5) unsigned NOT NULL DEFAULT '0',
                KEY id_league (id_league)";

            PHPLeague::run_install_or_upgrade($wpdb->table_cache, $sql, $db_version);
            
            // Team table
            $sql = "id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
                id_league smallint(5) unsigned NOT NULL DEFAULT '0',
                id_club smallint(5) unsigned NOT NULL DEFAULT '0',
                penalty tinyint(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (id),
                KEY id_club (id_club),
                KEY id_league (id_league)";

            PHPLeague::run_install_or_upgrade($wpdb->team, $sql, $db_version);
                        
            // Countries listing
            $countries = array(
                1 => 'Afghanistan',
                2 => 'Zaire',
                3 => 'Albania',
                4 => 'Algeria',
                5 => 'American&nbsp;Samoa',
                6 => 'Andorra',
                7 => 'Angola',
                8 => 'Anguilla',
                9 => 'Antarctica',
                10 => 'Antigua&nbsp;and&nbsp;Barbuda',
                11 => 'Argentina',
                12 => 'Armenia',
                13 => 'Aruba',
                14 => 'Australia',
                15 => 'Austria',
                16 => 'Azerbaijan',
                17 => 'Bahamas',
                18 => 'Bahrain',
                19 => 'Bangladesh',
                20 => 'Barbados',
                21 => 'Belarus',
                22 => 'Belgium',
                23 => 'Belize',
                24 => 'Benin',
                25 => 'Bermuda',
                26 => 'Bhutan',
                27 => 'Bolivia',
                28 => 'Bosnia&nbsp;and&nbsp;Herzegovina',
                29 => 'Botswana',
                30 => 'Bouvet&nbsp;Island',
                31 => 'Brazil',
                32 => 'British&nbsp;Indian&nbsp;Ocean&nbsp;territory',
                33 => 'Brunei&nbsp;Darussalam',
                34 => 'Bulgaria',
                35 => 'Burkina&nbsp;Faso',
                36 => 'Burundi',
                37 => 'Cambodia',
                38 => 'Cameroon',
                39 => 'Canada',
                40 => 'Cape&nbsp;Verde',
                41 => 'Cayman&nbsp;Islands',
                42 => 'Central&nbsp;African&nbsp;Republic',
                43 => 'Chad',
                44 => 'Chile',
                45 => 'China',
                46 => 'Christmas&nbsp;Island',
                47 => 'Cocos&nbsp;Keeling)&nbsp;Islands',
                48 => 'Colombia',
                49 => 'Comoros',
                50 => 'Congo',
                51 => 'Zambia',
                52 => 'Zimbabwe',
                53 => 'Cook&nbsp;Islands',
                54 => 'Costa&nbsp;Rica',
                55 => 'Ivory Coast',
                56 => 'Croatia',
                57 => 'Cuba',
                58 => 'Cyprus',
                59 => 'Czech&nbsp;Republic',
                60 => 'Denmark',
                61 => 'Djibouti',
                62 => 'Dominica',
                63 => 'Dominican&nbsp;Republic',
                64 => 'East&nbsp;Timor',
                65 => 'Ecuador',
                66 => 'Egypt',
                67 => 'El&nbsp;Salvador',
                68 => 'Equatorial&nbsp;Guinea',
                69 => 'Eritrea',
                70 => 'Estonia',
                71 => 'Ethiopia',
                72 => 'Falkland&nbsp;Islands',
                73 => 'Faroe&nbsp;Islands',
                74 => 'Fiji',
                75 => 'Finland',
                76 => 'France',
                77 => 'French&nbsp;Guiana',
                78 => 'French&nbsp;Polynesia',
                79 => 'French&nbsp;Southern&nbsp;Territories',
                80 => 'Gabon',
                81 => 'Gambia',
                82 => 'Georgia',
                83 => 'Germany',
                84 => 'Ghana',
                85 => 'Gibraltar',
                86 => 'Greece',
                87 => 'Greenland',
                88 => 'Grenada',
                89 => 'Guadeloupe',
                90 => 'Guam',
                91 => 'Guatemala',
                92 => 'Guinea',
                93 => 'Guinea-Bissau',
                94 => 'Guyana',
                95 => 'Haiti',
                96 => 'Heard&nbsp;and&nbsp;McDonald&nbsp;Islands',
                97 => 'Honduras',
                98 => 'Hong&nbsp;Kong',
                99 => 'Hungary',
                100 => 'Iceland',
                101 => 'India',
                102 => 'Indonesia',
                103 => 'Iran',
                104 => 'Iraq',
                105 => 'Ireland',
                106 => 'Israel',
                107 => 'Italy',
                108 => 'Jamaica',
                109 => 'Japan',
                110 => 'Jordan',
                111 => 'Kazakhstan',
                112 => 'Kenya',
                113 => 'Kiribati',
                114 => 'North Korea',
                115 => 'South Korea',
                116 => 'Kuwait',
                117 => 'Kyrgyzstan',
                118 => 'Laos',
                119 => 'Latvia',
                120 => 'Lebanon',
                121 => 'Lesotho',
                122 => 'Liberia',
                123 => 'Libyan&nbsp;Arab&nbsp;Jamahiriya',
                124 => 'Liechtenstein',
                125 => 'Lithuania',
                126 => 'Luxembourg',
                127 => 'Macao',
                128 => 'Macedonia',
                129 => 'Madagascar',
                130 => 'Malawi',
                131 => 'Malaysia',
                132 => 'Maldives',
                133 => 'Mali',
                134 => 'Malta',
                135 => 'Marshall&nbsp;Islands',
                136 => 'Martinique',
                137 => 'Mauritania',
                138 => 'Mauritius',
                139 => 'Mayotte',
                140 => 'Mexico',
                141 => 'Micronesia',
                142 => 'Moldova',
                143 => 'Monaco',
                144 => 'Mongolia',
                145 => 'Montserrat',
                146 => 'Morocco',
                147 => 'Mozambique',
                148 => 'Myanmar',
                149 => 'Namibia',
                150 => 'Nauru',
                151 => 'Nepal',
                152 => 'Netherlands',
                153 => 'Netherlands&nbsp;Antilles',
                154 => 'New&nbsp;Caledonia',
                155 => 'New&nbsp;Zealand',
                156 => 'Nicaragua',
                157 => 'Niger',
                158 => 'Nigeria',
                159 => 'Niue',
                160 => 'Norfolk&nbsp;Island',
                161 => 'Northern&nbsp;Mariana&nbsp;Islands',
                162 => 'Norway',
                163 => 'Oman',
                164 => 'Pakistan',
                165 => 'Palau',
                166 => 'Palestinian&nbsp;Territories',
                167 => 'Panama',
                168 => 'Papua&nbsp;New&nbsp;Guinea',
                169 => 'Paraguay',
                170 => 'Peru',
                171 => 'Philippines',
                172 => 'Pitcairn',
                173 => 'Poland',
                174 => 'Portugal',
                175 => 'Puerto&nbsp;Rico',
                176 => 'Qatar',
                177 => 'R&eacute;union',
                178 => 'Romania',
                179 => 'Russia',
                180 => 'Rwanda',
                181 => 'Saint&nbsp;Helena',
                182 => 'Saint&nbsp;Kitts&nbsp;and&nbsp;Nevis',
                183 => 'Saint&nbsp;Lucia',
                184 => 'Saint&nbsp;Pierre&nbsp;and&nbsp;Miquelon',
                185 => 'Saint&nbsp;Vincent&nbsp;and&nbsp;the&nbsp;Grenadines',
                186 => 'Samoa',
                187 => 'San&nbsp;Marino',
                188 => 'Sao&nbsp;Tome&nbsp;and&nbsp;Principe',
                189 => 'Saudi&nbsp;Arabia',
                190 => 'Senegal',
                191 => 'Serbia&nbsp;and&nbsp;Montenegro',
                192 => 'Seychelles',
                193 => 'Sierra&nbsp;Leone',
                194 => 'Singapore',
                195 => 'Slovakia',
                196 => 'Slovenia',
                197 => 'Solomon&nbsp;Islands',
                198 => 'Somalia',
                199 => 'South&nbsp;Africa',
                200 => 'Yemen',
                201 => 'Spain',
                202 => 'Sri&nbsp;Lanka',
                203 => 'Sudan',
                204 => 'Suriname',
                205 => 'Svalbard&nbsp;and&nbsp;Jan&nbsp;Mayen&nbsp;Islands',
                206 => 'Swaziland',
                207 => 'Sweden',
                208 => 'Switzerland',
                209 => 'Syria',
                210 => 'Taiwan',
                211 => 'Tajikistan',
                212 => 'Tanzania',
                213 => 'Thailand',
                214 => 'Togo',
                215 => 'Tokelau',
                216 => 'Tonga',
                217 => 'Trinidad&nbsp;and&nbsp;Tobago',
                218 => 'Tunisia',
                219 => 'Turkey',
                220 => 'Turkmenistan',
                221 => 'Turks&nbsp;and&nbsp;Caicos&nbsp;Islands',
                222 => 'Tuvalu',
                223 => 'Uganda',
                224 => 'Ukraine',
                225 => 'United&nbsp;Arab&nbsp;Emirates',
                226 => 'United&nbsp;Kingdom',
                227 => 'United&nbsp;States&nbsp;of&nbsp;America',
                228 => 'Uruguay',
                229 => 'Uzbekistan',
                230 => 'Vanuatu',
                231 => 'Vatican&nbsp;City',
                232 => 'Venezuela',
                233 => 'Vietnam',
                234 => 'Virgin&nbsp;Islands&nbsp;British',
                235 => 'Virgin&nbsp;Islands&nbsp;US',
                236 => 'Wallis&nbsp;and&nbsp;Futuna&nbsp;Islands',
                237 => 'Western&nbsp;Sahara',
                238 => 'England',
                239 => 'Wales',
                240 => 'Northern Ireland',
                241 => 'Scotland'
            );
            
            // Add every country in the database
            foreach ($countries as $key => $country) {
                $wpdb->query("INSERT INTO $wpdb->country VALUES($key, '".$country."');");
            }
            
            // Create the PHPLeague directory and sub-directories
            PHPLeague_Tools::manage_directory(WP_PHPLEAGUE_UPLOADS_PATH, 'create');
            PHPLeague_Tools::manage_directory(WP_PHPLEAGUE_UPLOADS_PATH.'logo_big/', 'create');
            PHPLeague_Tools::manage_directory(WP_PHPLEAGUE_UPLOADS_PATH.'logo_mini/', 'create');
            PHPLeague_Tools::manage_directory(WP_PHPLEAGUE_UPLOADS_PATH.'players/', 'create');
            
            // Save versions
            add_option('phpleague_version', WP_PHPLEAGUE_VERSION);
            add_option('phpleague_db_version', $db_version);
            add_option('phpleague_do_activation_redirect', TRUE);
        }

        /**
         * Uninstall plugin method
         *
         * @param  none
         * @return void
         */
        public static function uninstall()
        {
            global $wpdb;

            $tables = array(
                $wpdb->fixture,
                $wpdb->league,
                $wpdb->club,
                $wpdb->country,
                $wpdb->match,
                $wpdb->table_cache,
                $wpdb->team
            );
            
            foreach ($tables as $table) {
                $wpdb->query('DROP TABLE IF EXISTS '.$table.';');
            }

            // Delete the version in the options table
            delete_option('phpleague_version');
            delete_option('phpleague_db_version');
            
            // Delete the PHPLeague directory and sub-directories
            PHPLeague_Tools::manage_directory(WP_PHPLEAGUE_UPLOADS_PATH, 'delete');
            PHPLeague_Tools::manage_directory(WP_PHPLEAGUE_UPLOADS_PATH.'logo_big/', 'delete');
            PHPLeague_Tools::manage_directory(WP_PHPLEAGUE_UPLOADS_PATH.'logo_mini/', 'delete');
            PHPLeague_Tools::manage_directory(WP_PHPLEAGUE_UPLOADS_PATH.'players/', 'delete');
        }
        
        /**
         * Add the admin styles
         *
         * @param  none
         * @return void
         */
        public function print_admin_styles()
        {
            if (isset($_GET['page'])) {
                if ( ! in_array(trim($_GET['page']), $this->pages))
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
        public function print_admin_scripts()
        {
            if (isset($_GET['page'])) {
                if ( ! in_array(trim($_GET['page']), $this->pages))
                    return;

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
        public function admin_menu()
        {
            $instance = new PHPLeague_Admin();
            $parent   = 'phpleague_overview';
            
            if (function_exists('add_menu_page')) {
                add_menu_page(
                    __('Dashboard (PHPLeague)', 'phpleague'),
                    __('PHPLeague', 'phpleague'),
                    $this->access,
                    $parent,
                    array($instance, 'admin_page'),
                    plugins_url('phpleague/assets/img/league.png')
                );
            }
            
            if (function_exists('add_submenu_page')) {
                add_submenu_page(
                    $parent,
                    __('Clubs (PHPLeague)', 'phpleague'),
                    __('Clubs', 'phpleague'),
                    $this->access,
                    'phpleague_club',
                    array($instance, 'admin_page')
                );
                
                add_submenu_page(
                    $parent,
                    __('Players (PHPLeague)', 'phpleague'),
                    __('Players', 'phpleague'),
                    $this->access,
                    'phpleague_player',
                    array($instance, 'admin_page')
                );
                
                add_submenu_page(
                    $parent,
                    __('About (PHPLeague)', 'phpleague'),
                    __('About', 'phpleague'),
                    $this->access,
                    'phpleague_about',
                    array($instance, 'admin_page')
                );
            }
        }
        
        /**
         * Add TinyMCE Button
         *
         * @param  none
         * @return void
         */
        public function add_editor_button()
        {
            // Don't bother doing this stuff if the current user lacks permissions
            if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages')) return;

            // Check for PHPLeague capability
            if ( ! current_user_can('phpleague')) return;

            // Add only in Rich Editor mode
            if (get_user_option('rich_editing') == 'true') {
                add_filter('mce_external_plugins', array(&$this, 'add_editor_plugin'));
                add_filter('mce_buttons', array(&$this, 'register_editor_button'));
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
        
        // -- Frontend methods -- //

        /**
         * Add the front css
         *
         * @param  none
         * @return void
         */
        public function print_front_styles()
        {
            wp_register_style('phpleague-front', plugins_url('phpleague/assets/css/phpleague-front.css'));
            wp_enqueue_style('phpleague-front');
        }
        
        /**
         * We got all the public functions in here
         *
         * @param  none
         * @return void
         */
        public function display_phpleague($atts)
        {
            extract(shortcode_atts(
                array(
                    'id'      => 1,
                    'type'    => 'table',
                    'style'   => 'general',
                    'id_team' => ''
                ),
                $atts
            ));

            $id    = intval($id);
            $front = new PHPLeague_Front();
            
            if ($type == 'table') {
                if ($style == 'home')
                    return $front->get_league_table($id, 'home');
                elseif ($style == 'away')
                    return $front->get_league_table($id, 'away');
                else
                    return $front->get_league_table($id);
            } elseif ($type == 'fixtures') {
                if ( ! empty($id_team))
                    return $front->get_league_fixtures($id, $id_team);
                else
                    return $front->get_league_fixtures($id);                
            } elseif ($type == 'club') {
                return $front->get_club_information($id);                   
            } else {
                return $front->get_league_table($id);
            }
        }
    }
    
    $phpleague = new PHPLeague();
}