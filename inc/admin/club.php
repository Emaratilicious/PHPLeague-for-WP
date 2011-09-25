<?php
// -- Instances
$db  = new PHPLeague_Database();
$ctl = new PHPLeague_Admin_Controller();
$fct = new MWD_Plugin_Tools();

$id_club  = ( ! empty($_GET['id_club']) ? intval($_GET['id_club']) : '');

if ($db->is_club_unique($id_club, 'id') === FALSE) {
	return require_once WP_PHPLEAGUE_PATH.'inc/admin/club_edit.php';	
}

// -- Vars
$items_p_page  = 7;
$page_number   = ( ! empty($_GET['p_nb']) ? intval($_GET['p_nb']) : 1);
$offset 	   = ($page_number - 1 ) * $items_p_page;
$total_items   = $db->count_clubs();
$page_base_url = 'admin.php?page=phpleague_club';
$pagination	   = $fct->pagination($total_items, $items_p_page, $page_number);
$get_post 	   = ( ! empty($_POST['do']) ? $_POST['do'] : '');
$message	   = '';
$menu 		   = array(__('Overview', 'phpleague') => '#', __('New Club', 'phpleague') => '#');
$data 		   = array();

// Get every club as an array
foreach ($db->get_every_country(0, 250, 'ASC') as $array) {
	$countries_list[intval($array->id)] = esc_html($array->name);	
}

if ($get_post === 'new_club' && check_admin_referer('phpleague_nonce_admin')) {
	//-- cleaned vars
	$club 	 = (string) trim($_POST['club_name']);
	$country = intval($_POST['club_country']);

	if (in_array($club, array(NULL, FALSE, ''))) {
		$message = __('The name cannot be empty.', 'phpleague');
	} elseif ( ! preg_match('/^[A-Za-z0-9_\-. ]{3,}$/', $club)) {
		$message = __('The name must be alphanumeric and 3 characters long at least.', 'phpleague');
	} elseif ($db->is_club_unique($club, 'name') === FALSE) {
		$message = __('The club '.$club.' is already in your database.', 'phpleague');
	} else {
		$message = __('Club added successfully.', 'phpleague');
		$db->add_club($club, $country);
	}
}

if ($total_items == 0) {
	$message = __('We did not find any club in the database.', 'phpleague');	
}

$output = '
<table class="widefat">
	<thead>
		<tr>
			<th class="check-column">'.__('ID', 'phpleague').'</th>
			<th>'.__('Name', 'phpleague').'</th>
			<th>'.__('Country', 'phpleague').'</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th class="check-column">'.__('ID', 'phpleague').'</th>
			<th>'.__('Name', 'phpleague').'</th>
			<th>'.__('Country', 'phpleague').'</th>
		</tr>
	</tfoot>
	<tbody>';
	
	foreach ($db->get_every_club($offset, $items_p_page, 'ASC', TRUE) as $club) {
		$output .= '
			<tr '.$fct->alternate('', 'class="alternate"').'>
				<td>'.intval($club->id).'</td>
				<td>
					<a href="'.admin_url($page_base_url.'&id_club='.intval($club->id)).'">
						'.esc_html($club->name).'
					</a>
				</td>
				<td>'.esc_html($club->country).'</td>
			</tr>';
	}

$output .= '</tbody></table>';

if ($pagination) {
	$output .= '<div class="tablenav"><div class="tablenav-pages">'.$pagination.'</div></div>';	
}

$data[] = array(
	'menu'  => __('Overview', 'phpleague'),
	'title' => __('Clubs Listing', 'phpleague'),
	'text'  => $output,
	'class' => 'full'
);

$output  = $fct->form_open(admin_url('admin.php?page=phpleague_club'));
$output .= $fct->input('club_name', '', array('size' => 15)).
	$fct->select('club_country', $countries_list).
	$fct->input('do', 'new_club', array('type' => 'hidden')).
	$fct->input('club', __('Create', 'phpleague'), array('type' => 'submit', 'class' => 'button-secondary action'));
$output .= __(' Choose a name then a country for your club.', 'phpleague');
$output .= $fct->form_close();

$data[] = array(
	'menu'  => __('New Club', 'phpleague'),
	'title' => __('Insert a new Club', 'phpleague'),
	'text'  => $output,
	'class' => 'full'
);

echo $ctl->admin_container($menu, $data, $message);