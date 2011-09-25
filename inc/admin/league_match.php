<?php
// -- Instances
$db  = new PHPLeague_Database();
$ctl = new PHPLeague_Admin_Controller();
$fct = new MWD_Plugin_Tools();

// -- $_GET
$get_id_league  = ( ! empty($_GET['id_league'])  ? intval($_GET['id_league'])  : 0);
$get_id_fixture = ( ! empty($_GET['id_fixture']) ? intval($_GET['id_fixture']) : 1);

if ($db->is_league_exists($get_id_league) === FALSE) {
	wp_die(__('We did not find the league in the database.', 'phpleague'));
}

// -- Vars
$league_name = $db->return_league_name($get_id_league);
$setting	 = $db->get_league_settings($get_id_league);
$nb_teams	 = intval($setting->nb_teams);
$nb_legs 	 = intval($setting->nb_leg);
$page_url    = 'admin.php?page=phpleague_overview&option=match&id_league='.$get_id_league.'&id_fixture='.$get_id_fixture;
$message	 = '';
$output		 = '';
$data 		 = array();
$menu 		 = array(
	__('Matches', 'phpleague')  => '#',
	__('Teams', 'phpleague')    => admin_url('admin.php?page=phpleague_overview&option=team&id_league='.$get_id_league),
	__('Fixtures', 'phpleague') => admin_url('admin.php?page=phpleague_overview&option=fixture&id_league='.$get_id_league),
	__('Results', 'phpleague')  => admin_url('admin.php?page=phpleague_overview&option=result&id_league='.$get_id_league),
	__('Settings', 'phpleague') => admin_url('admin.php?page=phpleague_overview&option=setting&id_league='.$get_id_league)
);

// Check what kind of number we have (odd/even)
if (($nb_teams % 2) != 0) {
	$nb_fixtures = $nb_teams * $nb_legs;
	$nb_matches  = ($nb_teams - 1) / 2;
} else {
	$nb_fixtures = ($nb_teams * $nb_legs) - $nb_legs;
	$nb_matches  = ($nb_teams / 2);
}

// User sent some data
if (isset($_POST['matches']) && check_admin_referer('phpleague_nonce_admin')) {
	$id_fixture = ( ! empty($_POST['id_fixture'])) ? intval($_POST['id_fixture']) : 0;
	$id_home    = ( ! empty($_POST['id_home']) && is_array($_POST['id_home'])) ? $_POST['id_home'] : NULL;
	$id_away    = ( ! empty($_POST['id_away']) && is_array($_POST['id_away'])) ? $_POST['id_away'] : NULL;
	
	if ($id_fixture === 0) {
		$message = __('An error occurred with the fixture ID.', 'phpleague');
	}
	elseif ($id_home === NULL || $id_away === NULL) {
		$message = __('An error occurred with one of the input.', 'phpleague');
	} else {
		// Remove old data whatever
		$db->remove_matches_from_fixture($id_fixture);
		
		// We anticipe the success to give the chance to display
		// a message if it failed.
		$message = __('Match(es) updated successfully.', 'phpleague');
		$array   = array();
		
		// Insert new data
		for ($counter = 0; $counter < $nb_matches; $counter++) {
			// We cannot have the same team twice
			if ($id_home[$counter] == $id_away[$counter]) {
				$message = __('You cannot have the same team at home and away.', 'phpleague');
				break;
			} elseif (in_array($id_home[$counter], $array) || in_array($id_away[$counter], $array)) {
				$message = __('You cannot have the same team twice by fixture.', 'phpleague');
				break;
			}
			
			$db->add_matches_to_fixture($id_fixture, $id_home[$counter], $id_away[$counter]);
			
			// Add the teams in the array to check them later
			$array[] = $id_home[$counter];
			$array[] = $id_away[$counter];
		}
	}
}

$pagination = $fct->pagination($nb_fixtures, 1, $get_id_fixture, 'id_fixture');

$output .= $fct->form_open(admin_url($page_url));
$output .= '<div class="tablenav"><div class="alignleft actions">'.$fct->input('matches', __('Save', 'phpleague'), array('type' => 'submit', 'class' => 'button-secondary action')).'</div>';

if ($pagination) {
	$output .= '<div class="tablenav-pages">'.$pagination.'</div>';	
}

// Check if the fixture exists in matches table
$id_fixture = $db->get_fixture_id($get_id_fixture, $get_id_league, FALSE);

$i = $team_home = $team_away = 0;

$output .= '</div><table class="widefat">
	<thead>
	<tr><th colspan="2">'.$league_name.__(' - Fixture: ', 'phpleague').$get_id_fixture.'</th></tr>
	<tr><th style="text-align:center;">'.__('Home', 'phpleague').'</th><th style="text-align:center;">'.__('Away', 'phpleague').'</th></tr>
	</thead>';

foreach ($db->get_distinct_league_team($get_id_league) as $array) {
	$clubs_list[intval($array->club_id)] = esc_html($array->name);	
}

// Home & Away matches
for ($counter = $nb_matches; $counter > 0; $counter = $counter - 1) {
	
	// TODO: try to take it away from the for...
	foreach ($db->get_matches_by_fixture($id_fixture, $counter - 1) as $row) {
		$team_home = intval($row->id_team_home);
		$team_away = intval($row->id_team_away);
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

$output .= '</table>';
$output .= $fct->input('id_fixture', $id_fixture, array('type' => 'hidden'));
$output .= $fct->form_close();

$data[] = array(
	'menu'  => __('Matches', 'phpleague'),
	'title' => __('Matches of ', 'phpleague').$league_name,
	'text'  => $output,
	'class' => 'full'
);

echo $ctl->admin_container($menu, $data, $message);