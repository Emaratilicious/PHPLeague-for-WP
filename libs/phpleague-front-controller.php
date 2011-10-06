<?php

/*
 * This file is part of the PHPLeague package.
 *
 * (c) Maxime Dizerens <mdizerens@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if ( ! class_exists('PHPLeague_Front_Controller')) {
	
	/**
     * Manage the rendering in the front-end.
     *
     * @package    PHPLeague
     * @category   Front_Controller
     * @author     Maxime Dizerens
     * @copyright  (c) 2011 Mikaweb Design, Ltd
     */
    class PHPLeague_Front_Controller {

        /**
	     * Constructor
	     */
		public function __construct() {}

		// display the league table in the front area
		public function get_league_table($id, $style = 'general')
		{
			global $wpdb;

			$db = new PHPLeague_Database();

			// ID not in the database
			if ($db->is_league_exists($id) === FALSE)
				return;

			$setting    = $db->get_league_settings($id);
			$nb_teams  	= intval($setting->nb_teams);
			$promotion 	= intval($setting->promotion);
			$qualifying	= intval($setting->qualifying) + $promotion;
			$favorite   = intval($setting->id_favorite);
			$relegation = $nb_teams - intval($setting->relegation);

			$output =
			'<table id="phpleague">
				<thead>
					<tr>
						<th class="centered">'.__('Pos', 'phpleague').'</th>
						<th>'.__('Team', 'phpleague').'</th>
						<th class="centered">'.__('Pts', 'phpleague').'</th>
						<th class="centered">'.__('P', 'phpleague').'</th>
						<th class="centered">'.__('W', 'phpleague').'</th>
						<th class="centered">'.__('D', 'phpleague').'</th>
						<th class="centered">'.__('L', 'phpleague').'</th>
						<th class="centered">'.__('F', 'phpleague').'</th>
						<th class="centered">'.__('A', 'phpleague').'</th>
						<th class="centered">'.__('+/-', 'phpleague').'</th>
						<th class="centered"></th>
					</tr>
				</thead>
				<tbody>';

			$place = 1;

			foreach ($db->get_league_table_data($style, $id, $nb_teams) as $row)
			{
				if ($place <= $nb_teams)
				{				
					if ($favorite == $row->id_team)
						$output .= '<tr class="id-favorite">';
					else
						$output .= '<tr>';

					if ($place <= $promotion)
						$span = '<span class="promotion"></span>';
					elseif ($place <= $qualifying)
						$span = '<span class="qualifying"></span>';
					elseif ($place > $relegation)
						$span = '<span class="relegation"></span>';
					else
						$span = '<span></span>';

					if ($style == 'home') {
						$points 	  = intval($row->home_points);
						$played 	  = intval($row->home_played);
						$victory 	  = intval($row->home_v);
						$draw 		  = intval($row->home_d);
						$defeat 	  = intval($row->home_l);
						$goal_for 	  = intval($row->home_g_for);
						$goal_against = intval($row->home_g_against);
						$diff 		  = intval($row->home_diff);
					} elseif ($style == 'away') {
						$points 	  = intval($row->away_points);
						$played 	  = intval($row->away_played);
						$victory 	  = intval($row->away_v);
						$draw 		  = intval($row->away_d);
						$defeat 	  = intval($row->away_l);
						$goal_for 	  = intval($row->away_g_for);
						$goal_against = intval($row->away_g_against);
						$diff 		  = intval($row->away_diff);
					} else {
						$points 	  = intval($row->points);
						$played 	  = intval($row->played);
						$victory 	  = intval($row->victory);
						$draw 		  = intval($row->draw);
						$defeat 	  = intval($row->defeat);
						$goal_for 	  = intval($row->goal_for);
						$goal_against = intval($row->goal_against);
						$diff 		  = intval($row->diff);
					}
					
					$output .= '<td class="centered">'.$place.'</td>';
					
                    // If logo file exists, we show it
					if (is_file(WP_PHPLEAGUE_LOGOS_PATH.'logo_mini/'.$row->logo_mini))
						$output .= '<td><img src="'.content_url('uploads/phpleague/logo_mini/'.$row->logo_mini).'" alt="'.esc_html($row->club_name).'" />&nbsp;&nbsp;'.esc_html($row->club_name).'</td>';
					else
						$output .= '<td>'.esc_html($row->club_name).'</td>';
					
					$output .= '<td class="centered">'.$points.'</td>';
					$output .= '<td class="centered">'.$played.'</td>';
					$output .= '<td class="centered">'.$victory.'</td>';
					$output .= '<td class="centered">'.$draw.'</td>';
					$output .= '<td class="centered">'.$defeat.'</td>';
					$output .= '<td class="centered">'.$goal_for.'</td>';
					$output .= '<td class="centered">'.$goal_against.'</td>';
					$output .= '<td class="centered">'.$diff.'</td>';
					$output .= '<td class="centered">'.$span.'</td></tr>';

					$place++;
				}
			}

			$output .= '</tbody></table>';
			return $output;
		}

		// display the league fixtures in the front area
		public function get_league_fixtures($id_league, $id_team = NULL)
		{
			global $wpdb;

			$db = new PHPLeague_Database();

			// ID not in the database
			if ($db->is_league_exists($id_league) === FALSE)
				return;

			// If team does not exist in the league
			$team_exist = $db->is_team_already_in_league($id_league, $id_team);

			$setting  = $db->get_league_settings($id_league);
			$id_team  = (isset($id_team) && $team_exist === FALSE) ? intval($id_team) : intval($setting->id_favorite);
			
			// No favorite team has been given
			if ($id_team == 0)
				return;

			$output  = '<table id="phpleague"><tbody>';
			$output .=
			'<tr>
                <th>'.__('Date', 'phpleague').'</th>
                <th class="centered">'.__('Fixture', 'phpleague').'</th>
                <th>'.__('Match', 'phpleague').'</th>
                <th class="centered">'.__('Score', 'phpleague').'</th>
            </tr>';

			foreach ($db->get_league_fixtures_by_team($id_team, $id_league) as $row)
			{
				$output .= '<tr>';
				$output .= '<td>'.strftime("%a %e, %H:%S", strtotime($row->played)).'</td>';
				$output .= '<td class="centered">'.intval($row->number).'</td>';
				$output .= '<td>'.esc_html($row->home_name).' - '.esc_html($row->away_name).'</td>';

				if (date('Y-m-d') >= $row->played)
					$output .= '<td class="centered">'.intval($row->goal_home).' - '.intval($row->goal_away).'</td>';
				else
					$output .= '<td class="centered"> - </td>';

				$output .= '</tr>';
			}

			$output .= '</tbody></table>';
			return $output;
		}
		
		// display the club information in the front area
		public function get_club_information($id = NULL)
		{
			global $wpdb;

			$db = new PHPLeague_Database();

			// ID not in the database
			if ($db->is_club_unique($id, 'id') === TRUE)
				return;
				
			$info = $db->get_club_information($id);
			
			$output  = '<table id="phpleague"><caption>'.esc_html($info->name).__(' Factfile', 'phpleague').'</caption><tbody>';
			$output .=
			'<tr>
				<th rows="2">'.__('Details', 'phpleague').'</th>
            </tr>';

			$output .= '<tr><td><strong>'.__('Name: ', 'phpleague').'</strong></td><td>'.esc_html($info->name).'</td></tr>';
			$output .= '<tr><td><strong>'.__('Coach: ', 'phpleague').'</strong></td><td>'.esc_html($info->coach).'</td></tr>';
			$output .= '<tr><td><strong>'.__('Venue: ', 'phpleague').'</strong></td><td>'.esc_html($info->venue).'</td></tr>';
			$output .= '<tr><td><strong>'.__('Website: ', 'phpleague').'</strong></td><td><a href="'.esc_html($info->website).'">'.esc_html($info->website).'</a></td></tr>';
			$output .= '<tr><td><strong>'.__('Creation: ', 'phpleague').'</strong></td><td>'.intval($info->creation).'</td></tr>';
			
			$output .= '</tbody></table>';
			return $output;
		}
	}
}