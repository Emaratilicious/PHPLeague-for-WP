<?php

/*
 * This file is part of the PHPLeague package.
 *
 * (c) Maxime Dizerens <mdizerens@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Get ID fixture
$get_id_fixture = ( ! empty($_GET['id_fixture']) && $db->is_fixture_exists($_GET['id_fixture']) === TRUE)
    ? (int) $_GET['id_fixture'] : 1;

// Security
if ($db->is_league_exists($id_league) === FALSE)
    wp_die(__('We did not find the league in the database.', 'phpleague'));

// Variables
$league_name = $db->return_league_name($id_league);
$setting     = $db->get_league_settings($id_league);
$nb_teams    = (int) $setting->nb_teams;
$nb_legs     = (int) $setting->nb_leg;
$page_url    = 'admin.php?page=phpleague_overview&option=match&id_league='.$id_league.'&id_fixture='.$get_id_fixture;
$output      = '';
$data        = array();
$menu        = array(
    __('Teams', 'phpleague')    => admin_url('admin.php?page=phpleague_overview&option=team&id_league='.$id_league),
    __('Fixtures', 'phpleague') => admin_url('admin.php?page=phpleague_overview&option=fixture&id_league='.$id_league),
    __('Matches', 'phpleague')  => '#',
    __('Results', 'phpleague')  => admin_url('admin.php?page=phpleague_overview&option=result&id_league='.$id_league),
    __('Settings', 'phpleague') => admin_url('admin.php?page=phpleague_overview&option=setting&id_league='.$id_league)
);

// Check what kind of fixtures we are dealing with (odd/even)
if (($nb_teams % 2) != 0)
{
    $nb_fixtures = $nb_teams * $nb_legs;
    $nb_matches  = ($nb_teams - 1) / 2;
}
else
{
    $nb_fixtures = ($nb_teams * $nb_legs) - $nb_legs;
    $nb_matches  = ($nb_teams / 2);
}

// Data processing...
if (isset($_POST['matches']) && check_admin_referer('phpleague'))
{
    // Secure data
    $id_fixture = ( ! empty($_POST['id_fixture'])) ? (int) $_POST['id_fixture'] : 0;
    $id_home    = ( ! empty($_POST['id_home']) && is_array($_POST['id_home'])) ? $_POST['id_home'] : NULL;
    $id_away    = ( ! empty($_POST['id_away']) && is_array($_POST['id_away'])) ? $_POST['id_away'] : NULL;
    
    if ($id_fixture === 0)
    {
        $message[] = __('An error occurred with the fixture ID.', 'phpleague');
    }
    elseif ($id_home === NULL || $id_away === NULL)
    {
        $message[] = __('An error occurred because of the datatype given.', 'phpleague');
    }
    else
    {
        // Remove all previous data
        $db->remove_matches_from_fixture($id_fixture);

        // Array containing the teams to avoid duplicate
        $array = array();
        
        // Insert new data
        for ($counter = 0; $counter < $nb_matches; $counter++)
        {
            // We cannot have the same team twice
            if ($id_home[$counter] == $id_away[$counter])
            {
                $message[] = __('You cannot have the same team at home and away.', 'phpleague');
                break;
            }
            elseif (in_array($id_home[$counter], $array) || in_array($id_away[$counter], $array))
            {
                $message[] = __('You cannot have the same team twice in a fixture.', 'phpleague');
                break;
            }

            $db->add_matches_to_fixture($id_fixture, (int) $id_home[$counter], (int) $id_away[$counter]);

            // Add the teams into the array to check them later...
            $array[] = $id_home[$counter];
            $array[] = $id_away[$counter];
        }
        
        $message[] = __('Match(es) updated successfully.', 'phpleague');
    }
}

$pagination = $fct->pagination($nb_fixtures, 1, $get_id_fixture, 'id_fixture');
$output .= $fct->form_open(admin_url($page_url));
$output .= '<div class="tablenav"><div class="alignleft actions">'.$fct->input('matches', __('Save', 'phpleague'),
        array('type' => 'submit', 'class' => 'button')).'</div>';

if ($pagination)
    $output .= '<div class="tablenav-pages">'.$pagination.'</div>';

// Check if the fixture exists in matches table
$id_fixture = $db->get_fixture_id($get_id_fixture, $id_league, FALSE);
$i = $team_home = $team_away = 0;
$output .= '</div><table class="widefat"><thead><tr><th colspan="2">'.$league_name.
        __(' - Fixture: ', 'phpleague').$get_id_fixture.'</th></tr>'
        .'<tr><th class="text-centered">'.__('Home', 'phpleague').'</th><th class="text-centered">'
        .__('Away', 'phpleague').'</th></tr></thead>';

foreach ($db->get_distinct_league_team($id_league) as $array)
{
    $clubs_list[$array->club_id] = esc_html($array->name);  
}

// Matches
for ($counter = $nb_matches; $counter > 0; $counter = $counter - 1)
{
    // Get teams ID
    foreach ($db->get_matches_by_fixture($id_fixture, $counter - 1) as $row)
    {
        $team_home = (int) $row->id_team_home;
        $team_away = (int) $row->id_team_away;
    }

    // Home matches
    $output .= '<tr '.$fct->alternate('', 'class="alternate"').'><td>';
    $output .= $fct->select('id_home[]', $clubs_list, $team_home, array('style' => 'width: 100%;'));
    $output .= '</td>';

    // Away matches
    $output .= '<td>';
    $output .= $fct->select('id_away[]', $clubs_list, $team_away, array('style' => 'width: 100%;'));
    $output .= '</td></tr>';

    $i++;
}

$output .= '</table>'.$fct->input('id_fixture', $id_fixture, array('type' => 'hidden')).$fct->form_close();
$data[]  = array(
    'menu'  => __('Matches', 'phpleague'),
    'title' => __('Matches of ', 'phpleague').$league_name,
    'text'  => $output,
    'class' => 'full'
);

// Render the page
echo $ctl->admin_container($menu, $data, $message);