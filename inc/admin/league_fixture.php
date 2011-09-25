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
$league_name = $db->return_league_name($get_id_league);
$setting	 = $db->get_league_settings($get_id_league);
$nb_teams	 = intval($setting->nb_teams);
$nb_legs 	 = intval($setting->nb_leg);
$message     = '';
$output      = '';
$data 	     = array();
$menu 		 = array(
 	__('Fixtures', 'phpleague') => '#',
	__('Teams', 'phpleague')    => admin_url('admin.php?page=phpleague_overview&option=team&id_league='.$get_id_league),
	__('Matches', 'phpleague')  => admin_url('admin.php?page=phpleague_overview&option=match&id_league='.$get_id_league),
	__('Results', 'phpleague')  => admin_url('admin.php?page=phpleague_overview&option=result&id_league='.$get_id_league),
	__('Settings', 'phpleague') => admin_url('admin.php?page=phpleague_overview&option=setting&id_league='.$get_id_league)
);

if (($nb_teams % 2) != 0) {
	$message = __('Be aware that your league has an odd number of teams.', 'phpleague');	
}

if (isset($_POST['fixtures']) && check_admin_referer('phpleague_nonce_admin')) {
	$schedule = ( ! empty($_POST['schedule']) && is_array($_POST['schedule'])) ? $_POST['schedule'] : NULL;
	
	if ($schedule === NULL) {
		$message = __('The fixtures format is not good!', 'phpleague');
	} else {
		foreach ($schedule as $key => $scheduled) {
			// we don't want to start from 0
			$number = $key + 1;

			// Add the new fixtures in the db
			$db->edit_league_fixtures($number, $scheduled, $get_id_league);

			// We update the default datetime
			// TODO: Make the time configurable somewhere else
			foreach ($db->get_fixture_id($number, $get_id_league) as $row) {
				$db->edit_game_datetime($scheduled.' 13:00:00', $row->fixture_id);
			}
		}
		
		$message = __('Fixtures updated successfully!', 'phpleague');
	}
}

if ($nb_teams == 0 || $nb_teams == 1) {
	$message = __('It seems that '.$league_name.' has no team registered or only one.', 'phpleague');	
}
else {	
	$output = $fct->form_open(admin_url('admin.php?page=phpleague_overview&option=fixture&id_league='.$get_id_league));
	
	// count the number of fixture per league
	$nb_fixtures = $db->nb_fixtures_league($get_id_league);
	
	if (($nb_teams % 2) != 0) {
		$fixtures_number = $nb_teams * $nb_legs;
	} else {
		$fixtures_number = ($nb_teams * $nb_legs) - $nb_legs;
	}

	// small security check
	if ($nb_fixtures != $fixtures_number) {
		$db->remove_fixtures_league($get_id_league);

		$number = 1;
		while ($number <= $fixtures_number) {
			$db->add_fixtures_league($number, $get_id_league);
			$number++;
		}
	}
	
	// vars useful to order fixtures
	$column = 1;
	$first = $second = $third = $fourth = '';
	
	$output .= '<p>'.$fct->input('fixtures', __('Save', 'phpleague'), array('type' => 'submit', 'class' => 'button-secondary action')).'</p>';
			
	foreach ($db->get_fixtures_league($get_id_league) as $key => $row) {
		$col  = '<label for="schedule['.$key.']">'.__('Fixture: ', 'phpleague').esc_html($row->number).'</label>';
		$col .= $fct->input(
					'schedule['.$key.']',
					esc_html($row->scheduled),
					array('size' => '10', 'tabindex' => $key + 1, 'id' => 'schedule['.$key.']')
				);

		switch ($column) {
			case 1 :
				$first .= $col;
				$column	= 2;
				break;
			case 2 :
				$second .= $col;
				$column	 = 3;
				break;
			case 3 :
				$third  .= $col;
				$column	 = 4;
				break;
			case 4 :
				$fourth .= $col;
				$column	 = 1;
				break;
		}
	}
	
	$first   = $ctl->admin_wrapper(24, $first);
	$second  = $ctl->admin_wrapper(24, $second);
	$third 	 = $ctl->admin_wrapper(24, $third);
	$fourth  = $ctl->admin_wrapper(24, $fourth);
	$output .= $first.$second.$third.$fourth;
	$output .= $fct->form_close();
}

$data[] = array(
	'menu'  => __('Fixtures', 'phpleague'),
	'title' => __('Fixtures of ', 'phpleague').$league_name,
	'text'  => $output,
	'class' => 'full'
);

echo $ctl->admin_container($menu, $data, $message);