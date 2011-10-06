<?php

/*
 * This file is part of the PHPLeague package.
 *
 * (c) Maxime Dizerens <mdizerens@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Vars
$id_club  = ( ! empty($_GET['id_club']) ? intval($_GET['id_club']) : 0);
$message  = array();
$data     = array();
$menu 	  = array(__('Club Information', 'phpleague') => '#');

// Do we have to handle some data?
if (isset($_POST['edit_club']) && check_admin_referer('phpleague') && $db->is_club_unique($id_club, 'id') === FALSE)
{
	// Security
	$name 	  = (string) trim($_POST['name']);
	$venue 	  = (string) trim($_POST['venue']);
	$coach    = (string) trim($_POST['coach']);
    $website  = (string) trim($_POST['website']);
	$logo_b   = (string) trim($_POST['path_b_logo']);
	$logo_m   = (string) trim($_POST['path_m_logo']);
	$creation = intval($_POST['creation']);
    $country  = intval($_POST['country']);
    
    // Coach and venue field not required but secured
    if ($fct->valid_text($venue, 5) === FALSE)
    {
        $venue = '';
        $message[] = __('The venue must be alphanumeric and 5 characters long at least (optional).', 'phpleague'); 
    }
    
    if ($fct->valid_text($coach, 5) === FALSE)
    {
        $coach = '';
        $message[] = __('The coach must be alphanumeric and 5 characters long at least (optional).', 'phpleague');
    }
    
    if ( ! preg_match('/^([0-9]{4})$/', $creation))
    {
        $creation = '0000';
        $message[] = __('The creation date must be 4 digits (optional).', 'phpleague');
    }
    
    if ( ! filter_var($website, FILTER_VALIDATE_URL))
    {
        $website = '';
        $message[] = __('The website is not valid (optional).', 'phpleague');
    }

    // We need to pass those tests to insert the data
	if ($id_club === 0) {
	   $message[] = __('Busted! We got 2 different IDs which is not possible!', 'phpleague');
	} elseif ($fct->valid_text($name, 3) === FALSE) {
	   $message[] = __('The name must be alphanumeric and 3 characters long at least.', 'phpleague');
	} else {
        $db->update_club_information($id_club, $name, $country, $coach, $venue, $creation, $website, $logo_b, $logo_m);
        $message[] = __('Club information edited with success!', 'phpleague');
    }
}

// Get every country
foreach ($db->get_every_country(0, 250, 'ASC') as $array) {
    $countries_list[$array->id] = esc_html($array->name);
}

$logo_m_list = $fct->return_dir_files(WP_PHPLEAGUE_LOGOS_PATH.'logo_mini/');
$logo_b_list = $fct->return_dir_files(WP_PHPLEAGUE_LOGOS_PATH.'logo_big/');
$club_info 	 = $db->get_club_information($id_club);
$output    	 = $fct->form_open(admin_url('admin.php?page=phpleague_club&id_club='.$id_club));
$table     	 =
	'<table class="form-table">
		<tr>
			<td class="required">'.__('Club Name:', 'phpleague').'</td>
			<td>'.$fct->input('name', esc_html($club_info->name)).'</td>
			<td>'.__('Club Venue:', 'phpleague').'</td>
			<td>'.$fct->input('venue', esc_html($club_info->venue)).'</td>
		</tr>
		<tr>
			<td>'.__('Coach Name:', 'phpleague').'</td> 
			<td>'.$fct->input('coach', esc_html($club_info->coach)).'</td>
			<td>'.__('Creation Year:', 'phpleague').'</td>
            <td>'.$fct->input('creation', intval($club_info->creation)).'</td>
		</tr>
		<tr>
            <td>'.__('Club Website:', 'phpleague').'</td>
            <td>'.$fct->input('website', esc_html($club_info->website)).'</td>
            <td class="required">'.__('Country:', 'phpleague').'</td>
            <td>'.$fct->select('country', $countries_list, intval($club_info->id_country)).'</td>
        </tr>
		<tr>
			<td>'.__('Path Big Logo:', 'phpleague').'</td>
			<td>'.$fct->select('path_b_logo', $logo_b_list, esc_html($club_info->logo_big)).'</td>
			<td>'.__('Path Mini Logo:', 'phpleague').'</td>
			<td>'.$fct->select('path_m_logo', $logo_m_list, esc_html($club_info->logo_mini)).'</td>
		</tr>
 	</table>
	<div class="submit">
		'.$fct->input('id_club', $id_club, array('type' => 'hidden')).'
		'.$fct->input('edit_club', __('Save', 'phpleague'), array('type' => 'submit')).'
	</div>';
	
$output .= $table;
$output .= $fct->form_close();

$data[] = array(
	'menu'  => __('Club Information', 'phpleague'),
	'title' => __('Club Information', 'phpleague'),
	'text'  => $output,
	'class' => 'full'
);

echo $ctl->admin_container($menu, $data, $message);