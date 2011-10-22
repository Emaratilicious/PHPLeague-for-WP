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

// Do we have to handle some data?
if (isset($_POST['general']) && check_admin_referer('phpleague')) {
    // Secure vars...
    $time     = (preg_match('/^\d{2}:\d{2}:\d{2}$/', $_POST['time'])) ? trim($_POST['time']) : '17:00:00';
    $victory  = ( ! empty($_POST['pt_victory']))  ? intval($_POST['pt_victory'])  : 0;
    $draw     = ( ! empty($_POST['pt_draw']))     ? intval($_POST['pt_draw'])     : 0;
    $defeat   = ( ! empty($_POST['pt_defeat']))   ? intval($_POST['pt_defeat'])   : 0;
    $promo    = ( ! empty($_POST['promotion']))   ? intval($_POST['promotion'])   : 0;
    $qualif   = ( ! empty($_POST['qualifying']))  ? intval($_POST['qualifying'])  : 0;
    $releg    = ( ! empty($_POST['relegation']))  ? intval($_POST['relegation'])  : 0;
    $favorite = ( ! empty($_POST['id_favorite'])) ? intval($_POST['id_favorite']) : 0;
    $nb_leg   = ( ! empty($_POST['nb_leg']))      ? intval($_POST['nb_leg'])      : 2;
    $name     = ( ! empty($_POST['name']))        ? trim($_POST['name'])          : NULL;
    $link     = ( ! empty($_POST['link']))        ? trim($_POST['link'])          : 'no';
    $player   = ( ! empty($_POST['player']))      ? trim($_POST['player'])        : 'no';
    $year     = ( ! empty($_POST['year']))        ? intval($_POST['year'])        : 0000;
    
    if ($_POST['predict'] === 'yes' && $year >= date('Y')) {
        $predict = 'yes';
    } elseif ($_POST['predict'] === 'no') {
        $predict = 'no';
    } else {
        $predict = 'no';
        $message[] = __('You cannot activate the Prediction Mod because the year is past.', 'phpleague');
    }
    
    if ($db->is_league_setting_in_db($id_league) === FALSE) {
        $message[] = __('We did not find the corresponding settings to this league.', 'phpleague');
    } elseif ( ! preg_match('/^([0-9]{4})$/', $year)) {
        $message[] = __('The year must be 4 digits.', 'phpleague');
    } elseif (in_array($name, array(NULL, FALSE, ''))) {
        $message[] = __('The name cannot be empty.', 'phpleague');
    } elseif ($fct->valid_text($name, 3) === FALSE) {
        $message[] = __('The name must be alphanumeric and 3 characters long at least.', 'phpleague');
    } else { // Update the database
        $db->update_league_settings($name, $year, $id_league, $victory, $draw, $defeat, $promo, $qualif, $releg, $favorite, $nb_leg, $link, $time, $player, $predict);
        $message[] = __('Settings updated successfully.', 'phpleague');
    }
} elseif (isset($_POST['bonus_malus']) && check_admin_referer('phpleague')) {
    $malus = ( ! empty($_POST['malus']) && is_array($_POST['malus'])) ? $_POST['malus'] : 0;
    
    if ($malus === 0) {
        $message[] = __('We are sorry but it seems that an error occurred.', 'phpleague');
    } else {
        foreach ($malus as $key => $row) {
            $row = intval($row);
            $key = intval($key);
            $db->edit_bonus_malus($row, $key);
        }
        
        $message[] = __('Bonus/Malus updated successfully!', 'phpleague');
    }
} elseif (isset($_POST['prediction']) && check_admin_referer('phpleague')) {
    // Secure data
    $point_right = ( ! empty($_POST['point_right'])) ? intval($_POST['point_right']) : 5;
    $point_wrong = ( ! empty($_POST['point_wrong'])) ? intval($_POST['point_wrong']) : 0;
    $point_part  = ( ! empty($_POST['point_part']))  ? intval($_POST['point_part'])  : 1;
    $deadline    = ( ! empty($_POST['deadline']))    ? intval($_POST['deadline'])    : 1;
    
    $db->edit_prediction_settings($id_league, $point_right, $point_wrong, $point_part, $deadline);
    $message[] = __('Prediction settings updated successfully!', 'phpleague');
} elseif (isset($_POST['player']) && check_admin_referer('phpleague')) {
    // Secure data
    $starting   = ( ! empty($_POST['starting']))   ? intval($_POST['starting'])           : 0;
    $substitute = ( ! empty($_POST['substitute'])) ? intval($_POST['substitute'])         : 0;
    $positions  = ( ! empty($_POST['positions']))  ? maybe_serialize($_POST['positions']) : NULL;
    $events     = ( ! empty($_POST['events']))     ? maybe_serialize($_POST['events'])    : NULL;
        
    $db->edit_player_settings($id_league, $starting, $substitute, $positions, $events);
    $message[] = __('Players settings updated successfully!', 'phpleague');
}

// Vars
$league_name = $db->return_league_name($id_league);
$teams       = $db->get_distinct_league_team($id_league);
$setting     = $db->get_league_settings($id_league);
$page_url    = admin_url('admin.php?page=phpleague_overview&option=setting&id_league='.$id_league);
$clubs_list  = array();
$data        = array();
$numbers     = array();
$yes_no      = array('no' => __('No'), 'yes' => __('Yes'));
$menu        = array(
    __('Teams', 'phpleague')    => admin_url('admin.php?page=phpleague_overview&option=team&id_league='.$id_league),
    __('Fixtures', 'phpleague') => admin_url('admin.php?page=phpleague_overview&option=fixture&id_league='.$id_league),
    __('Matches', 'phpleague')  => admin_url('admin.php?page=phpleague_overview&option=team&id_league='.$id_league),
    __('Results', 'phpleague')  => admin_url('admin.php?page=phpleague_overview&option=result&id_league='.$id_league),
    __('Settings', 'phpleague') => '#',
    __('Generate', 'phpleague') => admin_url('admin.php?page=phpleague_overview&option=generator&id_league='.$id_league)
);

// Used for the player/prediction modes
for ($i = 0; $i <= 50; $i++) {
    $numbers[$i] = $i;
}

// Get every club from the league
$clubs_list[0] = '-- Select a team --';
foreach ($teams as $array) {
    $clubs_list[$array->club_id] = esc_html($array->name);  
}

$output  = $fct->form_open($page_url);
$output .=
'<table class="form-table">
    <tr>
        <td class="required">'.__('Name:', 'phpleague').'</td>
        <td>'.$fct->input('name', esc_html($setting->name), array('size' => 15, 'readonly' => 'readonly')).'</td>
        <td class="required">'.__('Year:', 'phpleague').'</td>
        <td>'.$fct->input('year', intval($setting->year), array('size' => 4, 'readonly' => 'readonly')).'</td>
    </tr>
    <tr>
        <td>'.__('Point(s) for Victory:', 'phpleague').'</td>
        <td>'.$fct->input('pt_victory', intval($setting->pt_victory), array('size' => 3)).'</td>
        <td>'.__('Team(s) for Promotion:', 'phpleague').'</td>
        <td>'.$fct->input('promotion', intval($setting->promotion), array('size' => 3)).'</td>
    </tr>
    <tr>
        <td>'.__('Point(s) for Draw:', 'phpleague').'</td> 
        <td>'.$fct->input('pt_draw', intval($setting->pt_draw), array('size' => 3)).'</td>
        <td>'.__('Team(s) for Qualifying:', 'phpleague').'</td>
        <td>'.$fct->input('qualifying', intval($setting->qualifying), array('size' => 3)).'</td>
    </tr>
    <tr>
        <td>'.__('Point(s) for Defeat:', 'phpleague').'</td>
        <td>'.$fct->input('pt_defeat', intval($setting->pt_defeat), array('size' => 3)).'</td>
        <td>'.__('Team(s) for Relegation:', 'phpleague').'</td>
        <td>'.$fct->input('relegation', intval($setting->relegation), array('size' => 3)).'</td>
    </tr>
    <tr>
        <td>'.__('Club Information Link:', 'phpleague').'</td>
        <td>'.$fct->select('link', $yes_no, $setting->team_link).'</td>
        <td>'.__('Time by Default:', 'phpleague').'</td>
        <td>'.$fct->input('time', $setting->default_time, array('size' => 6)).'</td>
    </tr>
    <tr>
        <td>'.__('Favorite Team:', 'phpleague').'</td>
        <td>'.$fct->select('id_favorite', $clubs_list, $setting->id_favorite).'</td>
        <td>'.__('Leg(s) between Teams:', 'phpleague').'</td>
        <td>'.$fct->input('nb_leg', intval($setting->nb_leg), array('size' => 3)).'</td>
    </tr>
    <tr>
        <td>'.__('Player Mod Enabled:', 'phpleague').'</td>
        <td>'.$fct->select('player', $yes_no, $setting->player_mod).'</td>
        <td>'.__('Prediction Mod Enabled:', 'phpleague').'</td>
        <td>'.$fct->select('predict', $yes_no, $setting->prediction_mod).'</td>
    </tr>
</table><div class="submit">'.$fct->input('general', __('Save', 'phpleague'), array('type' => 'submit')).'</div>';

$output .= $fct->form_close();
$data[]  = array(
    'menu'  => __('Settings', 'phpleague'),
    'title' => __('General Settings', 'phpleague'),
    'text'  => $output,
    'class' => 'full'
);

// If the player mode is enabled...
if ($setting->player_mod === 'yes') {
    $output  = $fct->form_open($page_url);
    $output .=
    '<table class="form-table">
        <tr>
            <td>'.__('Starting Players:', 'phpleague').'</td>
            <td>'.$fct->select('starting', $numbers, $setting->starting).'</td>
            <td>'.__('Substitute Players:', 'phpleague').'</td>
            <td>'.$fct->select('substitute', $numbers, $setting->substitute).'</td>
        </tr>
    </table><br />';
    
    $output .=
    '<table id="positions-table" class="widefat">
        <thead>
            <tr>
                <th>'.__('ID', 'phpleague').'</th>
                <th>'.__('Name', 'phpleague').'</th>
                <th>'.__('Order', 'phpleague').'</th>
                <th><a href="#add_position" id="add_position">'.__('New Position', 'phpleague').'</a></th>
            </tr>
        </thead>
        <tbody>';

    $positions = $db->get_positions($id_league);
    foreach (maybe_unserialize($positions) as $position) {
        // Field empty or null
        if ($position == 'NULL' || $position == '') {
            $output .= '<tr id="position-1">';
            $output .= '<td>'.$fct->input('positions[1][id]', 1, array('size' => '4', 'readonly' => 'readonly')).'</td>';
            $output .= '<td>'.$fct->input('positions[1][name]', '').'</td>';
            $output .= '<td>'.$fct->input('positions[1][order]', 0, array('size' => '4')).'</td>';
            $output .= '<td>'.$fct->input('remove_position', __('Remove', 'phpleague'), array('type' => 'button', 'class' => 'button remove_position')).'</td>';
            $output .= '</tr>';
        } else {
            foreach (maybe_unserialize($position) as $key => $row) {
                $key++;
                $output .= '<tr id="position-'.$key.'">';
                $output .= '<td>'.$fct->input('positions['.$key.'][id]', $row['id'], array('size' => '4', 'readonly' => 'readonly')).'</td>';
                $output .= '<td>'.$fct->input('positions['.$key.'][name]', $row['name']).'</td>';
                $output .= '<td>'.$fct->input('positions['.$key.'][order]', $row['order'], array('size' => '4')).'</td>';
                $output .= '<td>'.$fct->input('remove_position', __('Remove', 'phpleague'), array('type' => 'button', 'class' => 'button remove_position')).'</td>';
                $output .= '</tr>';
            }
        }
    }
    
    $output .= '</tbody></table><br />';
    $output .=
    '<table id="events-table" class="widefat">
        <thead>
            <tr>
                <th>'.__('ID', 'phpleague').'</th>
                <th>'.__('Full Name', 'phpleague').'</th>
                <th>'.__('Mini Name', 'phpleague').'</th>
                <th><a href="#add_event" id="add_event">'.__('New Event', 'phpleague').'</a></th>
            </tr>
        </thead>
        <tbody>';

    $events = $db->get_events($id_league);
    foreach (maybe_unserialize($events) as $event) {
        // Field empty or null
        if ($event == 'NULL' || $event == '') {
            $output .= '<tr id="event-1">';
            $output .= '<td>'.$fct->input('events[1][id]', 1, array('size' => '4', 'readonly' => 'readonly')).'</td>';
            $output .= '<td>'.$fct->input('events[1][full_name]', '').'</td>';
            $output .= '<td>'.$fct->input('events[1][mini_name]', '').'</td>';
            $output .= '<td>'.$fct->input('remove_event', __('Remove', 'phpleague'), array('type' => 'button', 'class' => 'button remove_event')).'</td>';
            $output .= '</tr>';
        } else {
            foreach (maybe_unserialize($event) as $key => $row) {
                $key++;
                $output .= '<tr id="event-'.$key.'">';
                $output .= '<td>'.$fct->input('events['.$key.'][id]', $row['id'], array('size' => '4', 'readonly' => 'readonly')).'</td>';
                $output .= '<td>'.$fct->input('events['.$key.'][full_name]', $row['full_name']).'</td>';
                $output .= '<td>'.$fct->input('events['.$key.'][mini_name]', $row['mini_name']).'</td>';
                $output .= '<td>'.$fct->input('remove_event', __('Remove', 'phpleague'), array('type' => 'button', 'class' => 'button remove_event')).'</td>';
                $output .= '</tr>';
            }
        }
    }
    
    $output .= '</tbody></table>';
    $output .= '<div class="submit">'.$fct->input('player', __('Save', 'phpleague'), array('type' => 'submit')).'</div>';
    $output .= $fct->form_close();
    $data[]  = array(
        'menu'  => __('Settings', 'phpleague'),
        'title' => __('Player Settings', 'phpleague'),
        'text'  => $output,
        'class' => 'full'
    );
}

// If the prediction mode is enabled...
if ($setting->prediction_mod === 'yes') {
    $output  = $fct->form_open($page_url);
    $output .=
    '<table class="form-table">
        <tr>
            <td>'.__('Point(s) when Right:', 'phpleague').'</td>
            <td>'.$fct->select('point_right', $numbers, $setting->point_right).'</td>
            <td>'.__('Point(s) when Wrong:', 'phpleague').'</td>
            <td>'.$fct->select('point_wrong', $numbers, $setting->point_wrong).'</td>
        </tr>
        <tr>
            <td>'.__('Point(s) for Participation:', 'phpleague').'</td>
            <td>'.$fct->select('point_part', $numbers, $setting->point_part).'</td>
            <td>'.__('Hour(s) before Closing:', 'phpleague').'</td>
            <td>'.$fct->select('deadline', $numbers, $setting->deadline).'</td>
        </tr>
    </table>
    <div class="submit">
        '.$fct->input('prediction', __('Save', 'phpleague'), array('type' => 'submit')).'
    </div>';
    
    $output .= $fct->form_close();
    $data[]  = array(
        'menu'  => __('Settings', 'phpleague'),
        'title' => __('Prediction Settings', 'phpleague'),
        'text'  => $output,
        'class' => 'full'
    );
}

$output  = $fct->form_open($page_url);
$output .= '<table class="form-table">';

foreach ($teams as $row) {
    $output .=
    '<tr>
        <td>'.esc_html($row->name).'</td>
        <td>'.$fct->input('malus['.intval($row->club_id).']', intval($row->penalty), array('size' => 4)).'</td>
    </tr>';
}
    
$output .= '</table><div class="submit">'.$fct->input('bonus_malus', __('Save'), array('type' => 'submit')).'</div>';
$output .= $fct->form_close();

$data[] = array(
    'menu'  => __('Settings', 'phpleague'),
    'title' => __('Bonus/Malus for ', 'phpleague').$league_name,
    'text'  => $output,
    'hide'  => TRUE,
    'class' => 'full'
);

// Show everything...
echo $ctl->admin_container($menu, $data, $message);