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

if ( ! class_exists('PHPLeague_Admin_Controller')) {
	
    /**
     * Manage the rendering in the admin area.
     *
     * @package    PHPLeague
     * @category   Admin_Controller
     * @author     Maxime Dizerens
     * @copyright  (c) 2011 Mikaweb Design, Ltd
     */
    class PHPLeague_Admin_Controller {

        /**
	     * Constructor
	     */
		public function __construct() {}
        
        /**
	     * Page header
	     */
        public function admin_header()
        {
			return '
			<div id="adminpanel">
				<div id="adminpanel-header">
					<div class="logo"><img alt="PHPLeague logo" src="'.plugins_url('phpleague/assets/img/logo.png').'" /></div>
				    <div class="theme-info">
						<span class="plugin">'.__('PHPLeague for WP - Core Edition', 'phpleague').'</span>
						<span class="release">'.__('Release: 1.2.5', 'phpleague').'</span>
					</div>
				</div>
				<div id="support-links">
					<ul>
						<li class="changelog"><a href="http://wordpress.org/extend/plugins/phpleague/changelog/">'.__('Changelog', 'phpleague').'</a></li>
						<li class="docs"><a href="http://www.mika-web.com/phpleague-for-wp-features/">'.__('Documentation', 'phpleague').'</a></li>
						<li class="help"><a href="http://www.mika-web.com/">'.__('Help', 'phpleague').'</a></li>
					</ul>
				</div>
				<div id="adminpanel-main">';
        }

		/**
		 * Page Container
		 *
		 * @param  array  $menu
		 * @param  array  $content
		 * @param  string $notification
		 *
		 * @return string
		 */
		public function admin_container($menu = array(), $content = array(), $notification = NULL)
	    {			
			$output = '<div id="adminpanel-menu"><ul>';
			
			foreach ($menu as $key => $value) {
				$output .= '
				<li class="adminpanel-menu-li">
					<a href="'.$value.'" class="adminpanel-menu-link" id="adminpanel-menu-'.strtolower(str_replace(' ', '', $key)).'">
						'.$key.'
					</a>
				</li>';
			}
			
			$output .= '</ul></div>';
			$output .= '<div id="adminpanel-content">';
			
			if ( ! is_null($notification) && $notification !== '') {
				$output .= '<div class="updated"><p>'.$notification.'</p></div>';
			}
			
			foreach ($menu as $key => $item) {
				$output .= '<div class="adminpanel-content-box" id="adminpanel-content-'.strtolower(str_replace(' ', '', $key)).'">';
				foreach ($content as $value) {
					if ($key == $value['menu']) {
						$output .= '
							<div class="section">
								<h3 class="heading">'.$value['title'].'</h3>
								<div class="option">
									<div class="'.$value['class'].'">'.$value['text'].'</div>
									<div class="clear"></div>
								</div>
							</div>';
					}
				}
				
				$output .= '</div>';
			}
			
			$output .= '</div>';

	        return $output;
	  	}

		/**
		 * Page Wrapper
		 */
		public function admin_wrapper($width = 98, $content = null)
	    {
	    	$output = '<div class="postbox-container" style="width: '.$width.'%">';
			$output .= $content;
			$output .= '</div>';

	        return $output;
	  	}
	
		/**
	     * Page footer
	     */
        public function admin_footer()
        {
            return '<div class="clear"></div></div><div id="adminpanel-footer"></div></div>';
        }

		/**
	     * Pages handler
	     */
        public function admin_page()
        {
        	// Page Header
			echo $this->admin_header();

			// Page Wrapper
			switch (trim($_GET['page'])) {
				case 'phpleague_club' :
					require_once WP_PHPLEAGUE_PATH.'inc/admin/club.php';
					break;
				case 'phpleague_setting' :
					require_once WP_PHPLEAGUE_PATH.'inc/admin/setting.php';
					break;
				case 'phpleague_about' :
					require_once WP_PHPLEAGUE_PATH.'inc/admin/about.php';
					break;
				case 'phpleague_overview' :
				default :
					require_once WP_PHPLEAGUE_PATH.'inc/admin/overview.php';
					break;
			}
			
			// Page Footer
			echo $this->admin_footer();
        }
	}
}