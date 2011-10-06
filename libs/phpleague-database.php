<?php

/*
 * This file is part of the PHPLeague package.
 *
 * (c) Maxime Dizerens <mdizerens@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if ( ! class_exists('PHPLeague_Database')) {

    /**
     * Handle all the database interaction.
     *
     * @package    PHPLeague
     * @category   Database
     * @author     Maxime Dizerens
     * @copyright  (c) 2011 Mikaweb Design, Ltd
     */
	class PHPLeague_Database extends Plugin_Tools {

        /**
	     * Constructor
	     */
		public function __construct()
        {
            parent::__construct();
        }
		
		/**
	     * Count the clubs
		 * 
		 * @return integer
	     */
	    public function count_clubs()
	    {
			global $wpdb;

			return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->club"));
	    }
	
		/**
	     * Get every club
		 * 
		 * @param  integer $offset
		 * @param  integer $limit
		 * @param  string  $order
		 * @param  boolean $join_country
	     * @return object
	     */
	    public function get_every_club($offset = 0, $limit = 10, $order = 'DESC', $join_country = FALSE)
	    {
			global $wpdb;
			
			if ($join_country === FALSE)
			{
				return $wpdb->get_results($wpdb->prepare("SELECT id, name, id_country
					FROM $wpdb->club
					ORDER BY name $order
					LIMIT %d, %d",
					$offset, $limit)
				);
			}
			else
			{
				return $wpdb->get_results($wpdb->prepare("SELECT c.id, c.name, d.name as country
					FROM $wpdb->club c
					LEFT JOIN $wpdb->country d ON c.id_country = d.id
					ORDER BY c.name $order
					LIMIT %d, %d",
					$offset, $limit)
				);
			}
	    }
	
		/**
	     * Verify if a club is unique
		 *
		 * @param  mixed  $var
		 * @param  string $check_by
	  	 * @return boolean
	 	 */
		public function is_club_unique($var, $check_by = 'name')
		{
			global $wpdb;
			
			if ($check_by == 'name')
				$exist = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->club WHERE name = %s", $var));	
			elseif ($check_by == 'id')
				$exist = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->club WHERE id = %d", $var));
			else
				wp_die(__('An error occured!', 'phpleague'));
			
			// We didn't find a row
			if ($exist == 0)
				return TRUE;	
			else
				return FALSE;
		}
		
		/**
	     * Add club
		 *
		 * @param  string  $name
		 * @param  integer $id_country
	     * @return object
	     */
		public function add_club($name, $id_country)
		{			
			global $wpdb;
			
			return $wpdb->insert($wpdb->club, array('name' => $name, 'id_country' => $id_country), array('%s', '%d'));
		}
		
		/**
	     * Update club information
		 *
		 * @param  integer $id_club
		 * @param  string  $name
		 * @param  integer $id_country
		 * @param  string  $coach
		 * @param  string  $venue
         * @param  integer $creation
         * @param  string  $website
		 * @param  string  $logo_b
		 * @param  string  $logo_m
	     * @return object
	     */
		public function update_club_information($id_club, $name, $id_country, $coach, $venue, $creation, $website, $logo_b, $logo_m)
		{			
			global $wpdb;
			
			return
			$wpdb->update( 
				$wpdb->club, 
				array(
					'name' 		 => $name,
					'id_country' => $id_country,
					'coach' 	 => $coach,
					'venue' 	 => $venue,
					'creation'   => $creation,
                    'website'    => $website,
					'logo_big' 	 => $logo_b,
					'logo_mini'  => $logo_m,
				), 
				array('id' => $id_club), 
				array( 
					'%s',
					'%d',
					'%s',
					'%s',
					'%d',
					'%s',
					'%s',
					'%s'
				), 
				array('%d') 
			);
		}
		
		/**
	     * Get all data for a specific club
		 *
		 * @param  integer $id
	  	 * @return object
	 	 */
		public function get_club_information($id)
		{
			global $wpdb;
            
            return $wpdb->get_row($wpdb->prepare("SELECT name, venue, coach, id_country, logo_big, logo_mini, creation, website
				FROM $wpdb->club
				WHERE id = %d",
				$id)
			);
		}
		
		/**
	     * Count the leagues
		 * 
		 * @return integer
	     */
	    public function count_leagues()
	    {
			global $wpdb;

			return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->league"));
	    }
	
		/**
	     * Get every league
		 * 
		 * @param  integer $offset
		 * @param  integer $limit
	     * @return object
	     */
	    public function get_every_league($offset = 0, $limit = 10)
	    {
			global $wpdb;
            
            return $wpdb->get_results($wpdb->prepare("SELECT id, name, year
                FROM $wpdb->league
                ORDER BY year
                DESC LIMIT %d, %d",
				$offset, $limit)
			);
	    }
	
		/**
	     * Check if a league exists
		 *
		 * @param  integer $id_league
	  	 * @return boolean
	 	 */
		public function is_league_exists($id_league)
		{
			global $wpdb;
						
			$exist = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->league WHERE id = %d", $id_league));
			
			// We didn't find a row
			if ($exist == 0)
                return FALSE;    
            else
                return TRUE;
		}
	
		/**
	     * Verify if a league is unique
		 *
		 * @param  integer $name
		 * @param  integer $year
	  	 * @return boolean
	 	 */
		public function is_league_unique($name, $year)
		{
			global $wpdb;
						
			$exist = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->league WHERE name = %s AND year = %d", $name, $year));
			
			// We didn't find a row
			if ($exist == 0)
                return TRUE;    
            else
                return FALSE;
		}
		
		/**
	     * Add league
		 *
		 * @param  string  $name
		 * @param  integer $year
	     * @return object
	     */
		public function add_league($name, $year)
		{			
			global $wpdb;
			
			return $wpdb->insert($wpdb->league, array('name' => $name, 'year' => $year), array('%s', '%d'));
		}
		
		/**
	     * Return the full league name
		 *
		 * @param  integer	$id_league
	     * @return string
	     */
		public function return_league_name($id_league)
		{
			global $wpdb;
			            
            $obj = $wpdb->get_results($wpdb->prepare("SELECT name, year
						FROM $wpdb->league
						WHERE id = %d",
						$id_league)
			);
			
			foreach ($obj as $row)
			{
				$year = intval($row->year);
				$output = esc_html($row->name).' '.$year.'/'.substr($year + 1, 2);
			}

			return $output;
		}
		
		/**
	     * Check if a club is already in a league
		 *
		 * @param  integer	$id_league
		 * @param  integer	$id_club
	     * @return boolean
	     */
		public function is_club_already_in_league($id_league, $id_club)
		{
			global $wpdb;
						
			$exist = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->team WHERE id_league = %d AND id_club = %d", $id_league, $id_club));
			
			// We didn't find a row
			if ($exist == 0)
                return TRUE;    
            else
                return FALSE;
		}
		
		/**
	     * Add a club in a league
		 *
		 * @param  integer	$id_league
		 * @param  integer	$id_club
	     * @return object
	     */
		public function add_club_in_league($id_league, $id_club)
		{			
			global $wpdb;
			
			return $wpdb->insert($wpdb->team, array('id_league' => $id_league, 'id_club' => $id_club), array('%d', '%d'));
		}
		
		/**
		 * Update number of teams
	 	 *
		 * @param  integer $number
		 * @param  integer $id_league
		 * @param  string  $type
		 * @return object
     	 */
		public function update_nb_teams($number, $id_league, $type = 'plus')
		{
			global $wpdb;
			
			if ($type == 'minus')
				return $wpdb->query($wpdb->prepare("UPDATE $wpdb->league SET nb_teams = nb_teams - %d WHERE id = %d", $number, $id_league));
			else
				return $wpdb->query($wpdb->prepare("UPDATE $wpdb->league SET nb_teams = nb_teams + %d WHERE id = %d", $number, $id_league));
		}
		
		/**
	     * Remove a club from a league
		 *
		 * @param  integer $id_club
	     * @return object
	     */
		public function remove_club_from_league($id_club)
		{			
			global $wpdb;
			
			return $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->team WHERE id = %d", $id_club));
		}
		
		/**
	     * Get every club in a league
		 * 
		 * @param  integer $id_league
	     * @return object
	     */
	    public function get_every_club_in_league($id_league, $pagination = FALSE, $offset = 0, $limit = 10)
	    {
			global $wpdb;
			
			if ($pagination)
			{
				return $wpdb->get_results($wpdb->prepare("SELECT c.name, t.id
	                FROM $wpdb->club c,
	                     $wpdb->team t
	                WHERE t.id_club = c.id
	                AND t.id_league = %d
	                ORDER BY c.name
					ASC LIMIT %d, %d",
					$id_league, $offset, $limit)
				);
			}
            
            return $wpdb->get_results($wpdb->prepare("SELECT c.name, t.id
                FROM $wpdb->club c,
                     $wpdb->team t
                WHERE t.id_club = c.id
                AND t.id_league = %d
                ORDER BY c.name",
				$id_league)
			);
	    }
		
		/**
		 * Count the number of fixtures by league
	 	 *
	 	 * @param  integer	$id_league
     	 * @return integer
     	 */
		public function nb_fixtures_league($id_league)
		{
			global $wpdb;

			return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->fixture WHERE id_league = %d", $id_league));
		}
		
		/**
		 * Remove all the fixtures from a league
	 	 *
		 * @param  integer	$id_league
		 * @return object
     	 */
		public function remove_fixtures_league($id_league)
		{
			global $wpdb;
			
			return $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->fixture WHERE id_league = %d", $id_league));
		}
		
		/**
		 * Add fixtures in a league
	 	 *
		 * @param  integer	$number
		 * @param  integer	$id_league
		 * @return object
     	 */
		public function add_fixtures_league($number, $id_league)
		{
			global $wpdb;
			
			return $wpdb->insert($wpdb->fixture, array('number' => $number, 'id_league' => $id_league), array('%d', '%d'));
		}
		
		/**
	     * Get fixtures from a league
		 * 
		 * @param  integer $id_league
	     * @return object
	     */
	    public function get_fixtures_league($id_league)
	    {
			global $wpdb;
            
            return $wpdb->get_results($wpdb->prepare("SELECT number, scheduled, id FROM $wpdb->fixture WHERE id_league = %d ORDER BY number", $id_league));
	    }
		
		/**
		 * Edit league's fixtures
	 	 *
		 * @param  integer $number
		 * @param  string  $scheduled
		 * @param  integer $id_league
		 * @return object
     	 */
		public function edit_league_fixtures($number, $scheduled, $id_league)
		{
			global $wpdb;
			
			return
			$wpdb->update( 
				$wpdb->fixture, 
				array('scheduled' => $scheduled), array('id_league' => $id_league, 'number' => $number), 
				array('%s'), array('%d', '%d') 
			);
		}
		
		/**
		 * Get league's fixtures
	 	 *
		 * @param  integer $number
		 * @param  integer $id_league
		 * @param  boolean $with_game
		 * @return object
     	 */
		public function get_fixture_id($number, $id_league, $with_game = TRUE)
		{
			global $wpdb;
			
			if ($with_game)
			{
				return $wpdb->get_results($wpdb->prepare("SELECT f.id as fixture_id
	                FROM $wpdb->fixture f, $wpdb->match g
	                WHERE f.id = g.id_fixture
	                AND f.number = %d
	                AND f.id_league = %d",
					$number,
					$id_league)
				);
			}
			else
			{
				return $wpdb->get_var($wpdb->prepare("SELECT id
	                FROM $wpdb->fixture
	                WHERE number = %d
	                AND id_league = %d",
					$number,
					$id_league)
				);
			} 
		}
		
		/**
		 * Edit match played
	 	 *
		 * @param  integer $played
		 * @param  integer $id_fixture
		 * @return object
     	 */
		public function edit_game_datetime($played, $id_fixture)
		{
			global $wpdb;
			
			return $wpdb->update($wpdb->match, array('played' => $played), array('id_fixture' => $id_fixture), array('%s'), array('%d'));
		}
		
		/**
	     * Select league's settings
		 *
		 * @param  integer  $id_league
	     * @return object
	     */
	    public function get_league_settings($id_league)
	    {
			global $wpdb;
            
            return $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->league WHERE id = %d", $id_league));
	    }

		/**
	     * Is league's settings in database?
		 *
		 * @param  integer $id_league
	  	 * @return boolean
	 	 */
		public function is_league_setting_in_db($id_league)
		{
			global $wpdb;
						
			$exist = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->league WHERE id = %d", $id_league));
			
			// We didn't find a row
			if ($exist == 0)
				return FALSE;	
			else
				return TRUE;
		}

		/**
		 * Edit league's setting
	 	 *
		 * @param  string  $name
		 * @param  integer $year
		 * @param  integer $id_league
		 * @param  integer $victory
		 * @param  integer $draw
		 * @param  integer $defeat
		 * @param  integer $promotion
		 * @param  integer $qualifying
		 * @param  integer $relegation
		 * @param  integer $id_favorite
		 * @param  integer $nb_leg
         * @param  string  $team_link
         * @param  string  $default_time
		 * @return object
     	 */
		public function update_league_settings($name, $year, $id_league, $victory, $draw, $defeat, $promo, $qualif, $releg, $favorite, $nb_leg, $team_link, $default_time)
		{
			global $wpdb;
			
			return
			$wpdb->update( 
				$wpdb->league,
				array(
					'name'  	     => $name,
					'year' 	  	     => $year,
					'pt_victory'     => $victory,
					'pt_draw' 	     => $draw,
					'pt_defeat'      => $defeat,
					'promotion'      => $promo,
					'qualifying'     => $qualif,
					'relegation'     => $releg,
					'id_favorite'    => $favorite,
					'nb_leg'	     => $nb_leg,
					'team_link'      => $team_link,
					'default_time'   => $default_time
				), 
				array('id' => $id_league),
				array(
					'%s',
					'%d',
					'%d',
					'%d',
					'%d',
					'%d',
					'%d',
					'%d',
					'%d',
					'%d',
					'%s',
					'%s'
				), 
				array('%d')
			);
		}
		
		/**
	     * Get distinct league's team
		 *
		 * @param  integer $id_league
	     * @return object
	     */
	    public function get_distinct_league_team($id_league)
	    {
			global $wpdb;
            
            return $wpdb->get_results($wpdb->prepare("SELECT DISTINCT c.name, t.id as club_id, penalty
				FROM $wpdb->team t, $wpdb->club c
				WHERE t.id_league = %d
				AND c.id = t.id_club
				ORDER BY c.name",
				$id_league)
			);
	    }
	
		/**
	     * Update bonus malus
		 *
		 * @param  integer  $penalty
		 * @param  integer  $id_team
	     * @return object
	     */
		public function edit_bonus_malus($penalty, $id_team)
		{			
			global $wpdb;
			
			return
			$wpdb->update( 
				$wpdb->team,
				array('penalty' => $penalty), 
				array('id' => $id_team),
				array('%d'), 
				array('%d')
			);
		}

		/**
	     * Get matches by fixture
		 *
		 * @param  integer	$id_fixture
		 * @param  integer	$counter
	     * @return object
	     */
		public function get_matches_by_fixture($id_fixture, $counter)
		{
			global $wpdb;
            
            return $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->match WHERE id_fixture = %d LIMIT %d, 1", $id_fixture, $counter));
		}
		
		/**
	     * Remove matches from a fixture
		 *
		 * @param  integer $id_fixture
	     * @return object
	     */
		public function remove_matches_from_fixture($id_fixture)
		{
			global $wpdb;
			
			return $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->match WHERE id_fixture = %d", $id_fixture));
		}
		
		/**
	     * Add matches to a fixture
		 *
		 * @param  integer	$id_fixture
		 * @param  integer	$id_home
		 * @param  integer	$id_away
	     * @return object
	     */
		public function add_matches_to_fixture($id_fixture, $id_home, $id_away)
		{
			global $wpdb;
			
			return
			$wpdb->insert( 
				$wpdb->match,
				array(
					'id_fixture'   => $id_fixture,
					'id_team_home' => $id_home,
					'id_team_away' => $id_away
				), 
				array(
					'%d',
					'%d',
					'%d'
				)
			);
		}
		
		/**
	     * Get results by fixture
		 *
		 * @param  integer	$fixture
		 * @param  integer	$id_league
	     * @return object
	     */
		public function get_results_by_fixture($fixture, $id_league)
		{
			global $wpdb;
            
            return $wpdb->get_results($wpdb->prepare(
				"SELECT c.name as name_home, caway.name as name_away, m.goal_home, m.goal_away, m.id as match_id, m.played
	 			FROM $wpdb->club c, $wpdb->club caway, $wpdb->match m, $wpdb->fixture f, $wpdb->team t, $wpdb->team taway
				WHERE c.id = t.id_club
				AND caway.id = taway.id_club
				AND t.id = m.id_team_home
				AND taway.id = m.id_team_away
				AND m.id_fixture = f.id
				AND f.number = %d
				AND f.id_league = %d
				ORDER BY m.played ASC",
				$fixture,
				$id_league)
			);
		}
		
		/**
	     * Update results
		 *
		 * @param  integer  $goal_home
		 * @param  integer  $goal_away
		 * @param  string   $played
		 * @param  integer  $match_id
	     * @return object
	     */
		public function update_results($goal_h, $goal_a, $played, $match_id)
		{
			global $wpdb;
			
			// WordPress doesn't handle properly to pass a null into the database
			if ($goal_h === NULL || $goal_a === NULL)
				$goal_a = $goal_h = 'NULL';
			
			return $wpdb->query("UPDATE $wpdb->match
				SET goal_home = $goal_h, goal_away = $goal_a, played = '$played'
				WHERE id = $match_id");
		}
		
		/**
	     * Count how many fixtures already played in a league
		 *
		 * @param  integer $id_league
	     * @return integer
	     */
		public function get_max_fixtures_played($id_league)
		{
			global $wpdb;
            
            return $wpdb->get_var($wpdb->prepare("SELECT MAX(f.number)
	 			FROM $wpdb->fixture f, $wpdb->match m
				WHERE f.id = m.id_fixture
				AND m.goal_home IS NOT NULL
				AND f.id_league = %d",
				$id_league)
			);
		}
		
		/**
	     * Get information we need to fill in the league table
		 *
		 * @param  integer $id_league
	     * @return object
	     */
		public function get_teams_information_table($id_league)
		{
			global $wpdb;
            
            return $wpdb->get_results($wpdb->prepare("SELECT c.name, t.penalty, t.id as team_id
				FROM $wpdb->team t, $wpdb->club c, $wpdb->league l
				WHERE t.id_league = l.id
				AND l.id = %d
				AND t.id_club = c.id",
				$id_league)
			);
		}
		
		/**
	     * Fill-in data in the league table
		 *
		 * @param  integer	$id_league
		 * @param  integer	$start
		 * @param  integer	$fixture
		 * @param  integer	$nb_clubs
		 * @param  integer	$pt_victory
		 * @param  integer	$pt_draw
		 * @param  integer	$pt_defeat
	     * @return object
	     */
		public function fill_league_table($id_league, $start, $fixture, $nb_clubs, $pt_v, $pt_d, $pt_l)
		{
			global $wpdb;
			
			$self = new PHPLeague_Database();
			
			// Delete old data
			$wpdb->query("DELETE FROM $wpdb->table_cache WHERE id_league = $id_league");

			if ( ! $fixture)
				$fixture = ($nb_clubs * 2) - 2;

			if ( ! $start)
				$start = 1;

			// Get settings
			$pt_victory = $pt_v;
			$pt_draw    = $pt_d;
			$pt_defeat  = $pt_l;

			// Home victory
			$query =
				"SELECT t_home.id, COUNT(t_home.id) as count_home_id, c.name, SUM(g.goal_home) as g_home, SUM(g.goal_away) as g_away
					FROM $wpdb->team t_home, $wpdb->club c, $wpdb->match g, $wpdb->fixture d, $wpdb->league l
					WHERE t_home.id_league = $id_league
					AND t_home.id_club = c.id
					AND t_home.id = g.id_team_home
					AND g.goal_home > g.goal_away
					AND l.id = d.id_league
					AND d.id = g.id_fixture
					AND d.number >= $start
					AND d.number <= $fixture
					GROUP BY c.name";

			foreach ($wpdb->get_results($wpdb->prepare($query)) as $row)
			{
				$name = trim($row->name);
				$table[$name]['home_v'] = $row->count_home_id;

				if ( ! isset($table[$name]['home_g_for']))
					$table[$name]['home_g_for'] = $row->g_home;
				else
					$table[$name]['home_g_for'] += $row->g_home;

				if ( ! isset($table[$name]['home_g_against']))
					$table[$name]['home_g_against'] = $row->g_away;
				else
					$table[$name]['home_g_against'] += $row->g_away;
			}

			// Home defeat
			$query = 
				"SELECT t_home.id, COUNT(t_home.id) as count_home_id, c.name, SUM(g.goal_home) as g_home, SUM(g.goal_away) as g_away
					FROM $wpdb->team t_home, $wpdb->club c, $wpdb->match g, $wpdb->fixture d, $wpdb->league l
					WHERE t_home.id_league = $id_league
					AND t_home.id_club = c.id
					AND t_home.id = g.id_team_home
					AND g.goal_home < g.goal_away
					AND l.id = d.id_league
					AND d.id = g.id_fixture
					AND d.number >= $start
					AND d.number <= $fixture
					GROUP BY c.name";

			foreach ($wpdb->get_results($wpdb->prepare($query)) as $row)
			{
				$name = trim($row->name);
				$table[$name]['home_l'] = $row->count_home_id;

				if ( ! isset($table[$name]['home_g_for']))
					$table[$name]['home_g_for'] = $row->g_home;
				else
					$table[$name]['home_g_for'] += $row->g_home;

				if ( ! isset($table[$name]['home_g_against']))
					$table[$name]['home_g_against'] = $row->g_away;
				else
					$table[$name]['home_g_against'] += $row->g_away;
			}

			// Home draw
			$query = 
				"SELECT t_home.id, COUNT(t_home.id) as count_home_id, c.name, SUM(g.goal_home) as g_home, SUM(g.goal_away) as g_away
					FROM $wpdb->team t_home, $wpdb->club c, $wpdb->match g, $wpdb->fixture d, $wpdb->league l
					WHERE t_home.id_league = $id_league
					AND t_home.id_club = c.id
					AND t_home.id = g.id_team_home
					AND g.goal_home = g.goal_away
					AND g.goal_home IS NOT NULL
					AND g.goal_away IS NOT NULL
					AND l.id = d.id_league
					AND d.id = g.id_fixture
					AND d.number >= $start
					AND d.number <= $fixture
					GROUP BY c.name";

			foreach ($wpdb->get_results($wpdb->prepare($query)) as $row)
			{
				$name = trim($row->name);
				$table[$name]['home_d'] = $row->count_home_id;

				if ( ! isset($table[$name]['home_g_for']))
					$table[$name]['home_g_for'] = $row->g_home;
				else
					$table[$name]['home_g_for'] += $row->g_home;

				if ( ! isset($table[$name]['home_g_against']))
					$table[$name]['home_g_against'] = $row->g_away;
				else
					$table[$name]['home_g_against'] += $row->g_away;
			}

			// Away victory
			$query = 
				"SELECT t_away.id, COUNT(t_away.id) as count_away_id, c.name, SUM(g.goal_home) as g_home, SUM(g.goal_away) as g_away
					FROM $wpdb->team t_away, $wpdb->club c, $wpdb->match g, $wpdb->fixture d, $wpdb->league l
					WHERE t_away.id_league = $id_league
					AND t_away.id_club = c.id
					AND t_away.id = g.id_team_away
					AND g.goal_away > g.goal_home
					AND l.id = d.id_league
					AND d.id = g.id_fixture
					AND d.number >= $start
					AND d.number <= $fixture
					GROUP BY c.name";

			foreach ($wpdb->get_results($wpdb->prepare($query)) as $row)
			{
				$name = trim($row->name);
				$table[$name]['away_v'] = $row->count_away_id;

				if ( ! isset($table[$name]['away_g_for']))
					$table[$name]['away_g_for'] = $row->g_away;
				else
					$table[$name]['away_g_for'] += $row->g_away;

				if ( ! isset($table[$name]['away_g_against']))
					$table[$name]['away_g_against'] = $row->g_home;
				else
					$table[$name]['away_g_against'] += $row->g_home;
			}

			// Away defeat
			$query = 
				"SELECT t_away.id, COUNT(t_away.id) as count_away_id, c.name, SUM(g.goal_home) as g_home, SUM(g.goal_away) as g_away
					FROM $wpdb->team t_away, $wpdb->club c, $wpdb->match g, $wpdb->fixture d, $wpdb->league l
					WHERE t_away.id_league = $id_league
					AND t_away.id_club = c.id
					AND t_away.id = g.id_team_away
					AND g.goal_away < g.goal_home
					AND l.id = d.id_league
					AND d.id = g.id_fixture
					AND d.number >= $start
					AND d.number <= $fixture
					GROUP BY c.name";

			foreach ($wpdb->get_results($wpdb->prepare($query)) as $row)
			{
				$name = trim($row->name);
				$table[$name]['away_l'] = $row->count_away_id;

				if ( ! isset($table[$name]['away_g_for']))
					$table[$name]['away_g_for'] = $row->g_away;
				else
					$table[$name]['away_g_for'] += $row->g_away;

				if ( ! isset($table[$name]['away_g_against']))
					$table[$name]['away_g_against'] = $row->g_home;
				else
					$table[$name]['away_g_against'] += $row->g_home;
			}

			// Away draw
			$query = 
				"SELECT t_away.id, COUNT(t_away.id) as count_away_id, c.name, SUM(g.goal_home) as g_home, SUM(g.goal_away) as g_away
					FROM $wpdb->team t_away, $wpdb->club c, $wpdb->match g, $wpdb->fixture d, $wpdb->league l
					WHERE t_away.id_league = $id_league
					AND t_away.id_club = c.id
					AND t_away.id = g.id_team_away
					AND g.goal_away = g.goal_home
					AND g.goal_away IS NOT NULL
					AND g.goal_home IS NOT NULL
					AND l.id = d.id_league
					AND d.id = g.id_fixture
					AND d.number >= $start
					AND d.number <= $fixture
					GROUP BY c.name";

			foreach ($wpdb->get_results($wpdb->prepare($query)) as $row)
			{
				$name = trim($row->name);
				$table[$name]['away_d'] = $row->count_away_id;

				if ( ! isset($table[$name]['away_g_for']))
					$table[$name]['away_g_for'] = $row->g_away;
				else
					$table[$name]['away_g_for'] += $row->g_away;

				if ( ! isset($table[$name]['away_g_against']))
					$table[$name]['away_g_against'] = $row->g_home;
				else
					$table[$name]['away_g_against'] += $row->g_home;
			}

			// Get all the data we need to fill in the table
			foreach ($self->get_teams_information_table($id_league) as $row)
			{			
				$name 			= trim($row->name);
				$home_victory 	= (isset($table[$name]['home_v']) ? $table[$name]['home_v'] : '');
				$home_draw	 	= (isset($table[$name]['home_d']) ? $table[$name]['home_d'] : '');
				$home_defeat 	= (isset($table[$name]['home_l']) ? $table[$name]['home_l'] : '');
				$away_victory 	= (isset($table[$name]['away_v']) ? $table[$name]['away_v'] : '');
				$away_draw	 	= (isset($table[$name]['away_d']) ? $table[$name]['away_d'] : '');
				$away_defeat 	= (isset($table[$name]['away_l']) ? $table[$name]['away_l'] : '');
				$away_g_for 	= (isset($table[$name]['away_g_for']) ? $table[$name]['away_g_for'] : '');
				$home_g_for	 	= (isset($table[$name]['home_g_for']) ? $table[$name]['home_g_for'] : '');
				$away_g_aga 	= (isset($table[$name]['away_g_against']) ? $table[$name]['away_g_against'] : '');
				$home_g_aga 	= (isset($table[$name]['home_g_against']) ? $table[$name]['home_g_against'] : '');
				
				$home_played	= $home_victory + $home_draw + $home_defeat;
				$away_played	= $away_victory + $away_draw + $away_defeat;
				$played			= $home_played + $away_played;
				$home_pts		= ($home_victory * $pt_victory) + ($home_draw * $pt_draw) + ($home_defeat * $pt_defeat);
				$away_pts		= ($away_victory * $pt_victory) + ($away_draw * $pt_draw) + ($away_defeat * $pt_defeat);
				$points			= $home_pts + $away_pts + $row->penalty;
				$nb_victory		= $home_victory + $away_victory;
				$nb_draw		= $home_draw + $away_draw;
				$nb_defeat		= $home_defeat + $away_defeat;
				$home_v			= $home_victory;
				$home_d			= $home_draw;
				$home_l			= $home_defeat;
				$away_v			= $away_victory;
				$away_d			= $away_draw;
				$away_l			= $away_defeat;
				$goal_for		= $away_g_for + $home_g_for;
				$home_g_for		= $home_g_for;
				$away_g_for		= $away_g_for;
				$goal_against	= $home_g_aga + $away_g_aga;
				$home_g_against	= $home_g_aga;
				$away_g_against	= $away_g_aga;
				$diff			= $goal_for - $goal_against;
				$home_diff		= $home_g_for - $home_g_against;
				$away_diff		= $away_g_for - $away_g_against;

				$wpdb->insert( 
					$wpdb->table_cache,
					array(
						'club_name'   	 => $name,
						'id_team' 	  	 => $row->team_id,
						'id_league'   	 => $id_league,
						'points' 	  	 => $points,
						'home_points' 	 => $home_pts,
						'away_points' 	 => $away_pts,
						'played' 	  	 => $played,
						'home_played' 	 => $home_played,
						'away_played' 	 => $away_played,
						'victory' 	  	 => $nb_victory,
						'draw' 		  	 => $nb_draw,
						'defeat' 	  	 => $nb_defeat,
						'home_v' 	   	 => $home_v,
						'home_d'	  	 => $home_d,
						'home_l' 	 	 => $home_l,
						'away_v' 	 	 => $away_v,
						'away_d' 	 	 => $away_d,
						'away_l' 	  	 => $away_l,
						'goal_for' 	  	 => $goal_for,
						'goal_against'	 => $goal_against,
						'home_g_for'  	 => $home_g_for,
						'home_g_against' => $home_g_against,
						'away_g_for' 	 => $away_g_for,
						'away_g_against' => $away_g_against,
						'diff' 	  		 => $diff,
						'home_diff' 	 => $home_diff,
						'away_diff' 	 => $away_diff,
						'pen'			 => $row->penalty
					),
					array(
						'%s',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d'						
					)
				);
			}
		}

		/**
	     * Get every country
		 * 
		 * @param  integer $offset
		 * @param  integer $limit
		 * @param  string  $order
	     * @return object
	     */
	    public function get_every_country($offset = 0, $limit = 250, $order = 'DESC')
	    {
			global $wpdb;
            
            return $wpdb->get_results($wpdb->prepare("SELECT id, name
				FROM $wpdb->country
				ORDER BY name $order
				LIMIT %d, %d", $offset, $limit));
	    }
		
		/**
	     * Verify if a country is unique
		 *
		 * @param  integer $id
	  	 * @return bool
	 	 */
		public function is_country_unique($id)
		{
			global $wpdb;
			
			$exist = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->country WHERE id = %d", $id));
			
			// We didn't find a row
			if ($exist == 0)
                return TRUE;    
            else
                return FALSE;
		}

		/**
	     * Get country name from an ID
		 *
		 * @param  integer $id
	  	 * @return string
	 	 */
		public function get_country_name_from_id($id)
		{
			global $wpdb;
            
            return $wpdb->get_var($wpdb->prepare("SELECT name FROM $wpdb->country WHERE id = %d", $id));
		}
				
		/**
	     * Get league table data
		 *
		 * This method gives us all the data we need in order
		 * to build the table for a selected league.
		 *
		 * @param  string	$type
		 * @param  integer	$id_league
		 * @param  integer	$nb_teams
	     * @return object
	     */
		public function get_league_table_data($type, $id_league, $nb_teams)
		{
			global $wpdb;
			
			switch ($type) { // Not good for performance to have "*" in the select...
				case 'general':
				default :
					$query = "SELECT * FROM $wpdb->table_cache c LEFT JOIN $wpdb->team t ON t.id = c.id_team LEFT JOIN $wpdb->club a ON a.id = t.id_club WHERE c.id_league = $id_league ORDER BY c.points DESC, c.diff DESC, c.goal_for DESC, c.goal_against ASC, c.club_name ASC LIMIT $nb_teams";
					break;
				case 'home':
					$query = "SELECT * FROM $wpdb->table_cache c LEFT JOIN $wpdb->team t ON t.id = c.id_team LEFT JOIN $wpdb->club a ON a.id = t.id_club WHERE c.id_league = $id_league ORDER BY c.home_points DESC, c.home_diff DESC LIMIT $nb_teams";
					break;
				case 'away':
					$query = "SELECT * FROM $wpdb->table_cache c LEFT JOIN $wpdb->team t ON t.id = c.id_team LEFT JOIN $wpdb->club a ON a.id = t.id_club WHERE c.id_league = $id_league ORDER BY c.away_points DESC, c.away_diff DESC LIMIT $nb_teams";
					break;
			}
			
			return $wpdb->get_results($wpdb->prepare($query));
		}
		
		/**
	     * Get all fixtures by league and team
		 *
		 * This method gives us the possibility to get all the fixtures
		 * for a selected team in a league.
		 *
		 * @param  integer	$id_team
		 * @param  integer	$id_league
	     * @return object
	     */
		public function get_league_fixtures_by_team($id_team, $id_league)
		{
			global $wpdb;
			
			return $wpdb->get_results($wpdb->prepare("SELECT d.number, clhome.name as home_name, claway.name as away_name, g.goal_home, g.goal_away,
			g.played, g.id as game_id, g.id_team_home, g.id_team_away
				FROM $wpdb->team home, $wpdb->team away, $wpdb->match g, $wpdb->fixture d, $wpdb->club clhome, $wpdb->club claway
				WHERE g.id_team_home = home.id
				AND g.id_team_away = away.id
				AND (g.id_team_away = %d
					OR g.id_team_home = %d)
				AND d.id_league = %d
				AND home.id_club = clhome.id
				AND away.id_club = claway.id
				AND g.id_fixture = d.id
				ORDER BY d.number ASC",
				$id_team, $id_team, $id_league));
		}
		
		/**
	     * Check if a team is already in a league
		 *
		 * Use to check if we can use the team selected by the user
		 * or use the favorite team from the setting table.
		 *
		 * @param  integer	$id_league
		 * @param  integer	$id_team
	     * @return bool
	     */
		public function is_team_already_in_league($id_league, $id_team)
		{
			global $wpdb;
						
			$exist = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->team WHERE id_league = %d AND id = %d",
				$id_league,
				$id_team)
			);
			
			// We didn't find a row
			if ($exist == 0)
                return TRUE;    
            else
                return FALSE;
		}
	}
}