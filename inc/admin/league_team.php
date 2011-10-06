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

// Vars
$league_name = $db->return_league_name($id_league);
$nb_clubs	 = $db->count_clubs();
$clubs_list	 = array();
$data 		 = array();
$menu        = array(
    __('Teams', 'phpleague')    => '#',
    __('Fixtures', 'phpleague') => admin_url('admin.php?page=phpleague_overview&option=fixture&id_league='.$id_league),
    __('Matches', 'phpleague')  => admin_url('admin.php?page=phpleague_overview&option=match&id_league='.$id_league),
    __('Results', 'phpleague')  => admin_url('admin.php?page=phpleague_overview&option=result&id_league='.$id_league),
    __('Settings', 'phpleague') => admin_url('admin.php?page=phpleague_overview&option=setting&id_league='.$id_league)
);

// Do we have to handle some data?
if (isset($_POST['add_club']) && check_admin_referer('phpleague'))
{
	$id_club = intval($_POST['id_club']);
	
	if ($db->is_club_unique($id_club, 'id') === TRUE) {
		$message[] = __('We did not find the club in the database.', 'phpleague');
	} elseif ($db->is_club_already_in_league($id_league, $id_club) === FALSE) {
		$message[] = __('You cannot add twice the same club in a league.', 'phpleague');
	} else {
		$db->add_club_in_league($id_league, $id_club);
		$db->update_nb_teams(1, $id_league); // Edit the number of teams in the league
		$message[] = __('Club added successfully in ', 'phpleague').$league_name;
	}
}
elseif (isset($_POST['remove_club']) && check_admin_referer('phpleague'))
{
	$id_club = ( ! empty($_POST['id_club']) && is_array($_POST['id_club'])) ? $_POST['id_club'] : 0;
	
	if ($id_club === 0)
	{
		// Check that the format is correct
		$message[] = __('We are sorry but it seems that you did not select a club.', 'phpleague');
	}
	else
	{
		$i = 0;
		foreach ($id_club as $value)
		{
			$db->remove_club_from_league($value);
			$i++;
		}
        
		// Edit the number of teams in the league
		$db->update_nb_teams($i, $id_league, 'minus');
		
		if ($i === 1)
			$message[] = __('Club removed successfully from ', 'phpleague').$league_name;
		else
			$message[] = __('Clubs removed successfully from ', 'phpleague').$league_name;
	}
}

// Vars
$per_page   = 10;
$page       = ( ! empty($_GET['p_nb']) ? intval($_GET['p_nb']) : 1);
$offset 	= ($page - 1 ) * $per_page;
$setting	= $db->get_league_settings($id_league);
$total      = intval($setting->nb_teams);
$pagination = $fct->pagination($total, $per_page, $page);

foreach ($db->get_every_club(0, $nb_clubs, 'ASC', TRUE) as $array) {
	$clubs_list[$array->country][$array->id] = esc_html($array->name);	
}

$output  = $fct->form_open(admin_url('admin.php?page=phpleague_overview&option=team&id_league='.$id_league));
$output .= $fct->select('id_club', $clubs_list).$fct->input('add_club', __('Send', 'phpleague'), array('type' => 'submit', 'class' => 'button-secondary action'));
$output .= __(' Your league has currently <b>'.$total.'</b> teams.', 'phpleague');
$output .= $fct->form_close();

$data[] = array(
	'menu'  => __('Teams', 'phpleague'),
	'title' => __('Add a Team in ', 'phpleague').$league_name,
	'text'  => $output,
	'class' => 'full'
);

$output = $fct->form_open(admin_url('admin.php?page=phpleague_overview&option=team&id_league='.$id_league));
$output .= '<div class="tablenav top">
	<div class="alignleft actions">'.$fct->input('remove_club', __('Remove', 'phpleague'), array('type' => 'submit', 'class' => 'button-secondary action')).'</div>';
	
if ($pagination)
	$output .= '<div class="tablenav-pages">'.$pagination.'</div>';

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
		
		foreach ($db->get_every_club_in_league($id_league, TRUE, $offset, $per_page) as $club)
		{
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