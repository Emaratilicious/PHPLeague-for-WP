<?php
// -- Instances
$db  = new PHPLeague_Database();
$ctl = new PHPLeague_Admin_Controller();
$fct = new MWD_Plugin_Tools();

// -- $_GET
$get_id_league = ( ! empty($_GET['id_league']) ? intval($_GET['id_league']) : 0);

if ($db->is_league_exists($get_id_league) === FALSE) {
	wp_die(__('We did not find the league in the database.', 'phpleague'));
}

// -- Vars
$league_name   = $db->return_league_name($get_id_league);
$nb_clubs	   = $db->count_clubs();
$message	   = '';
$clubs_list	   = array();
$data 		   = array();
$menu 		   = array(
	__('Teams', 'phpleague')    => '#',
	__('Fixtures', 'phpleague') => admin_url('admin.php?page=phpleague_overview&option=fixture&id_league='.$get_id_league),
	__('Matches', 'phpleague')  => admin_url('admin.php?page=phpleague_overview&option=match&id_league='.$get_id_league),
	__('Results', 'phpleague')  => admin_url('admin.php?page=phpleague_overview&option=result&id_league='.$get_id_league),
	__('Settings', 'phpleague') => admin_url('admin.php?page=phpleague_overview&option=setting&id_league='.$get_id_league),
);

if (isset($_POST['add_club']) && check_admin_referer('phpleague_nonce_admin')) {
	$id_club = intval($_POST['id_club']);
	
	if ($db->is_club_unique($id_club, 'id') === TRUE) {
		$message = __('We did not find the club in the database.', 'phpleague');
	} elseif ($db->is_club_already_in_league($get_id_league, $id_club) === FALSE) {
		$message = __('You cannot add twice the same club in a league.', 'phpleague');
	} else {
		$db->add_club_in_league($get_id_league, $id_club);
		// edit the number of teams in the league
		$db->update_nb_teams(1, $get_id_league);
		$message = __('Club added successfully in ', 'phpleague').$league_name;
	}
} elseif (isset($_POST['remove_club']) && check_admin_referer('phpleague_nonce_admin')) {
	$id_club = ( ! empty($_POST['id_club']) && is_array($_POST['id_club'])) ? $_POST['id_club'] : 0;
	
	if ($id_club === 0) {
		// check that the format is correct
		$message = __('We are sorry but it seems that you did not select a club.', 'phpleague');
	} else {
		$i = 0;
		foreach ($id_club as $value) {
			$db->remove_club_from_league($value);
			$i++;
		}
		// edit the number of teams in the league
		$db->update_nb_teams($i, $get_id_league, 'minus');
		
		if ($i === 1) {
			$message = __('Club removed successfully from ', 'phpleague').$league_name;
		} else {
			$message = __('Clubs removed successfully from ', 'phpleague').$league_name;
		}
	}
}

// -- Vars
$items_p_page = 10;
$page_number  = ( ! empty($_GET['p_nb']) ? intval($_GET['p_nb']) : 1);
$offset 	  = ($page_number - 1 ) * $items_p_page;
$setting	  = $db->get_league_settings($get_id_league);
$total_items  = intval($setting->nb_teams);
$pagination	  = $fct->pagination($total_items, $items_p_page, $page_number);

foreach ($db->get_every_club(0, $nb_clubs, 'ASC', FALSE) as $array) {
	$clubs_list[intval($array->id)] = esc_html($array->name);	
}

$output  = $fct->form_open(admin_url('admin.php?page=phpleague_overview&option=team&id_league='.$get_id_league));
$output .= $fct->select('id_club', $clubs_list).$fct->input('add_club', __('Send', 'phpleague'), array('type' => 'submit', 'class' => 'button-secondary action'));
$output .= __(' Your league has currently <b>'.$total_items.'</b> teams.', 'phpleague');
$output .= $fct->form_close();

$data[] = array(
	'menu'  => __('Teams', 'phpleague'),
	'title' => __('Add a Team in ', 'phpleague').$league_name,
	'text'  => $output,
	'class' => 'full'
);

$output = $fct->form_open(admin_url('admin.php?page=phpleague_overview&option=team&id_league='.$get_id_league));
$output .= '<div class="tablenav top">
	<div class="alignleft actions">'.$fct->input('remove_club', __('Remove', 'phpleague'), array('type' => 'submit', 'class' => 'button-secondary action')).'</div>';
	
if ($pagination) {
	$output .= '<div class="tablenav-pages">'.$pagination.'</div>';	
}

$output .=
	'</div><table class="widefat">
		<thead>
			<tr>
				<th class="check-column"><input type="checkbox"/></th>
				<th class="manage-column">'.__('ID', 'phpleague').'</th>
				<th>'.__('Name', 'phpleague').'</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th class="check-column"><input type="checkbox"/></th>
				<th class="manage-column">'.__('ID', 'phpleague').'</th>
				<th>'.__('Name', 'phpleague').'</th>
			</tr>
		</tfoot>
		<tbody>';
		
		foreach ($db->get_every_club_in_league($get_id_league, TRUE, $offset, $items_p_page) as $club) {
			$output .= '
				<tr '.$fct->alternate('', 'class="alternate"').'>
					<th class="check-column"><input type="checkbox" name="id_club[]" value="'.intval($club->id).'" /></th>
					<td class="check-column">'.intval($club->id).'</td>
					<td>'.esc_html($club->name).'</td>
				</tr>';
		}

$output .= '</tbody></table>';
$output .= $fct->form_close();

$data[] = array(
	'menu'  => __('Teams', 'phpleague'),
	'title' => __('Teams in ', 'phpleague').$league_name,
	'text'  => $output,
	'class' => 'full'
);

echo $ctl->admin_container($menu, $data, $message);