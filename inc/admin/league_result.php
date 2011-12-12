<?php

/*
 * This file is part of the PHPLeague package.
 *
 * (c) Maxime Dizerens <mdizerens@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// ID Fixture
$id_fixture = ( ! empty($_GET['id_fixture']) && $db->is_fixture_exists($_GET['id_fixture']) === TRUE)
    ? (int) $_GET['id_fixture'] : 1;

if ($db->is_league_exists($id_league) === FALSE)
    wp_die(__('We did not find the league in the database.', 'phpleague'));

// Variables
$league_name = $db->return_league_name($id_league);
$setting     = $db->get_league_settings($id_league);
$nb_teams    = (int) $setting->nb_teams;
$nb_legs     = (int) $setting->nb_leg;
$nb_players  = (int) $setting->nb_starter + (int) $setting->nb_bench;
$page_url    = 'admin.php?page=phpleague_overview&option=result&id_league='.$id_league.'&id_fixture='.$id_fixture;
$output      = '';
$data        = array();
$menu        = array(
    __('Teams', 'phpleague')    => admin_url('admin.php?page=phpleague_overview&option=team&id_league='.$id_league),
    __('Fixtures', 'phpleague') => admin_url('admin.php?page=phpleague_overview&option=fixture&id_league='.$id_league),
    __('Matches', 'phpleague')  => admin_url('admin.php?page=phpleague_overview&option=match&id_league='.$id_league),
    __('Results', 'phpleague')  => '#',
    __('Settings', 'phpleague') => admin_url('admin.php?page=phpleague_overview&option=setting&id_league='.$id_league)
);

// Data processing...
if (isset($_POST['results']) && check_admin_referer('phpleague'))
{
    // Secure data
    $array = ( ! empty($_POST['array'])) ? $_POST['array'] : NULL;

    if (is_array($array))
    {
        foreach ($array as $item)
        {
            if ( ! (($item['goal_home'] == '') || ($item['goal_away'] == '')))
            {
                $db->update_results($item['goal_home'], $item['goal_away'], $item['date'], $item['id_match']);
                $message[] = __('Match "'.$item['id_match'].'" updated successfully with goals.', 'phpleague');
            }
            elseif (($item['goal_home'] == '') || ($item['goal_away'] == ''))
            {
                $db->update_results(NULL, NULL, $item['date'], $item['id_match']);
                $message[] = __('Match "'.$item['id_match'].'" updated successfully without any goals.', 'phpleague');
            }
            else
            {
                $message[] = __('An error occurred during the results generation.', 'phpleague');
            }
        }
    }
    
    // Player mod is enabled, so check if we have anything to add in the database
    if ($setting->player_mod === 'yes')
    {
        // Secure data
        $players = ( ! empty($_POST['players'])) ? $_POST['players'] : 0;

        if (is_array($players))
        {
            foreach ($players as $key => $item)
            {
                // Remove old match data
                $db->remove_players_data_match($key);
                foreach ($item as $row)
                {
                    // TODO - Show a message that we intercept a duplicata try
                    // $key = id_match, $row = id_player_team
                    // Check that the player is real and not already once in the match
                    if ($row > 0 && $db->player_data_already_match($row, $key) === FALSE)
                        $db->add_player_data(255, $row, $key, 1);
                }
            }
        }
    }
}

// Check what kind of number we have (odd/even)
if (($nb_teams % 2) != 0)
    $nb_fixtures = $nb_teams * $nb_legs;
else
    $nb_fixtures = ($nb_teams * $nb_legs) - $nb_legs;

// If the player mod is enabled, we show the toggle button
$button_players = '';
if ($setting->player_mod === 'yes')
    $button_players = $fct->input('show_match', '#', array('type' => 'button', 'class' => 'button show_match'));

$pagination = $fct->pagination($nb_fixtures, 1, $id_fixture, 'id_fixture');
$output    .= $fct->form_open(admin_url($page_url));
$output    .= '<div class="tablenav"><div class="alignleft actions">'
           .$fct->input('results', __('Save', 'phpleague'), array('type' => 'submit', 'class' => 'button')).'</div>';

if ($pagination)
    $output .= '<div class="tablenav-pages">'.$pagination.'</div>'; 

$output .= '</div><table class="widefat"><thead><tr><th colspan="7">'.$league_name.
        __(' - Fixture: ', 'phpleague').$id_fixture.'</th></tr></thead>';
foreach ($db->get_results_by_fixture($id_fixture, $id_league) as $key => $row)
{
    $output .= '<tr><td class="check-column">'.(int) $row->match_id.'</td>'
            .'<td style="text-align:right;">'.esc_html($row->name_home).'</td>';

    $output .= '<td class="check-column">'.$fct->input('array['.$key.'][goal_home]',
            $row->goal_home, array('size' => 2)).'</td>';

    $output .= '<td class="check-column">'.$fct->input('array['.$key.'][goal_away]',
            $row->goal_away, array('size' => 2)).'</td>';

    $output .= '<td>'.esc_html($row->name_away).'</td>';
    $output .= '<td class="check-column">'.$fct->input('array['.$key.'][date]',
            esc_html($row->played), array('size' => 18, 'class' => 'masked-full')).$fct->input('array['.$key.'][id_match]',
            (int) $row->match_id, array('type' => 'hidden')).'</td>';

    $output .= '<td class="check-column">'.$button_players.'</td></tr>';
    
    // If we have the player mode enabled, we show it
    if ($setting->player_mod === 'yes')
    {
        // Get all home players
        $home_players[0] = __('-- Select a player --', 'phpleague');
        foreach ($db->get_players_team($row->home_id) as $player_home)
        {
            $full_name = esc_html($player_home->firstname).' '.esc_html($player_home->lastname);
            $home_players[$player_home->id] = $full_name;
        }
        
        // Get all away players
        $away_players[0] = __('-- Select a player --', 'phpleague');
        foreach ($db->get_players_team($row->away_id) as $player_away)
        {
            $full_name = esc_html($player_away->firstname).' '.esc_html($player_away->lastname);
            $away_players[$player_away->id] = $full_name;
        }
        
        // Variables...
        $select_away = $select_home = '';
        $nb_pl_home  = $nb_pl_away  = $nb_players;
        
        // -- Home Players
        $count_players_home = (count($home_players) - 1);
        if ($count_players_home < $nb_pl_home)
            $nb_pl_home = $count_players_home;

        // Get all players already in the database
        $player_match_home = $db->get_players_by_match($row->match_id, $row->home_id);
        $count_p_home      = count($player_match_home);
        
        // More data in the database than authorized so we killed
        // them all by not showing them :)
        if ($count_p_home > $nb_pl_home)
        {
            for ($i = 1; $i <= $nb_pl_home; $i++)
            {            
                $select_home .= $fct->select('players['.$row->match_id.'][]', $home_players, '');
            }
        }
        else // We got players in the database so we showed them...
        {
            foreach ($player_match_home as $player_m_home)
            {
                $select_home .= $fct->select('players['.$row->match_id.'][]', $home_players, (int) $player_m_home->player_id);
            }
            
            // If we have less players selected than authorized, we show the rest of them...
            if ($count_p_home < $nb_pl_home)
            {
                for ($i = $count_p_home; $i < $nb_pl_home; $i++)
                {            
                    $select_home .= $fct->select('players['.$row->match_id.'][]', $home_players, '');
                }
            }
        }

        // -- Away Players
        $count_players_away = (count($away_players) - 1);
        if ($count_players_away < $nb_pl_away)
            $nb_pl_away = $count_players_away;
        
        // Get all players already in the database and count them
        $player_match_away = $db->get_players_by_match($row->match_id, $row->away_id);
        $count_p_away      = count($player_match_away);
        
        // More data in the database than authorized so we killed
        // them all by not showing them :)
        if ($count_p_away > $nb_pl_away)
        {
            for ($i = 1; $i <= $nb_pl_away; $i++)
            {            
                $select_away .= $fct->select('players['.$row->match_id.'][]', $away_players, '');
            }
        }
        else // We got players in the database so we showed them...
        {
            foreach ($player_match_away as $player_m_away)
            {
                $select_away .= $fct->select('players['.$row->match_id.'][]', $away_players, (int) $player_m_away->player_id);
            }
            
            // If we have less players selected than authorized, we show the rest of them...
            if ($count_p_away < $nb_pl_away)
            {
                for ($i = $count_p_away; $i < $nb_pl_away; $i++)
                {            
                    $select_away .= $fct->select('players['.$row->match_id.'][]', $away_players, '');
                }
            }
        }

        // Render players dropdown list
        $output .= '<tr class="hidden alternate">';
        $output .= '<td class="check-column"></td>';
        $output .= '<td style="text-align:right;">'.$select_home.'</td>';
        $output .= '<td class="check-column"></td>';
        $output .= '<td class="check-column"></td>';
        $output .= '<td>'.$select_away.'</td>';
        $output .= '<td class="check-column"></td>';
        $output .= '<td class="check-column"></td>';
        $output .= '</tr>';
    }
    
    // We remove previous data to use new one in the next iteration...
    unset($count_players_home, $count_players_away, $nb_pl_home, $nb_pl_away, $select_home, $select_away, $home_players, $away_players);
}

$output .= '</table>'.$fct->form_close();
$data[]  = array(
    'menu'  => __('Results', 'phpleague'),
    'title' => __('Results of ', 'phpleague').$league_name,
    'text'  => $output,
    'class' => 'full'
);

// Render the page
echo $ctl->admin_container($menu, $data, $message);