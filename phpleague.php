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
Plugin URI: http://www.mika-web.com/phpleague-for-wordpress/
Description: PHPLeague for WordPress is the best companion to manage your sports leagues.
Version: 1.2.8
Author: Maxime Dizerens
Author URI: http://www.mika-web.com/
*/

// Constants
define('WP_PHPLEAGUE_VERSION', '1.2.8');
define('WP_PHPLEAGUE_DB_VERSION', '1.2.3');
define('WP_PHPLEAGUE_EDITION', 'Core');
define('WP_PHPLEAGUE_PATH', plugin_dir_path(__FILE__));
define('WP_PHPLEAGUE_LOGOS_PATH', WP_PHPLEAGUE_PATH.'../../uploads/phpleague/');

// PHPLeague Translation
load_plugin_textdomain('phpleague', FALSE, 'phpleague/i18n');

// Core files
require_once WP_PHPLEAGUE_PATH.'libs/plugin-tools.php';
require_once WP_PHPLEAGUE_PATH.'libs/phpleague-database.php';

// Specific actions
register_activation_hook(__FILE__, array('Plugin_Igniter', 'activate'));
register_uninstall_hook(__FILE__, array('Plugin_Igniter', 'uninstall'));

if ( ! class_exists('PHPLeague_Admin') && is_admin() && ( ! defined('DOING_AJAX') || ! DOING_AJAX)) {
	
	class PHPLeague_Admin extends Plugin_Tools {

		public $longname  = 'PHPLeague for WordPress';
		public $shortname = 'PHPLeague for WP';
		public $access	  = 'administrator';
		public $pages 	  = array
		(
            'phpleague_about',
		    'phpleague_overview',
		    'phpleague_club',
		    'phpleague_setting'
	   );

		/**
	     * Constructor
	     */
		public function __construct()
		{
            parent::__construct();
            $this->define_tables();
			add_action('init', array(&$this, 'add_editor_button'));
			add_action('admin_init', array(&$this, 'plugin_admin_init'));
			add_action('admin_init', array(&$this, 'plugin_check_upgrade'));
			add_action('admin_menu', array(&$this, 'admin_menu'));
            add_action('admin_print_styles', array(&$this, 'print_admin_styles'));
			add_action('admin_print_scripts', array(&$this, 'print_admin_scripts'));
		}
		
        /**
	     * Add the admin styles
	     */
		public function print_admin_styles()
		{
			if (isset($_GET['page']))
			{
				if ( ! in_array(trim($_GET['page']), $this->pages))
				 	return;
			}
			
			$handle = 'phpleague-admin';
			wp_register_style($handle, plugins_url('phpleague/assets/css/phpleague-admin.css'));
		    wp_enqueue_style($handle);
		}
		
		/**
	     * Add the admin scripts
	     */
		public function print_admin_scripts()
		{
			if (isset($_GET['page']))
			{
				if ( ! in_array(trim($_GET['page']), $this->pages))
				 	return;
			}
			
			wp_register_script('phpleague', plugins_url('phpleague/assets/js/admin.js'));
			wp_enqueue_script('phpleague');
		}
		
		/**
	     * Generate the admin menu
		 *
		 * @param  none
		 * @return void
	     */
		public function admin_menu()
		{           
            require_once WP_PHPLEAGUE_PATH.'libs/phpleague-admin-controller.php';

			$instance = new PHPLeague_Admin_Controller();
			$parent   = 'phpleague_overview';
			
			if (function_exists('add_menu_page'))
			{
				add_menu_page(
					__('Dashboard (PHPLeague)', 'phpleague'),
					__('PHPLeague', 'phpleague'),
					$this->access,
					$parent,
					array($instance, 'admin_page'),
					plugins_url('phpleague/assets/img/league.png')
				);
			}
			
			if (function_exists('add_submenu_page'))
			{
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
					__('Settings (PHPLeague)', 'phpleague'),
					__('Settings', 'phpleague'),
					$this->access,
					'phpleague_setting',
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
			if (get_user_option('rich_editing') == 'true')
			{
				add_filter('mce_external_plugins', array(&$this, 'add_editor_plugin'));
				add_filter('mce_buttons', array(&$this, 'register_editor_button'));
			}
		}
		
		/**
		 * Add TinyMCE plugin
		 *
		 * @param  array
		 * @return void
		 */
		public function add_editor_plugin($plugin_array)
		{
			$plugin_array['PHPLeague'] = plugins_url('phpleague/assets/js/tinymce/editor_plugin.js');
			return $plugin_array;
		}
		
		/**
		 * Register TinyMCE button
		 *
		 * @param  array
		 * @return void
		 */
		public function register_editor_button($buttons)
		{
			array_push($buttons, 'separator', 'PHPLeague');
			return $buttons;
		}
	}
	
	$phpleague_admin = new PHPLeague_Admin();
}

if ( ! class_exists('PHPLeague_Front') && ! is_admin() && ( ! defined('DOING_AJAX') || ! DOING_AJAX)) {
	
	class PHPLeague_Front extends Plugin_Tools {

		public $longname  = 'PHPLeague for WordPress';
		public $shortname = 'PHPLeague for WP';
		public $access	  = '';

		public function __construct()
		{
			parent::__construct();
			$this->define_tables();
			add_action('wp_print_styles', array(&$this, 'print_front_styles'));
			add_shortcode('phpleague', array(&$this, 'display_phpleague'));
		}
		
		/**
	     * Add the front css
	     */
		public function print_front_styles()
		{
			$handle = 'phpleague-front';
			wp_register_style($handle, plugins_url('phpleague/assets/css/phpleague-front.css'));
		    wp_enqueue_style($handle);
		}
		
		// We got all the public functions in here
		public function display_phpleague($atts)
		{
			extract(shortcode_atts(
                array(
                    'id'      => 1,
                    'type'    => 'table',
                    'style'   => 'general',
                    'id_team' => '',
                ),
                $atts
            ));

			$id = intval($id);
			
			require_once WP_PHPLEAGUE_PATH.'libs/phpleague-front-controller.php';
			$front = new PHPLeague_Front_Controller();
			
			if ($type == 'table')
			{
				if ($style == 'home')
					return $front->get_league_table($id, 'home');
				elseif ($style == 'away')
					return $front->get_league_table($id, 'away');
				else
					return $front->get_league_table($id);
			}
			elseif ($type == 'fixtures')
			{
				if ( ! empty($id_team))
					return $front->get_league_fixtures($id, $id_team);
				else
					return $front->get_league_fixtures($id);				
			}
			elseif ($type == 'club')
			{
				return $front->get_club_information($id);					
			}
			else
			{
				return $front->get_league_table($id);
			}
		}
	}
	
	$phpleague_front = new PHPLeague_Front();
}