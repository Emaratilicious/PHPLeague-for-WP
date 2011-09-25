<?php
// -- Vars
$get_id_club  = ( ! empty($_GET['id_club'])  ? intval($_GET['id_club'])  : 0);
$post_id_club = ( ! empty($_POST['id_club']) ? intval($_POST['id_club']) : 0);
$message   	  = '';
$menu 	   	  = array(__('Overview', 'phpleague') => '#');
$data 	   	  = array();

if (isset($_POST['edit_club']) && check_admin_referer('phpleague_nonce_admin') && $db->is_club_unique($post_id_club, 'id') === FALSE) {		
	// -- cleaned vars
	$message = __('Club information edited with success!', 'phpleague');
	$name 	 = (string) trim($_POST['club_name']);
	$venue 	 = (string) trim($_POST['club_venue']);
	$coach   = (string) trim($_POST['club_coach']);
	$country = intval($_POST['club_country']);
	$logo_b  = (string) trim($_POST['path_big_logo']);
	$logo_m  = (string) trim($_POST['path_mini_logo']);

	//-- security controls
	if ($get_id_club !== $post_id_club && ($get_id_club !== 0 || $post_id_club !== 0)) {
		$message = __('Busted! We got 2 different IDs which is not possible!', 'phpleague');
	} elseif ( ! preg_match('/^[A-Za-z0-9_\-. ]{3,}$/', $name)) {
		$message = __('The name must be alphanumeric and 3 characters long at least.', 'phpleague');
	} elseif ( ! preg_match('/^[A-Za-z0-9_\-. ]{3,}$/', $venue)) {
		$message = __('The venue must be alphanumeric and 3 characters long at least.', 'phpleague');
	} elseif ( ! preg_match('/^[A-Za-z0-9_\-. ]{3,}$/', $coach)) {
		$message = __('The coach must be alphanumeric and 3 characters long at least.', 'phpleague');
	} else {
		$db->update_club_information($post_id_club, $name, $country, $coach, $venue, $logo_b, $logo_m);
	}
}

// Get every country as an array
foreach ($db->get_every_country(0, 250, 'ASC') as $array) {
	$countries_list[intval($array->id)] = esc_html($array->name);	
}

$club_info = $db->get_club_information($get_id_club);
$output    = $fct->form_open(admin_url('admin.php?page=phpleague_club&id_club='.$get_id_club));
$table     =
	'<table class="form-table">
		<tr>
			<td>'.__('Club Name:', 'phpleague').'</td>
			<td>'.$fct->input('club_name', esc_html($club_info->name)).'</td>
			<td>'.__('Club Venue:', 'phpleague').'</td>
			<td>'.$fct->input('club_venue', esc_html($club_info->venue)).'</td>
		</tr>
		<tr>
			<td>'.__('Coach Name:', 'phpleague').'</td> 
			<td>'.$fct->input('club_coach', esc_html($club_info->coach)).'</td>
			<td>'.__('Country:', 'phpleague').'</td>
			<td>'.$fct->select('club_country', $countries_list, intval($club_info->id_country)).'</td>
		</tr>
		<tr>
			<td>'.__('Path Big Logo:', 'phpleague').'</td>
			<td>'.$fct->input('path_big_logo', esc_html($club_info->logo_big)).'</td>
			<td>'.__('Path Mini Logo:', 'phpleague').'</td>
			<td>'.$fct->input('path_mini_logo', esc_html($club_info->logo_mini)).'</td>
		</tr>
 	</table>
	<div class="submit">
		'.$fct->input('id_club', $get_id_club, array('type' => 'hidden')).'
		'.$fct->input('edit_club', __('Save', 'phpleague'), array('type' => 'submit')).'
	</div>';
	
$output .= $table;
$output .= $fct->form_close();

$data[] = array(
	'menu'  => __('Overview', 'phpleague'),
	'title' => __('Edit the Club', 'phpleague'),
	'text'  => $output,
	'class' => 'full'
);

echo $ctl->admin_container($menu, $data, $message);