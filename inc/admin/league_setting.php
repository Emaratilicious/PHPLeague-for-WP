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

// Data processing...
if (isset($_POST['general']) && check_admin_referer('phpleague'))
{
    // Secure data
    $time     = (preg_match('/^\d{2}:\d{2}:\d{2}$/', $_POST['time'])) ? trim($_POST['time']) : '17:00:00';
    $victory  = ( ! empty($_POST['pt_victory']))  ? (int) $_POST['pt_victory']  : 0;
    $draw     = ( ! empty($_POST['pt_draw']))     ? (int) $_POST['pt_draw']     : 0;
    $defeat   = ( ! empty($_POST['pt_defeat']))   ? (int) $_POST['pt_defeat']   : 0;
    $promo    = ( ! empty($_POST['promotion']))   ? (int) $_POST['promotion']   : 0;
    $qualif   = ( ! empty($_POST['qualifying']))  ? (int) $_POST['qualifying']  : 0;
    $releg    = ( ! empty($_POST['relegation']))  ? (int) $_POST['relegation']  : 0;
    $favorite = ( ! empty($_POST['id_favorite'])) ? (int) $_POST['id_favorite'] : 0;
    $nb_leg   = ( ! empty($_POST['nb_leg']))      ? (int) $_POST['nb_leg']      : 2;
    $name     = ( ! empty($_POST['name']))        ? trim($_POST['name'])        : NULL;
    $link     = ( ! empty($_POST['link']))        ? trim($_POST['link'])        : 'no';
    $player   = ( ! empty($_POST['player']))      ? trim($_POST['player'])      : 'no';
    $year     = ( ! empty($_POST['year']))        ? (int) $_POST['year']        : 0000;
    
    if ($_POST['predict'] === 'yes' && $year >= date('Y'))
    {
        $predict = 'yes';
    }
    elseif ($_POST['predict'] === 'no')
    {
        $predict = 'no';
    }
    else
    {
        $predict = 'no';
        $message[] = __('You cannot activate the Prediction Mod because the year is past.', 'phpleague');
    }
    
    if ($db->is_league_setting_in_db($id_league) === FALSE)
    {
        $message[] = __('We did not find the corresponding settings to this league.', 'phpleague');
    }
    elseif ( ! preg_match('/^([0-9]{4})$/', $year))
    {
        $message[] = __('The year must be 4 digits.', 'phpleague');
    }
    elseif (in_array($name, array(NULL, FALSE, '')))
    {
        $message[] = __('The name cannot be empty.', 'phpleague');
    }
    elseif ($fct->valid_text($name, 3) === FALSE)
    {
        $message[] = __('The name must be alphanumeric and 3 characters long at least.', 'phpleague');
    }
    else // Update the database
    {
        $db->update_league_settings($name, $year, $id_league, $victory, $draw, $defeat, $promo, $qualif, $releg, $favorite, $nb_leg, $link, $time, $player, $predict);
        $message[] = __('Settings updated successfully.', 'phpleague');
    }
}
elseif (isset($_POST['bonus_malus']) && check_admin_referer('phpleague'))
{
    // Secure data
    $malus = ( ! empty($_POST['malus']) && is_array($_POST['malus'])) ? $_POST['malus'] : 0;
    
    if ($malus === 0)
    {
        $message[] = __('We are sorry but it seems that an error occurred.', 'phpleague');
    }
    else
    {
        foreach ($malus as $key => $row)
        {
            $row = (int) $row;
            $key = (int) $key;
            $db->edit_bonus_malus($row, $key);
        }
        
        $message[] = __('Bonus/Malus updated successfully!', 'phpleague');
    }
}
elseif (isset($_POST['prediction']) && check_admin_referer('phpleague'))
{
    // Secure data
    $point_right = ( ! empty($_POST['point_right'])) ? (int) $_POST['point_right'] : 5;
    $point_wrong = ( ! empty($_POST['point_wrong'])) ? (int) $_POST['point_wrong'] : 0;
    $point_part  = ( ! empty($_POST['point_part']))  ? (int) $_POST['point_part']  : 1;
    $deadline    = ( ! empty($_POST['deadline']))    ? (int) $_POST['deadline']    : 1;
    
    $db->edit_prediction_settings($id_league, $point_right, $point_wrong, $point_part, $deadline);
    $message[] = __('Prediction settings updated successfully!', 'phpleague');
}
elseif (isset($_POST['player']) && check_admin_referer('phpleague'))
{
    // Secure data
    $starting   = ( ! empty($_POST['starting']))   ? (int) $_POST['starting']   : 0;
    $substitute = ( ! empty($_POST['substitute'])) ? (int) $_POST['substitute'] : 0;
        
    $db->edit_player_settings($id_league, $starting, $substitute);
    $message[] = __('Players settings updated successfully!', 'phpleague');
}

// Variables
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

// Numbers list
for ($i = 0; $i <= 50; $i++)
{
    $numbers[$i] = $i;
}

// Get every team from the league
$clubs_list[0] = __('-- Select a team --', 'phpleague');
foreach ($teams as $array)
{
    $clubs_list[$array->club_id] = esc_html($array->name);  
}

// Get a list of every available sports
$sports_list = array(
    //'american-football' => __('American Football', 'phpleague'),
    //'basketball'        => __('Basketball', 'phpleague'),
    'football'          => __('Football', 'phpleague'),
    //'handball'          => __('Handball', 'phpleague'),
    //'hockey'            => __('Hockey', 'phpleague'),
    //'rugby'             => __('Rugby', 'phpleague'),
    //'volleyball'        => __('Volleyball', 'phpleague')
);

$output  = $fct->form_open($page_url);
$output .=
'<table class="form-table">
    <tr>
        <td class="required">'.__('Name:', 'phpleague').'</td>
        <td>'.$fct->input('name', esc_html($setting->name), array('size' => 15, 'readonly' => 'readonly')).'</td>
        <td class="required">'.__('Year:', 'phpleague').'</td>
        <td>'.$fct->input('year', (int) $setting->year, array('size' => 4, 'readonly' => 'readonly')).'</td>
    </tr>
    <tr>
        <td>'.__('Point(s) for Victory:', 'phpleague').'</td>
        <td>'.$fct->input('pt_victory', (int) $setting->pt_victory, array('size' => 3)).'</td>
        <td>'.__('Team(s) for Promotion:', 'phpleague').'</td>
        <td>'.$fct->input('promotion', (int) $setting->promotion, array('size' => 3)).'</td>
    </tr>
    <tr>
        <td>'.__('Point(s) for Draw:', 'phpleague').'</td> 
        <td>'.$fct->input('pt_draw', (int) $setting->pt_draw, array('size' => 3)).'</td>
        <td>'.__('Team(s) for Qualifying:', 'phpleague').'</td>
        <td>'.$fct->input('qualifying', (int) $setting->qualifying, array('size' => 3)).'</td>
    </tr>
    <tr>
        <td>'.__('Point(s) for Defeat:', 'phpleague').'</td>
        <td>'.$fct->input('pt_defeat', (int) $setting->pt_defeat, array('size' => 3)).'</td>
        <td>'.__('Team(s) for Relegation:', 'phpleague').'</td>
        <td>'.$fct->input('relegation', (int) $setting->relegation, array('size' => 3)).'</td>
    </tr>
    <tr>
        <td>'.__('Club Information Link:', 'phpleague').'</td>
        <td>'.$fct->select('link', $yes_no, $setting->team_link).'</td>
        <td>'.__('Time by Default:', 'phpleague').'</td>
        <td>'.$fct->input('time', $setting->default_time, array('size' => 6, 'class' => 'masked-time')).'</td>
    </tr>
    <tr>
        <td>'.__('Favorite Team:', 'phpleague').'</td>
        <td>'.$fct->select('id_favorite', $clubs_list, $setting->id_favorite).'</td>
        <td>'.__('Leg(s) between Teams:', 'phpleague').'</td>
        <td>'.$fct->input('nb_leg', (int) $setting->nb_leg, array('size' => 3)).'</td>
    </tr>
    <tr>
        <td>'.__('Player Mod Enabled:', 'phpleague').'</td>
        <td>'.$fct->select('player', $yes_no, $setting->player_mod).'</td>
        <td>'.__('Prediction Mod Enabled:', 'phpleague').'</td>
        <td>'.$fct->select('predict', $yes_no, $setting->prediction_mod).'</td>
    </tr>
</table>
<div>'.__('I do not recommend using the Player Mod in production environment yet and be aware that only few features have been developed so far...', 'phpleague').'</div>
<div>'.__('Prediction Mod is not working at all except to save the settings because I am waiting for the new WordPress 3.3.', 'phpleague').'</div>
<div class="submit">'.$fct->input('general', __('Save', 'phpleague'), array('type' => 'submit')).'</div>';

$output .= $fct->form_close();
$data[]  = array(
    'menu'  => __('Settings', 'phpleague'),
    'title' => __('General Settings', 'phpleague'),
    'text'  => $output,
    'class' => 'full'
);

// If the player mode is enabled...
if ($setting->player_mod === 'yes')
{
    $output  = $fct->form_open($page_url);
    $output .=
    '<table class="form-table">
        <tr>
            <td>'.__('Starting Players:', 'phpleague').'</td>
            <td>'.$fct->select('starting', $numbers, (int) $setting->nb_starter).'</td>
            <td>'.__('Substitute Players:', 'phpleague').'</td>
            <td>'.$fct->select('substitute', $numbers, (int) $setting->nb_bench).'</td>
        </tr>
        <tr>
            <td>'.__('What Sport:', 'phpleague').'</td>
            <td>'.$fct->select('sports', $sports_list).'</td>
        </tr>
    </table>';

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
if ($setting->prediction_mod === 'yes')
{
    $output  = $fct->form_open($page_url);
    $output .=
    '<table class="form-table">
        <tr>
            <td>'.__('Point(s) when Right:', 'phpleague').'</td>
            <td>'.$fct->select('point_right', $numbers, (int) $setting->point_right).'</td>
            <td>'.__('Point(s) when Wrong:', 'phpleague').'</td>
            <td>'.$fct->select('point_wrong', $numbers, (int) $setting->point_wrong).'</td>
        </tr>
        <tr>
            <td>'.__('Point(s) for Participation:', 'phpleague').'</td>
            <td>'.$fct->select('point_part', $numbers, (int) $setting->point_part).'</td>
            <td>'.__('Hour(s) before Closing:', 'phpleague').'</td>
            <td>'.$fct->select('deadline', $numbers, (int) $setting->deadline).'</td>
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

foreach ($teams as $row)
{
    $output .= '<tr><td>'.esc_html($row->name).'</td>'
            .'<td>'.$fct->input('malus['.(int) $row->club_id.']', (int) $row->penalty, array('size' => 4)).'</td></tr>';
}
    
$output .= '</table><div class="submit">'.$fct->input('bonus_malus',
        __('Save'), array('type' => 'submit')).'</div>'.$fct->form_close();
$data[]  = array(
    'menu'  => __('Settings', 'phpleague'),
    'title' => __('Bonus/Malus for ', 'phpleague').$league_name,
    'text'  => $output,
    'hide'  => TRUE,
    'class' => 'full'
);

// Render the page
echo $ctl->admin_container($menu, $data, $message);