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
$page_url    = 'admin.php?page=phpleague_overview&option=result&id_league='.$get_id_league.'&id_fixture='.$get_id_fixture;
$message	 = '';
$output		 = '';
$data 		 = array();
$menu 		 = array(
	__('Results', 'phpleague')  => '#',
	__('Teams', 'phpleague')    => admin_url('admin.php?page=phpleague_overview&option=team&id_league='.$get_id_league),
	__('Fixtures', 'phpleague') => admin_url('admin.php?page=phpleague_overview&option=fixture&id_league='.$get_id_league),
	__('Matches', 'phpleague')  => admin_url('admin.php?page=phpleague_overview&option=match&id_league='.$get_id_league),
	__('Settings', 'phpleague') => admin_url('admin.php?page=phpleague_overview&option=setting&id_league='.$get_id_league)
);

if (isset($_POST['results']) && check_admin_referer('phpleague_nonce_admin')) {
	$array  = ( ! empty($_POST['array']) && is_array($_POST['array'])) ? $_POST['array'] : NULL;
	
	foreach ($array as $item) {
		if ( ! (($item['goal_home'] == '') || ($item['goal_away'] == ''))) {
			$db->update_results($item['goal_home'], $item['goal_away'], $item['date'], $item['id_match']);
			$message = __('Results updated successfully with goals.', 'phpleague');
		} elseif (($item['goal_home'] == '') || ($item['goal_away'] == '')) {
			$db->update_results(NULL, NULL, $item['date'], $item['id_match']);
			$message = __('Results updated successfully without all goals.', 'phpleague');
		} else {
			$message = __('An error occurred during the results generation.', 'phpleague');
		}
	}
}

// Check what kind of number we have (odd/even)
if (($nb_teams % 2) != 0) {
	$nb_fixtures = $nb_teams * $nb_legs;
} else {
	$nb_fixtures = ($nb_teams * $nb_legs) - $nb_legs;
}

$pagination = $fct->pagination($nb_fixtures, 1, $get_id_fixture, 'id_fixture');

$output .= $fct->form_open(admin_url($page_url));
$output .= '<div class="tablenav"><div class="alignleft actions">'.$fct->input('results', __('Save', 'phpleague'), array('type' => 'submit', 'class' => 'button-secondary action')).'</div>';

if ($pagination) {
	$output .= '<div class="tablenav-pages">'.$pagination.'</div>';	
}

$output .= '</div><table class="widefat"><thead><tr><th colspan="5">'.$league_name.__(' - Fixture: ', 'phpleague').$get_id_fixture.'</th></tr></thead>';

foreach ($db->get_results_by_fixture($get_id_fixture, $get_id_league) as $key => $row) {
	$output .= '<tr '.$fct->alternate('', 'class="alternate"').'><td style="text-align:right;">'.esc_html($row->name_home).'</td>';
	$output .= '<td class="check-column">'.$fct->input('array['.$key.'][goal_home]', $row->goal_home, array('size' => 2)).'</td>';
	$output .= '<td class="check-column">'.$fct->input('array['.$key.'][goal_away]', $row->goal_away, array('size' => 2)).'</td>';
	$output .= '<td>'.esc_html($row->name_away).'</td>';
	$output .= '<td class="check-column">'.$fct->input('array['.$key.'][date]', esc_html($row->played), array('size' => 18)).
			$fct->input('array['.$key.'][id_match]', intval($row->match_id), array('type' => 'hidden')).'</td></tr>';
}

$output .= '</table>';
$output .= $fct->form_close();

$data[] = array(
	'menu'  => __('Results', 'phpleague'),
	'title' => __('Results of ', 'phpleague').$league_name,
	'text'  => $output,
	'class' => 'full'
);
echo $ctl->admin_container($menu, $data, $message);