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

if ( ! class_exists('MWD_Plugin_Tools')) {

	/**
     * Tools library.
     *
     * @package    Plugin_Tools
     * @category   MWD
     * @author     Maxime Dizerens
     * @copyright  (c) 2011 Mikaweb Design, Ltd
     */
	class MWD_Plugin_Tools {

		public $hook 	   = '';
		public $version	   = '';
		public $path	   = '';
		public $longname   = '';
		public $shortname  = '';
		public $homepage   = 'http://www.mika-web.com/';
		public $access	   = 'administrator';
		public $feed	   = 'http://www.mika-web.com/feed/';
		
		/**
         * @var  array  preferred order of attributes
         */
        public static $attribute_order = array
        (
            'action',
            'method',
            'type',
            'id',
            'name',
            'value',
            'href',
            'src',
            'width',
            'height',
            'cols',
            'rows',
            'size',
            'maxlength',
            'rel',
            'media',
            'accept-charset',
            'accept',
            'tabindex',
            'accesskey',
            'alt',
            'title',
            'class',
            'style',
            'selected',
            'checked',
            'readonly',
            'disabled',
        );
		
		/**
	     * Constructor
	     */
		public function __construct() {}
		
		/**
	     * Admin initializer
	     */
		public function plugin_admin_init()
		{
            // Set Capabilities
			$role = get_role($this->access);
			$role->add_cap('manage_phpleague');
			$role->add_cap('phpleague');

			// For the editor button
			$role = get_role('editor');
			$role->add_cap('phpleague');
			
			if (isset($_GET['page']) && in_array(trim($_GET['page']), $this->pages)) {
		    	if ( ! current_user_can('manage_phpleague')) {
					wp_die(__('Access Denied! Permission insufficient to run: ').$this->hook, 'phpleague');
				}
			}
		}
		
		/**
	     * Plugin upgrade handler
	     */
		public function plugin_check_upgrade()
		{
			global $wpdb;
			
			$version = get_option('phpleague_version');
			$current_version = isset($version) ? $version : 0;
			
			$db_version = get_option('phpleague_db_version');
			$current_db_version = isset($db_version) ? $db_version : 0;

			// You're already using the latest version
			if ($current_version == WP_PHPLEAGUE_VERSION || $current_version == 0 || $current_db_version == 0) {
				return;
			}
			
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
				
				// penalty is not unsigned anymore
				$wpdb->query("ALTER TABLE $wpdb->team MODIFY penalty TINYINT(1) NOT NULL DEFAULT '0';");
			}

			if ($current_version < WP_PHPLEAGUE_VERSION) {
				// do stuff in order to upgarde PHPLeague
				// might be database related or anything else
				update_option('phpleague_version', WP_PHPLEAGUE_VERSION);
				update_option('phpleague_db_version', WP_PHPLEAGUE_DB_VERSION);
			}
		}
		
		/**
	     * Define every table we are using
	     */
		public function define_tables()
		{		
			global $wpdb;

			$wpdb->club 	   = $wpdb->prefix.'phpleague_club';
			$wpdb->country	   = $wpdb->prefix.'phpleague_country';
			$wpdb->fixture	   = $wpdb->prefix.'phpleague_fixture';
			$wpdb->league	   = $wpdb->prefix.'phpleague_league';
			$wpdb->match 	   = $wpdb->prefix.'phpleague_match';
			$wpdb->table_cache = $wpdb->prefix.'phpleague_table_cache';
			$wpdb->team  	   = $wpdb->prefix.'phpleague_team';
		}
		
		/**
         * Handle pagination.
         *
         * @param   int    total items
         * @param   int    items per page
         * @param   int    current page number
		 * @param   string query string
         * @return  string
         */
        public function pagination($total_items, $items_p_page, $page_number, $query = 'p_nb')
        {
            $nb_pages   = ceil($total_items / $items_p_page);
			$page_links = paginate_links(array
			(
				'base' 		=> add_query_arg($query, '%#%'),
				'format' 	=> '',
				'prev_text' => __('&laquo;', 'phpleague'),
				'next_text' => __('&raquo;', 'phpleague'),
				'total' 	=> $nb_pages,
				'current' 	=> $page_number
			));

            return $page_links;
        }

		/**
		 * Alternates between two or more strings.
		 *
		 * Note that using multiple iterations of different strings may produce
		 * unexpected results.
		 *
		 * @param   string  strings to alternate between
		 * @return  string
		 */
		public function alternate()
		{
			static $i;

			if (func_num_args() === 0) {
				$i = 0;
				return '';
			}

			$args = func_get_args();
			return $args[($i++ % count($args))];
		}
        
        /**
         * Compiles an array of HTML attributes into an attribute string.
         *
         * @param   array  $attributes
         * @return  string
         */
        public static function attributes(array $attributes = NULL)
        {
            if (empty($attributes)) {
				return '';
			}

            $sorted = array();
            foreach (MWD_Plugin_Tools::$attribute_order as $key) {
                if (isset($attributes[$key])) {
                    // Add the attribute to the sorted list
                    $sorted[$key] = $attributes[$key];
                }
            }

            // Combine the sorted attributes
            $attributes = $sorted + $attributes;

            $compiled = '';
            foreach ($attributes as $key => $value) {
                if ($value === NULL) {
                    // Skip attributes that have NULL values
                    continue;
                }

                if (is_int($key)) {
                    // Assume non-associative keys are mirrored attributes
                    $key = $value;
                }

                // Add the attribute value
                $compiled .= ' '.$key.'="'.esc_html($value).'"';
            }

            return $compiled;
        }
        
        /**
         * Creates a form input. If no type is specified, a "text" type input will
         * be returned.
         *
         * @param   string  input name
         * @param   string  input value
         * @param   array   html attributes
         * @return  string
         */
        public function input($name, $value = NULL, array $attributes = NULL)
        {
            // Set the input name
            $attributes['name'] = $name;

            // Set the input value
            $attributes['value'] = $value;

            if ( ! isset($attributes['type'])) {
                // Default type is text
                $attributes['type'] = 'text';
            }

            return '<input'.MWD_Plugin_Tools::attributes($attributes).' />';
        }
        
        /**
         * Creates a textarea form input.
         *
         * @param   string   textarea name
         * @param   string   textarea body
         * @param   array    html attributes
         * @param   boolean  encode existing HTML characters
         * @return  string
         */
        public function textarea($name, $body = '', array $attributes = NULL)
        {
            // Set the input name
            $attributes['name'] = $name;

            // Add default rows and cols attributes (required)
            $attributes += array('rows' => 10, 'cols' => 50);

            return '<textarea'.MWD_Plugin_Tools::attributes($attributes).'>'.esc_html($body).'</textarea>';
        }

		/**
		 * Generates an opening HTML form tag.
		 *
		 * @param   string  form action
		 * @param   array   html attributes
		 * @return  string
		 */
		public function form_open($action = NULL, array $attributes = NULL)
		{
			if ($action === NULL) {
				return wp_die(__('An error occurred! We need to fix this later...', 'phpleague'));
			}

			// Add the form action to the attributes
			$attributes['action'] = $action;

			// Only accept the default character set
			$attributes['accept-charset'] = 'UTF-8';

			if ( ! isset($attributes['method'])) {
				// Use POST method
				$attributes['method'] = 'post';
			}

			return '<form'.MWD_Plugin_Tools::attributes($attributes).'>';
		}

		/**
		 * Creates the closing form tag.
		 *
		 * @return  string
		 */
		public function form_close()
		{
			$nonce = '';
			if (is_admin()) {
				$nonce = wp_nonce_field('phpleague_nonce_admin');
			}
			
			return $nonce.'</form>';
		}
		
		/**
		 * Creates a select form input.
		 *
		 * @param   string   input name
		 * @param   array    available options
		 * @param   mixed    selected option string, or an array of selected options
		 * @param   array    html attributes
		 * @return  string
		 */
		public function select($name, array $options = NULL, $selected = NULL, array $attributes = NULL)
		{
			// Set the input name
			$attributes['name'] = $name;

			if (is_array($selected)) {
				// This is a multi-select, god save us!
				$attributes['multiple'] = 'multiple';
			}

			if ( ! is_array($selected)) {
				if ($selected === NULL) {
					// Use an empty array
					$selected = array();
				} else {
					// Convert the selected options to an array
					$selected = array((string) $selected);
				}
			}

			if (empty($options)) {
				// There are no options
				$options = '';
			} else {
				foreach ($options as $value => $name) {
					if (is_array($name)) {
						// Create a new optgroup
						$group = array('label' => $value);

						// Create a new list of options
						$_options = array();

						foreach ($name as $_value => $_name) {
							// Force value to be string
							$_value = (string) $_value;

							// Create a new attribute set for this option
							$option = array('value' => $_value);

							if (in_array($_value, $selected)) {
								// This option is selected
								$option['selected'] = 'selected';
							}

							// Change the option to the HTML string
							$_options[] = '<option'.MWD_Plugin_Tools::attributes($option).'>'.esc_html($_name).'</option>';
						}

						// Compile the options into a string
						$_options = "\n".implode("\n", $_options)."\n";

						$options[$value] = '<optgroup'.MWD_Plugin_Tools::attributes($group).'>'.$_options.'</optgroup>';
					} else {
						// Force value to be string
						$value = (string) $value;

						// Create a new attribute set for this option
						$option = array('value' => $value);

						if (in_array($value, $selected)) {
							// This option is selected
							$option['selected'] = 'selected';
						}

						// Change the option to the HTML string
						$options[$value] = '<option'.MWD_Plugin_Tools::attributes($option).'>'.esc_html($name).'</option>';
					}
				}

				// Compile the options into a single string
				$options = "\n".implode("\n", $options)."\n";
			}

			return '<select'.MWD_Plugin_Tools::attributes($attributes).'>'.$options.'</select>';
		}
	}
}

if ( ! class_exists('MWD_Plugin_Igniter')) {

	/**
     * Activation & uninstallation library.
     *
     * @package    Plugin_Igniter
     * @category   MWD
     * @author     Maxime Dizerens
     * @copyright  (c) 2011 Mikaweb Design, Ltd
     */
    class MWD_Plugin_Igniter {

		/**
	     * Constructor
	     */
	    public function __construct() {}
	
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
			
			// Table does not exist, create it!
			if ($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name) {
				$create = "CREATE TABLE ".$table_name." ( ".$sql." ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;";
				require_once ABSPATH.'wp-admin/includes/upgrade.php';
				dbDelta($create);
			}
		}
	
		/**
	     * The user is activating the plugin, do what we need to do
	     */
	    public static function activate()
	    {
			global $wpdb;
			
			$db_version = WP_PHPLEAGUE_DB_VERSION;
			
			// -- Club table
			$sql = "id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
				name VARCHAR(255) NOT NULL DEFAULT '',
				id_country smallint(4) unsigned NOT NULL,
				venue VARCHAR(100) DEFAULT NULL,
				coach VARCHAR(100) DEFAULT NULL,
				logo_big VARCHAR(255) DEFAULT NULL,
				logo_mini VARCHAR(255) DEFAULT NULL,
				PRIMARY KEY (id),
				UNIQUE KEY name (name)";

			MWD_Plugin_Igniter::run_install_or_upgrade($wpdb->club, $sql, $db_version);
			
			// -- Country table
			$sql = "id tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
				name VARCHAR(100) DEFAULT NULL,
				PRIMARY KEY (id),
				UNIQUE KEY name (name)";

			MWD_Plugin_Igniter::run_install_or_upgrade($wpdb->country, $sql, $db_version);
			
			// -- Fixture table
			$sql = "number tinyint(3) unsigned NOT NULL DEFAULT '0',
				scheduled date NOT NULL DEFAULT '0000-00-00',
				id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
				id_league smallint(5) unsigned NOT NULL DEFAULT '0',
				PRIMARY KEY (id),
				KEY id_league (id_league)";

			MWD_Plugin_Igniter::run_install_or_upgrade($wpdb->fixture, $sql, $db_version);
			
			// -- League table
			$sql = "id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
				name VARCHAR(100) NOT NULL DEFAULT '',
				year year(4) NOT NULL,
				pt_victory tinyint(3) unsigned NOT NULL DEFAULT '3',
				pt_draw tinyint(3) unsigned NOT NULL DEFAULT '1',
				pt_defeat tinyint(3) unsigned NOT NULL DEFAULT '0',
				promotion tinyint(3) unsigned NOT NULL DEFAULT '4',
				qualifying tinyint(3) unsigned NOT NULL DEFAULT '2',
				relegation tinyint(3) unsigned NOT NULL DEFAULT '3',
				id_favorite smallint(4) unsigned DEFAULT NULL,
				nb_leg tinyint(1) NOT NULL DEFAULT '2',
				nb_teams tinyint(1) NOT NULL DEFAULT '0',
				PRIMARY KEY (id)";

			MWD_Plugin_Igniter::run_install_or_upgrade($wpdb->league, $sql, $db_version);
			
			// -- Match table
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

			MWD_Plugin_Igniter::run_install_or_upgrade($wpdb->match, $sql, $db_version);
			
			// -- Table Cache table
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

			MWD_Plugin_Igniter::run_install_or_upgrade($wpdb->table_cache, $sql, $db_version);
			
			// -- Team table
			$sql = "id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
				id_league smallint(5) unsigned NOT NULL DEFAULT '0',
				id_club smallint(5) unsigned NOT NULL DEFAULT '0',
				penalty tinyint(1) NOT NULL DEFAULT '0',
				PRIMARY KEY (id),
				KEY id_club (id_club),
				KEY id_league (id_league)";

			MWD_Plugin_Igniter::run_install_or_upgrade($wpdb->team, $sql, $db_version);
			
			// Alter few tables in order to add some constraints
			$wpdb->query("ALTER TABLE $wpdb->fixture ADD CONSTRAINT phpleague_fixture_ibfk_1 FOREIGN KEY (id_league) REFERENCES $wpdb->league (id) ON DELETE CASCADE;");
			$wpdb->query("ALTER TABLE $wpdb->match ADD CONSTRAINT phpleague_match_ibfk_1 FOREIGN KEY (id_fixture) REFERENCES $wpdb->fixture (id) ON DELETE CASCADE;");
			$wpdb->query("ALTER TABLE $wpdb->table_cache ADD CONSTRAINT phpleague_table_cache_ibfk_1 FOREIGN KEY (id_league) REFERENCES $wpdb->league (id) ON DELETE CASCADE;");			
			$wpdb->query("ALTER TABLE $wpdb->team ADD CONSTRAINT phpleague_team_ibfk_1 FOREIGN KEY (id_league) REFERENCES $wpdb->league (id) ON DELETE CASCADE;");
			
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
				56 => 'Croatia&nbsp;Hrvatska)',
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
				114 => 'Korea&nbsp;North)',
				115 => 'Korea&nbsp;South)',
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
				179 => 'Russian&nbsp;Federation',
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
				234 => 'Virgin&nbsp;Islands&nbsp;British)',
				235 => 'Virgin&nbsp;Islands&nbsp;US)',
				236 => 'Wallis&nbsp;and&nbsp;Futuna&nbsp;Islands',
				237 => 'Western&nbsp;Sahara',
				238 => 'England',
				239 => 'Wales',
				240 => 'Northern Ireland',
				241 => 'Scotland'
			);
			
			// Update the country table with all of the above
			foreach ($countries as $key => $country) {
				$wpdb->query("INSERT INTO $wpdb->country VALUES($key, '".$country."');");
			}
			
			// Save versions
			add_option('phpleague_version', WP_PHPLEAGUE_VERSION);
			add_option('phpleague_db_version', $db_version);
	    }

		/**
	     * The user is deactivating the plugin, do what we need to do
	     */
	    public static function deactivate() {}

		/**
	     * The user is deleting the plugin, do what we need to do
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
			
			// To avoid error during the drop process
			$wpdb->query('SET FOREIGN_KEY_CHECKS=0;');

			foreach ($tables as $table) {
				$wpdb->query('DROP TABLE IF EXISTS '.$table.';');
			}
			
			// Get back to normal
			$wpdb->query('SET FOREIGN_KEY_CHECKS=1;');
			
			// Delete the version in the options table
			delete_option('phpleague_version');
			delete_option('phpleague_db_version');
	    }
	}
}