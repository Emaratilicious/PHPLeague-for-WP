<?php
// -- Instances
$db  = new PHPLeague_Database();
$ctl = new PHPLeague_Admin_Controller();
$fct = new MWD_Plugin_Tools();

// -- $_GET
$get_id_league = ( ! empty($_GET['id_league']) ? intval($_GET['id_league']) : 0);
$message	   = '';

if ($db->is_league_exists($get_id_league) === FALSE) {
	wp_die(__('We did not find the league in the database.', 'phpleague'));
}

if (isset($_POST['settings']) && check_admin_referer('phpleague_nonce_admin')) {
	$victory  = ( ! empty($_POST['pt_victory']))  ? intval($_POST['pt_victory'])  : 0;
	$draw 	  = ( ! empty($_POST['pt_draw'])) 	  ? intval($_POST['pt_draw']) 	  : 0;
	$defeat   = ( ! empty($_POST['pt_defeat']))   ? intval($_POST['pt_defeat'])   : 0;
	$promo 	  = ( ! empty($_POST['promotion']))   ? intval($_POST['promotion'])   : 0;
	$qualif	  = ( ! empty($_POST['qualifying']))  ? intval($_POST['qualifying'])  : 0;
	$releg    = ( ! empty($_POST['relegation']))  ? intval($_POST['relegation'])  : 0;
	$favorite = ( ! empty($_POST['id_favorite'])) ? intval($_POST['id_favorite']) : 0;
	$nb_leg   = ( ! empty($_POST['nb_leg'])) 	  ? intval($_POST['nb_leg']) 	  : 2;
	$name  	  = ( ! empty($_POST['name']))  	  ? trim($_POST['name'])		  : NULL;
	$year 	  = ( ! empty($_POST['year'])) 	  	  ? intval($_POST['year']) 	  	  : 2000;
	
	if ($db->is_league_setting_in_db($get_id_league) === FALSE) {
		$message = __('We did not find the corresponding settings to this league.', 'phpleague');
	} elseif ( ! preg_match("/^([0-9]{4})$/", $year)) {
		$message = __('The year must be 4 digits.', 'phpleague');
	} elseif (in_array($name, array(NULL, FALSE, ''))) {
		$message = __('The competition name cannot be empty.', 'phpleague');
	} elseif ( ! preg_match('/^[A-Za-z0-9_\-. ]{3,}$/', $name)) {
		$message = __('The name must be alphanumeric and 3 characters long at least.', 'phpleague');
	} else {
		// update the db
		$db->update_league_settings($name, $year, $get_id_league, $victory, $draw, $defeat, $promo, $qualif, $releg, $favorite, $nb_leg);
		$message = __('Settings updated successfully.', 'phpleague');
	}
} elseif (isset($_POST['bonus_malus']) && check_admin_referer('phpleague_nonce_admin')) {
	$malus = ( ! empty($_POST['malus']) && is_array($_POST['malus'])) ? $_POST['malus'] : 0;
	
	if ($malus === 0) {
		$message = __('We are sorry but it seems that an error occurred.', 'phpleague');
	} else {
		foreach ($malus as $key => $row) {
			$row = intval($row);
			$key = intval($key);
			$db->edit_bonus_malus($row, $key);
		}
		
		$message = __('Bonus/Malus updated successfully!', 'phpleague');
	}
}

// -- Vars
$league_name = $db->return_league_name($get_id_league);
$setting	 = $db->get_league_settings($get_id_league);
$clubs_list	 = array();
$data 		 = array();
$menu 		 = array(
	__('Settings', 'phpleague') => '#',
	__('Teams', 'phpleague')    => admin_url('admin.php?page=phpleague_overview&option=team&id_league='.$get_id_league),
	__('Fixtures', 'phpleague') => admin_url('admin.php?page=phpleague_overview&option=fixture&id_league='.$get_id_league),
	__('Matches', 'phpleague')  => admin_url('admin.php?page=phpleague_overview&option=match&id_league='.$get_id_league),
	__('Results', 'phpleague')  => admin_url('admin.php?page=phpleague_overview&option=result&id_league='.$get_id_league)
);

// Get every club as an array
foreach ($db->get_favorite_league_team($get_id_league) as $array) {
	$clubs_list[intval($array->club_id)] = esc_html($array->name);	
}

$output  = $fct->form_open(admin_url('admin.php?page=phpleague_overview&option=setting&id_league='.$get_id_league));
$output .=
	'<table class="form-table">
		<tr>
			<td>'.__('League Name:', 'phpleague').'</td>
			<td>'.$fct->input('name', esc_html($setting->name), array('size' => 15)).'</td>
			<td>'.__('League Season:', 'phpleague').'</td>
			<td>'.$fct->input('year', intval($setting->year), array('size' => 4)).'</td>
		</tr>
		<tr>
			<td>'.__('How many point(s) for a victory:', 'phpleague').'</td>
			<td>'.$fct->input('pt_victory', intval($setting->pt_victory), array('size' => 3)).'</td>
			<td>'.__('How many team(s) for Promotion:', 'phpleague').'</td>
			<td>'.$fct->input('promotion', intval($setting->promotion), array('size' => 3)).'</td>
		</tr>
		<tr>
			<td>'.__('How many point(s) for a draw:', 'phpleague').'</td> 
			<td>'.$fct->input('pt_draw', intval($setting->pt_draw), array('size' => 3)).'</td>
			<td>'.__('How many team(s) for Qualifying:', 'phpleague').'</td>
			<td>'.$fct->input('qualifying', intval($setting->qualifying), array('size' => 3)).'</td>
		</tr>
		<tr>
			<td>'.__('How many point(s) for a defeat:', 'phpleague').'</td>
			<td>'.$fct->input('pt_defeat', intval($setting->pt_defeat), array('size' => 3)).'</td>
			<td>'.__('How many team(s) for Relegation:', 'phpleague').'</td>
			<td>'.$fct->input('relegation', intval($setting->relegation), array('size' => 3)).'</td>
		</tr>
		<tr>
			<td>'.__('Your favorite team:', 'phpleague').'</td>
			<td>'.$fct->select('id_favorite', $clubs_list, $setting->id_favorite).'</td>
			<td>'.__('How many leg(s) between each team:', 'phpleague').'</td>
			<td>'.$fct->input('nb_leg', intval($setting->nb_leg), array('size' => 3)).'</td>
		</tr>
 	</table>
	<div class="submit">
		'.$fct->input('settings', __('Save', 'phpleague'), array('type' => 'submit')).'
	</div>';

$output .= $fct->form_close();

$data[] = array(
	'menu'  => __('Settings', 'phpleague'),
	'title' => __('Overview of ', 'phpleague').$league_name,
	'text'  => $output,
	'class' => 'full'
);

$output  = $fct->form_open(admin_url('admin.php?page=phpleague_overview&option=setting&id_league='.$get_id_league));
$output .= '<table class="form-table">';

foreach ($db->get_distinct_league_team($get_id_league) as $row) {
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
	'class' => 'full'
);

echo $ctl->admin_container($menu, $data, $message);