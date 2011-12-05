<?php

/*
 * This file is part of the PHPLeague package.
 *
 * (c) Maxime Dizerens <mdizerens@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if ( ! class_exists('PHPLeague_Front')) {
    
    /**
     * Manage the PHPLeague front-end.
     *
     * @category   Front
     * @package    PHPLeague
     * @author     Maxime Dizerens
     * @copyright  (c) 2011 Mikaweb Design, Ltd
     */
    class PHPLeague_Front {

        /**
         * Constructor
         *
         * @param  none
         * @return void
         */
        public function __construct() {}

        /**
         * Add the front css
         *
         * @param  none
         * @return void
         */
        public static function print_front_styles()
        {
            wp_register_style('phpleague-front', plugins_url('phpleague/assets/css/phpleague-front.css'));
            wp_enqueue_style('phpleague-front');
        }

        /**
         * Show the league ranking table.
         *
         * @param  integer $id_league
         * @param  string  $style
         * @param  string  $last_results
         * @return string
         */
        public function get_league_table($id_league, $style = 'general', $last_results = 'true')
        {
            global $wpdb;
            $db = new PHPLeague_Database;

            // League not found in the database
            if ($db->is_league_exists($id_league) === FALSE)
                return;

            $setting    = $db->get_league_settings($id_league);
            $nb_teams   = (int) $setting->nb_teams;
            $promotion  = (int) $setting->promotion;
            $qualifying = (int) $setting->qualifying + $promotion;
            $favorite   = (int) $setting->id_favorite;
            $relegation = $nb_teams - (int) $setting->relegation;
            $team_links = ($setting->team_link == 'yes') ? TRUE : FALSE;
            
            // We display club information
            if (isset($_GET['club']))
            {
                $front = new PHPLeague_Front;
                echo $front->get_club_information((int) $_GET['club']);
            }

            if ($last_results == 'true')
                $th_latest = '<th>'.__('Last 5 matches', 'phpleague').'</th>';
            else
                $th_latest = '';

            $output = '<table id="phpleague"><thead><tr>'
                    .'<th class="centered">'.__('Pos', 'phpleague').'</th>'
                    .'<th>'.__('Team', 'phpleague').'</th>'
                    .'<th class="centered">'.__('Pts', 'phpleague').'</th>'
                    .'<th class="centered">'.__('P', 'phpleague').'</th>'
                    .'<th class="centered">'.__('W', 'phpleague').'</th>'
                    .'<th class="centered">'.__('D', 'phpleague').'</th>'
                    .'<th class="centered">'.__('L', 'phpleague').'</th>'
                    .'<th class="centered">'.__('F', 'phpleague').'</th>'
                    .'<th class="centered">'.__('A', 'phpleague').'</th>'
                    .'<th class="centered">'.__('+/-', 'phpleague').'</th>'
                    .$th_latest.'<th class="centered"></th></tr></thead><tbody>';

            $place = 1;

            foreach ($db->get_league_table_data($style, $id_league, $nb_teams) as $row)
            {
                // User want to show team links?
                if ($team_links)
                {
                    $permalink = get_permalink();
                    $url       = add_query_arg('club', $row->id_club, $permalink);
                }
                
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

                    if ($style == 'home')
                    {
                        $points       = (int) $row->home_points;
                        $played       = (int) $row->home_played;
                        $victory      = (int) $row->home_v;
                        $draw         = (int) $row->home_d;
                        $defeat       = (int) $row->home_l;
                        $goal_for     = (int) $row->home_g_for;
                        $goal_against = (int) $row->home_g_against;
                        $diff         = (int) $row->home_diff;
                    }
                    elseif ($style == 'away')
                    {
                        $points       = (int) $row->away_points;
                        $played       = (int) $row->away_played;
                        $victory      = (int) $row->away_v;
                        $draw         = (int) $row->away_d;
                        $defeat       = (int) $row->away_l;
                        $goal_for     = (int) $row->away_g_for;
                        $goal_against = (int) $row->away_g_against;
                        $diff         = (int) $row->away_diff;
                    }
                    else
                    {
                        $points       = (int) $row->points;
                        $played       = (int) $row->played;
                        $victory      = (int) $row->victory;
                        $draw         = (int) $row->draw;
                        $defeat       = (int) $row->defeat;
                        $goal_for     = (int) $row->goal_for;
                        $goal_against = (int) $row->goal_against;
                        $diff         = (int) $row->diff;
                    }
                    
                    $output .= '<td class="centered">'.$place.'</td>';
                    
                    // If a logo has been found, we display it!
                    if (is_file(WP_PHPLEAGUE_UPLOADS_PATH.'logo_mini/'.$row->logo_mini))
                    {
                        if (empty($url))
                            $output .= '<td><img src="'.content_url('uploads/phpleague/logo_mini/'.$row->logo_mini).
                                    '" alt="'.esc_html($row->club_name).'" />&nbsp;&nbsp;'.esc_html($row->club_name).'</td>';
                        else
                            $output .= '<td><img src="'.content_url('uploads/phpleague/logo_mini/'.$row->logo_mini).'" alt="'
                                    .esc_html($row->club_name).'" />&nbsp;&nbsp;<a title="'.esc_html($row->club_name).
                                    '" href="'.$url.'">'.esc_html($row->club_name).'</a></td>';
                    }
                    else
                    {
                        if (empty($url))
                            $output .= '<td>'.esc_html($row->club_name).'</td>';
                        else
                            $output .= '<td><a title="'.esc_html($row->club_name).'" href="'.$url.'">'
                                    .esc_html($row->club_name).'</a></td>';
                    }
                    
                    $output .= '<td class="centered">'.$points.'</td>';
                    $output .= '<td class="centered">'.$played.'</td>';
                    $output .= '<td class="centered">'.$victory.'</td>';
                    $output .= '<td class="centered">'.$draw.'</td>';
                    $output .= '<td class="centered">'.$defeat.'</td>';
                    $output .= '<td class="centered">'.$goal_for.'</td>';
                    $output .= '<td class="centered">'.$goal_against.'</td>';
                    $output .= '<td class="centered">'.$diff.'</td>';

                    // Show latest results if enabled
                    if ($last_results == 'true')
                    {
                        // Open the td tag
                        $output .= '<td>';

                        // Get the latest results
                        $results = array_reverse($db->get_latest_results($row->id_team));
                        foreach ($results as $result)
                        {
                            if ($row->id_team == $result->id_team_home)
                            {
                                if ($result->goal_home > $result->goal_away)
                                {
                                    $output .= '<span class="win">'.__('W', 'phpleague').'</span>';
                                }
                                elseif ($result->goal_home < $result->goal_away)
                                {
                                    $output .= '<span class="lose">'.__('L', 'phpleague').'</span>';
                                }
                                elseif ($result->goal_home == $result->goal_away)
                                {
                                    $output .= '<span class="draw">'.__('D', 'phpleague').'</span>';
                                }
                            }
                            elseif ($row->id_team == $result->id_team_away)
                            {
                                if ($result->goal_home < $result->goal_away)
                                {
                                    $output .= '<span class="win">'.__('W', 'phpleague').'</span>';
                                }
                                elseif ($result->goal_home > $result->goal_away)
                                {
                                    $output .= '<span class="lose">'.__('L', 'phpleague').'</span>';
                                }
                                elseif ($result->goal_home == $result->goal_away)
                                {
                                    $output .= '<span class="draw">'.__('D', 'phpleague').'</span>';
                                }
                            }
                        }

                        // Close the td tag
                        $output .= '</td>';
                    }
                    
                    $output .= '<td class="centered">'.$span.'</td></tr>';

                    $place++;
                }
            }

            $output .= '</tbody></table>';
            return $output;
        }

        /**
         * Show one specific fixture.
         *
         * @param  integer $id_fixture
         * @return string
         */
        public function get_league_fixture($id_fixture)
        {
            global $wpdb;
            $db = new PHPLeague_Database;

            // Fixture not found in the database
            if ($db->is_fixture_exists((int) $id_fixture) === FALSE)
                return;

            $output = '<table id="phpleague"><tbody><tr>'
                    .'<th>'.__('Date', 'phpleague').'</th>'
                    .'<th>'.__('Match', 'phpleague').'</th>'
                    .'<th class="centered">'.__('Score', 'phpleague').'</th></tr>';
            
            foreach ($db->get_fixture_results((int) $id_fixture) as $row)
            {
                $output .= '<tr>';
                $output .= '<td>'.strftime("%a %e %b, %H:%M", strtotime($row->played)).'</td>';
                $output .= '<td>'.esc_html($row->home_name).' - '.esc_html($row->away_name).'</td>';

                if (date('Y-m-d') >= $row->played)
                    $output .= '<td class="centered">'.$row->goal_home.' - '.$row->goal_away.'</td>';
                else
                    $output .= '<td class="centered"> - </td>';

                $output .= '</tr>';
            }

            $output .= '</tbody></table>';
            return $output;
        }

        /**
         * Show the league fixtures.
         * Also possible to show only for one particular team.
         *
         * @param  integer $id_league
         * @param  integer $id_team
         * @return string
         */
        public function get_league_fixtures($id_league, $id_team = NULL)
        {
            global $wpdb;
            $db = new PHPLeague_Database;

            // League not found in the database
            if ($db->is_league_exists((int) $id_league) === FALSE)
                return;

            $output = '<table id="phpleague"><tbody><tr>'
                    .'<th>'.__('Date', 'phpleague').'</th>'
                    .'<th class="centered">'.__('Fixture', 'phpleague').'</th>'
                    .'<th>'.__('Match', 'phpleague').'</th>'
                    .'<th class="centered">'.__('Score', 'phpleague').'</th></tr>';
            
            // We got a team...
            if (isset($id_team) && $db->is_team_already_in_league($id_league, $id_team) === FALSE)
            {
                foreach ($db->get_fixtures_by_team((int) $id_team, (int) $id_league) as $row)
                {
                    $output .= '<tr>';
                    $output .= '<td>'.strftime("%a %e %b, %H:%M", strtotime($row->played)).'</td>';
                    $output .= '<td class="centered">'.(int) $row->number.'</td>';
                    $output .= '<td>'.esc_html($row->home_name).' - '.esc_html($row->away_name).'</td>';

                    if (date('Y-m-d') >= $row->played)
                        $output .= '<td class="centered">'.$row->goal_home.' - '.$row->goal_away.'</td>';
                    else
                        $output .= '<td class="centered"> - </td>';

                    $output .= '</tr>';
                }
            }
            else // No team has been given...
            {
                foreach ($db->get_fixtures_by_league((int) $id_league) as $row)
                {
                    $output .= '<tr>';
                    $output .= '<td>'.strftime("%a %e %b, %H:%M", strtotime($row->played)).'</td>';
                    $output .= '<td class="centered">'.(int) $row->number.'</td>';
                    $output .= '<td>'.esc_html($row->home_name).' - '.esc_html($row->away_name).'</td>';

                    if (date('Y-m-d') >= $row->played)
                        $output .= '<td class="centered">'.$row->goal_home.' - '.$row->goal_away.'</td>';
                    else
                        $output .= '<td class="centered"> - </td>';

                    $output .= '</tr>';
                }
            }

            $output .= '</tbody></table>';
            return $output;
        }
        
        /**
         * Show the club information.
         *
         * @param  integer $id
         * @return string
         */
        public function get_club_information($id = NULL)
        {
            global $wpdb;
            $db = new PHPLeague_Database;

            // League not found in the database
            if ($db->is_club_unique($id, 'id') === TRUE)
                return;

            $info = $db->get_club_information($id);
                
            if ($info->creation == 0)
                $creation = '';
            else
                $creation = (int) $info->creation;

            $output  = '<table id="phpleague"><caption>'.esc_html($info->name).__(' Factfile', 'phpleague').'</caption><tbody>';
            $output .= '<tr><th rows="2">'.__('Details', 'phpleague').'</th></tr>';
            $output .= '<tr><td><strong>'.__('Name: ', 'phpleague').'</strong></td><td>'.esc_html($info->name).'</td></tr>';
            $output .= '<tr><td><strong>'.__('Coach: ', 'phpleague').'</strong></td><td>'.esc_html($info->coach).'</td></tr>';
            $output .= '<tr><td><strong>'.__('Venue: ', 'phpleague').'</strong></td><td>'.esc_html($info->venue).'</td></tr>';
            $output .= '<tr><td><strong>'.__('Website: ', 'phpleague').'</strong></td><td><a href="'
                    .esc_html($info->website).'">'.esc_html($info->website).'</a></td></tr>';
            $output .= '<tr><td><strong>'.__('Creation: ', 'phpleague').'</strong></td><td>'.$creation.'</td></tr>';
            $output .= '</tbody></table>';

            return $output;
        }

        /**
         * Show the league ranking as widget.
         *
         * @param  integer $id_league
         * @return string
         */
        public function widget_ranking_table($id_league)
        {
            global $wpdb;
            $db = new PHPLeague_Database;

            // League not found in the database
            if ($db->is_league_exists($id_league) === FALSE)
                return;

            $setting  = $db->get_league_settings($id_league);
            $nb_teams = (int) $setting->nb_teams;
            $favorite = (int) $setting->id_favorite;

            $output = '<table id="phpleague"><thead><tr>
                        <th class="centered">'.__('Pos', 'phpleague').'</th>
                        <th>'.__('Team', 'phpleague').'</th>
                        <th class="centered">'.__('Pts', 'phpleague').'</th>
                        <th class="centered">'.__('P', 'phpleague').'</th>
                        <th class="centered">'.__('+/-', 'phpleague').'</th></tr></thead><tbody>';

            $place = 1;

            foreach ($db->get_league_table_data('general', $id_league, $nb_teams) as $row)
            {                
                if ($place <= $nb_teams)
                {               
                    if ($favorite == $row->id_team)
                        $output .= '<tr class="id-favorite">';
                    else
                        $output .= '<tr>';

                    $points = (int) $row->points;
                    $played = (int) $row->played;
                    $diff   = (int) $row->diff;
                    
                    $output .= '<td class="centered">'.$place.'</td>';
                    $output .= '<td>'.esc_html($row->club_name).'</td>';                    
                    $output .= '<td class="centered">'.$points.'</td>';
                    $output .= '<td class="centered">'.$played.'</td>';
                    $output .= '<td class="centered">'.$diff.'</td></tr>';                    

                    $place++;
                }
            }

            $output .= '</tbody></table>';
            return $output;
        }
    }
}