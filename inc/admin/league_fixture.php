<?php

/*
 * This file is part of the PHPLeague package.
 *
 * (c) Maxime Dizerens <mdizerens@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if ($db->is_league_exists($id_league) === FALSE)
    wp_die(__('We did not find the league in the database.', 'phpleague'));

// Vars
$league_name = $db->return_league_name($id_league);
$setting     = $db->get_league_settings($id_league);
$nb_teams    = intval($setting->nb_teams);
$nb_legs     = intval($setting->nb_leg);
$output      = '';
$data        = array();
$menu        = array(
    __('Teams', 'phpleague')    => admin_url('admin.php?page=phpleague_overview&option=team&id_league='.$id_league),
    __('Fixtures', 'phpleague') => '#',
    __('Matches', 'phpleague')  => admin_url('admin.php?page=phpleague_overview&option=match&id_league='.$id_league),
    __('Results', 'phpleague')  => admin_url('admin.php?page=phpleague_overview&option=result&id_league='.$id_league),
    __('Settings', 'phpleague') => admin_url('admin.php?page=phpleague_overview&option=setting&id_league='.$id_league)
);

if (($nb_teams % 2) != 0)
    $message[] = __('Be aware that your league has an odd number of teams.', 'phpleague');  

if (isset($_POST['fixtures']) && check_admin_referer('phpleague')) {
    $schedule = ( ! empty($_POST['schedule']) && is_array($_POST['schedule'])) ? $_POST['schedule'] : NULL;
    
    if ($schedule === NULL) {
        $message[] = __('The fixtures format is not good!', 'phpleague');   
    } else {
        foreach ($schedule as $key => $scheduled) {
            // We don't want to start from zero
            $number = $key + 1;

            // Add the new fixtures in the db
            $db->edit_league_fixtures($number, $scheduled, $id_league);

            // We update the match datetime using the default value
            foreach ($db->get_fixture_id($number, $id_league) as $row) {
                $db->edit_game_datetime($scheduled.' '.$setting->default_time, $row->fixture_id);
            }
        }
        
        $message[] = __('Fixtures updated successfully!', 'phpleague');
    }
}

if ($nb_teams == 0 || $nb_teams == 1) {
    $message[] = __('It seems that '.$league_name.' has no team registered or only one.', 'phpleague');
} else {   
    $output = $fct->form_open(admin_url('admin.php?page=phpleague_overview&option=fixture&id_league='.$id_league));
    
    // Count the number of fixture per league
    $nb_fixtures = $db->nb_fixtures_league($id_league);
    
    if (($nb_teams % 2) != 0)
        $fixtures_number = $nb_teams * $nb_legs;
    else
        $fixtures_number = ($nb_teams * $nb_legs) - $nb_legs;

    // Security check
    if ($nb_fixtures != $fixtures_number) {
        $db->remove_fixtures_league($id_league);

        $number = 1;
        while ($number <= $fixtures_number) {
            $db->add_fixtures_league($number, $id_league);
            $number++;
        }
    }
    
    // Vars useful to get things sorted
    $column = 1;
    $first = $second = $third = '';
    
    $output .= '<p>'.$fct->input('fixtures', __('Save', 'phpleague'), array('type' => 'submit', 'class' => 'button')).'</p>';
            
    foreach ($db->get_fixtures_league($id_league) as $key => $row) {
        $col  = '<label for="schedule['.$key.']">'.__('Fixture: ', 'phpleague').esc_html($row->number).'</label>';
        $col .= $fct->input('schedule['.$key.']', esc_html($row->scheduled), array('size' => '10', 'tabindex' => $key + 1, 'id' => 'schedule['.$key.']'));

        switch ($column) {
            case 1 :
                $first .= $col;
                $column = 2;
                break;
            case 2 :
                $second .= $col;
                $column  = 3;
                break;
            case 3 :
                $third  .= $col;
                $column  = 1;
                break;
        }
    }
    
    $first   = $ctl->admin_wrapper(32, $first);
    $second  = $ctl->admin_wrapper(32, $second);
    $third   = $ctl->admin_wrapper(32, $third);
    $output .= $first.$second.$third;
    $output .= $fct->form_close();
}

$data[] = array(
    'menu'  => __('Fixtures', 'phpleague'),
    'title' => __('Fixtures of ', 'phpleague').$league_name,
    'text'  => $output,
    'class' => 'full'
);

// Show everything...
echo $ctl->admin_container($menu, $data, $message);