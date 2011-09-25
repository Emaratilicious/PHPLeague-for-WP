<?php
$get_option = ( ! empty($_GET['option']) ? trim($_GET['option']) : '');

switch ($get_option) {
	case 'team' :
		return require_once WP_PHPLEAGUE_PATH.'inc/admin/league_team.php';
		break;
	case 'fixture' :
		return require_once WP_PHPLEAGUE_PATH.'inc/admin/league_fixture.php';
		break;
	case 'match' :
		return require_once WP_PHPLEAGUE_PATH.'inc/admin/league_match.php';
		break;
	case 'result' :
		return require_once WP_PHPLEAGUE_PATH.'inc/admin/league_result.php';
		break;
	case 'setting' :
		return require_once WP_PHPLEAGUE_PATH.'inc/admin/league_setting.php';
		break;
	default :
		break;
}

// -- Instances & vars
$db  	 = new PHPLeague_Database();
$ctl 	 = new PHPLeague_Admin_Controller();
$fct 	 = new MWD_Plugin_Tools();
$message = '';

if ($get_option === 'generator' && ! empty($_GET['id_league'])) {

	$id 	   = intval($_GET['id_league']);
	$setting   = $db->get_league_settings($id);
	$nb_teams  = intval($setting->nb_teams);
	$pt_v	   = intval($setting->pt_victory);
	$pt_d 	   = intval($setting->pt_draw);
	$pt_l  	   = intval($setting->pt_defeat);
	$start     = 0;
	$cache     = 1;
	$max       = $db->get_max_fixtures_played($id);
	
	// fill in the league table
	$db->fill_league_table($id, $start, $max, $cache, $nb_teams, $pt_v, $pt_d, $pt_l);
	
	$output =
	'<table class="widefat">
		<thead>
			<tr>
				<th>'.__('Pos', 'phpleague').'</th>
				<th>'.__('Team', 'phpleague').'</th>
				<th>'.__('Pts', 'phpleague').'</th>
				<th>'.__('P', 'phpleague').'</th>
				<th>'.__('W', 'phpleague').'</th>
				<th>'.__('D', 'phpleague').'</th>
				<th>'.__('L', 'phpleague').'</th>
				<th>'.__('F', 'phpleague').'</th>
				<th>'.__('A', 'phpleague').'</th>
				<th>'.__('+/-', 'phpleague').'</th>
			</tr>
		</thead>
		<tbody>';

	$place = 1;

	foreach ($db->get_league_table_data('general', $id, 20) as $row) {
		$output .= '<tr>';
		$output .= '<td>'.$place.'</td>';
		$output .= '<td>'.esc_html($row->club_name).'</td>';
		$output .= '<td>'.intval($row->points).'</td>';
		$output .= '<td>'.intval($row->played).'</td>';
		$output .= '<td>'.intval($row->victory).'</td>';
		$output .= '<td>'.intval($row->draw).'</td>';
		$output .= '<td>'.intval($row->defeat).'</td>';
		$output .= '<td>'.intval($row->goal_for).'</td>';
		$output .= '<td>'.intval($row->goal_against).'</td>';
		$output .= '<td>'.intval($row->diff).'</td>';
		$output .= '</tr>';
	
		$place++;
	}

	$output .= '</tbody></table>';
	
	$message   = __('Table updated successfully.<br />'.$output, 'phpleague');

} elseif (isset($_POST['add_league']) && check_admin_referer('phpleague_nonce_admin')) {

	$year = ( ! empty($_POST['year'])) ? intval($_POST['year']) : 0;
	$name = ( ! empty($_POST['name'])) ? trim($_POST['name'])   : NULL;

	if ( ! preg_match("/^([0-9]{4})$/", $year))	{
		$message = __('The year must be 4 digits.', 'phpleague');
	} elseif (in_array($name, array(NULL, FALSE, ''))) {
		$message = __('The name cannot be empty.', 'phpleague');
	} elseif ( ! preg_match('/^[A-Za-z0-9_\-. ]{3,}$/', $name)) {
		$message = __('The name must be alphanumeric and 3 characters long at least.', 'phpleague');
	} elseif ($db->is_league_unique($name, $year) === FALSE) {
		$message = __('The league is already in your database.', 'phpleague');
	} else {
		$db->add_league($name, $year);
		$message = __('League added successfully.', 'phpleague');
	}
}

// -- Vars
$items_p_page  = 7;
$page_number   = ( ! empty($_GET['p_nb']) ? intval($_GET['p_nb']) : 1);
$offset 	   = ($page_number - 1 ) * $items_p_page;
$page_base_url = 'admin.php?page=phpleague_overview';
$total_items   = $db->count_leagues();
$pagination	   = $fct->pagination($total_items, $items_p_page, $page_number);
$menu 	   	   = array(__('Overview', 'phpleague') => '#', __('New League', 'phpleague') => '#');
$data 	   	   = array();

if ($total_items == 0) {
	$message = __('We did not find any league in the database.', 'phpleague');	
}

$output = '
<table class="widefat">
	<thead>
		<tr>
			<th>'.__('League', 'phpleague').'</th>
			<th colspan="6">'.__('Options', 'phpleague').'</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>'.__('League', 'phpleague').'</th>
			<th colspan="6">'.__('Options', 'phpleague').'</th>
		</tr>
	</tfoot>
	<tbody>';
	
	foreach ($db->get_every_league($offset, $items_p_page) as $league) {
		$id 	 = intval($league->id);
		$year 	 = intval($league->year);
		$output .= '
			<tr '.$fct->alternate('', 'class="alternate"').'>
				<td>
					<i>
					'.esc_html($league->name) .' '. $year.'/'.substr($year + 1, 2).'
					</i>
				</td>
				<td>
					<a href="'.admin_url($page_base_url.'&option=team&id_league='.$id).'">
					'.__('Teams', 'phpleague').'
					</a>
				</td>
				<td>
					<a href="'.admin_url($page_base_url.'&option=fixture&id_league='.$id).'">
					'.__('Fixtures', 'phpleague').'
					</a>
				</td>
				<td>
					<a href="'.admin_url($page_base_url.'&option=match&id_league='.$id).'">
					'.__('Matches', 'phpleague').'
					</a>
				</td>
				<td>
					<a href="'.admin_url($page_base_url.'&option=result&id_league='.$id).'">
					'.__('Results', 'phpleague').'
					</a>
				</td>
				<td>
					<a href="'.admin_url($page_base_url.'&option=setting&id_league='.$id).'">
					'.__('Settings', 'phpleague').'
					</a>
				</td>
				<td>
					<a href="'.admin_url($page_base_url.'&option=generator&id_league='.$id).'">
					'.__('Generator', 'phpleague').'
					</a>
				</td>
			</tr>
		';
	}
	
$output .= '</tbody></table>';

if ($pagination) {
	$output .= '<div class="tablenav"><div class="tablenav-pages">'.$pagination.'</div></div>';	
}

$data[] = array(
	'menu'  => __('Overview', 'phpleague'),
	'title' => __('Dashboard', 'phpleague'),
	'text'  => $output,
	'class' => 'full'
);

$output  = $fct->form_open(admin_url($page_base_url));
$output .= $fct->input('name', '');
$output .= __(' Only alphanumeric characters and spaces authorized.', 'phpleague');

$data[] = array(
	'menu'  => __('New League', 'phpleague'),
	'title' => __('Name', 'phpleague'),
	'text'  => $output,
	'class' => 'full'
);

$output  = $fct->input('year', '');
$output .= __(' The year must be 4 digits (e.g. 2008 for 2008/09).', 'phpleague');
$output .= $fct->input('add_league', __('Create', 'phpleague'), array('type' => 'submit', 'class' => 'button-secondary action'));
$output .= $fct->form_close();

$data[] = array(
	'menu'  => __('New League', 'phpleague'),
	'title' => __('Year', 'phpleague'),
	'text'  => $output,
	'class' => 'full'
);

echo $ctl->admin_container($menu, $data, $message);