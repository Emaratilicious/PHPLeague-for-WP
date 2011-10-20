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
if (isset($_POST['general']) && check_admin_referer('phpleague'))
{
	$time     = (preg_match('/^\d{2}:\d{2}:\d{2}$/', $_POST['time'])) ? trim($_POST['time']) : '17:00:00';
	$victory  = ( ! empty($_POST['pt_victory']))  ? intval($_POST['pt_victory'])  : 0;
	$draw 	  = ( ! empty($_POST['pt_draw'])) 	  ? intval($_POST['pt_draw']) 	  : 0;
	$defeat   = ( ! empty($_POST['pt_defeat']))   ? intval($_POST['pt_defeat'])   : 0;
	$promo 	  = ( ! empty($_POST['promotion']))   ? intval($_POST['promotion'])   : 0;
	$qualif	  = ( ! empty($_POST['qualifying']))  ? intval($_POST['qualifying'])  : 0;
	$releg    = ( ! empty($_POST['relegation']))  ? intval($_POST['relegation'])  : 0;
	$favorite = ( ! empty($_POST['id_favorite'])) ? intval($_POST['id_favorite']) : 0;
	$nb_leg   = ( ! empty($_POST['nb_leg'])) 	  ? intval($_POST['nb_leg']) 	  : 2;
	$name  	  = ( ! empty($_POST['name']))  	  ? trim($_POST['name'])		  : NULL;
    $link     = ( ! empty($_POST['link']))        ? trim($_POST['link'])          : 'no';
	$year 	  = ( ! empty($_POST['year'])) 	  	  ? intval($_POST['year']) 	  	  : 2000;
	
	if ($db->is_league_setting_in_db($id_league) === FALSE) {
	    $message[] = __('We did not find the corresponding settings to this league.', 'phpleague');
	} elseif ( ! preg_match('/^([0-9]{4})$/', $year)) {
		$message[] = __('The year must be 4 digits.', 'phpleague');
	} elseif (in_array($name, array(NULL, FALSE, ''))) {
		$message[] = __('The name cannot be empty.', 'phpleague');
	} elseif ($fct->valid_text($name, 3) === FALSE) {
		$message[] = __('The name must be alphanumeric and 3 characters long at least.', 'phpleague');
	} else { // Update the database
		$db->update_league_settings($name, $year, $id_league, $victory, $draw, $defeat, $promo, $qualif, $releg, $favorite, $nb_leg, $link, $time);
		$message[] = __('Settings updated successfully.', 'phpleague');
	}
}
elseif (isset($_POST['bonus_malus']) && check_admin_referer('phpleague'))
{
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
}

// Vars
$league_name = $db->return_league_name($id_league);
$teams       = $db->get_distinct_league_team($id_league);
$setting	 = $db->get_league_settings($id_league);
$page_url    = admin_url('admin.php?page=phpleague_overview&option=setting&id_league='.$id_league);
$clubs_list	 = array();
$data 		 = array();
$yes_no      = array('no' => __('No'), 'yes' => __('Yes'));
$menu        = array(
    __('Teams', 'phpleague')    => admin_url('admin.php?page=phpleague_overview&option=team&id_league='.$id_league),
    __('Fixtures', 'phpleague') => admin_url('admin.php?page=phpleague_overview&option=fixture&id_league='.$id_league),
    __('Matches', 'phpleague')  => admin_url('admin.php?page=phpleague_overview&option=team&id_league='.$id_league),
    __('Results', 'phpleague')  => admin_url('admin.php?page=phpleague_overview&option=result&id_league='.$id_league),
    __('Settings', 'phpleague') => '#',
    __('Generate', 'phpleague') => admin_url('admin.php?page=phpleague_overview&option=generator&id_league='.$id_league)
);

// Useful when the player and/or prediction mode are enabled
$numbers = array();
for ($i = 0; $i <= 50; $i++) {
    $numbers[$i] = $i;
}

// Get every club from the league
$clubs_list[0] = '-- Select a club --';
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
</table><div class="submit">'.$fct->input('general', __('Save', 'phpleague'), array('type' => 'submit')).'</div>';

$output .= $fct->form_close();

$data[] = array(
	'menu'  => __('Settings', 'phpleague'),
	'title' => __('General Settings', 'phpleague'),
	'text'  => $output,
	'class' => 'full'
);

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

echo $ctl->admin_container($menu, $data, $message);