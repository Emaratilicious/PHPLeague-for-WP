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
$get_option = ( ! empty($_GET['option'])    ? trim($_GET['option'])      : '');
$id_league  = ( ! empty($_GET['id_league']) ? intval($_GET['id_league']) : 0);
$message    = array();

// Sub-requests
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

// Can we generate the table?
if ($get_option === 'generator' && $id_league) {
    // Secure vars...
    $setting  = $db->get_league_settings($id_league);
    $max      = $db->get_max_fixtures_played($id_league);
    $nb_teams = intval($setting->nb_teams);
    $pt_v     = intval($setting->pt_victory);
    $pt_d     = intval($setting->pt_draw);
    $pt_l     = intval($setting->pt_defeat);
    $start    = 0;
    
    // Fill in the table
    $db->fill_league_table($id_league, $start, $max, $nb_teams, $pt_v, $pt_d, $pt_l);
    $message[] = __('Table updated successfully.', 'phpleague');
} elseif (isset($_POST['add_league']) && check_admin_referer('phpleague')) {
    // Secure vars...
    $year = ( ! empty($_POST['year'])) ? intval($_POST['year']) : 0;
    $name = ( ! empty($_POST['name'])) ? trim($_POST['name'])   : NULL;
    if ( ! preg_match('/^([0-9]{4})$/', $year)) {
       $message[] = __('The year must be 4 digits.', 'phpleague');
    } elseif (in_array($name, array(NULL, FALSE, ''))) {
        $message[] = __('The name cannot be empty.', 'phpleague');
    } elseif ($fct->valid_text($name, 3) === FALSE) {
        $message[] = __('The name must be alphanumeric and 3 characters long at least.', 'phpleague');
    } elseif ($db->is_league_unique($name, $year) === FALSE) {
        $message[] = __('The league is already in your database.', 'phpleague');
    } else {
        $db->add_league($name, $year);
        $message[] = __('League added successfully. You are strongly recommended going to edit your settings straight away.', 'phpleague');
    }
}

// Vars
$per_page   = 7;
$p_number   = ( ! empty($_GET['p_nb']) ? intval($_GET['p_nb']) : 1);
$offset 	= ($p_number - 1 ) * $per_page;
$total      = $db->count_leagues();
$pagination	= $fct->pagination($total, $per_page, $p_number);
$page_url   = 'admin.php?page=phpleague_overview';
$output     = '';
$data       = array();
$menu 	   	= array(__('Overview', 'phpleague') => '#');

if ($total == 0)
	$message[] = __('No league found in the database! Click on "New League" to add one.', 'phpleague');
	
$output  = $fct->form_open(admin_url($page_url));
$output .= $fct->input('name', __('League Name', 'phpleague'), array('readonly' => 'readonly'));
$output .= $fct->input('year', __('League Year', 'phpleague'), array('readonly' => 'readonly'));
$output .= $fct->input('add_league', __('Create', 'phpleague'), array('type' => 'submit', 'class' => 'button'));
$output .= $fct->form_close();

$data[] = array(
    'menu'  => __('Overview', 'phpleague'),
    'title' => __('New League', 'phpleague'),
    'text'  => $output,
    'hide'  => TRUE,
    'class' => 'full'
);

$output = '';
if ($pagination)
    $output .= '<div class="tablenav"><div class="tablenav-pages">'.$pagination.'</div></div>'; 

$output .= '
<table class="widefat">
    <thead>
        <tr>
            <th class="check-column"><input type="checkbox"/></th>
            <th>'.__('League', 'phpleague').'</th>
            <th colspan="5">'.__('Options', 'phpleague').'</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th class="check-column"><input type="checkbox"/></th>
            <th>'.__('League', 'phpleague').'</th>
            <th colspan="5">'.__('Options', 'phpleague').'</th>
        </tr>
    </tfoot>
    <tbody>';
    
    foreach ($db->get_every_league($offset, $per_page) as $league) {
        $id   = intval($league->id);
        $year = intval($league->year);
        $output .= '
        <tr '.$fct->alternate('', 'class="alternate"').'>
            <th class="check-column"><input type="checkbox" name="league_id[]" value="'.$id.'" /></th>
            <td>'.esc_html($league->name) .' '. $year.'/'.substr($year + 1, 2).'</td>
            <td>
                <a href="'.admin_url($page_url.'&option=team&id_league='.$id).'">
                '.__('Teams', 'phpleague').'
                </a>
            </td>
            <td>
                <a href="'.admin_url($page_url.'&option=fixture&id_league='.$id).'">
                '.__('Fixtures', 'phpleague').'
                </a>
            </td>
            <td>
                <a href="'.admin_url($page_url.'&option=match&id_league='.$id).'">
                '.__('Matches', 'phpleague').'
                </a>
            </td>
            <td>
                <a href="'.admin_url($page_url.'&option=result&id_league='.$id).'">
                '.__('Results', 'phpleague').'
                </a>
            </td>
            <td>
                <a href="'.admin_url($page_url.'&option=setting&id_league='.$id).'">
                '.__('Settings', 'phpleague').'
                </a>
            </td>
        </tr>';
    }

$output .= '</tbody></table>';
$data[]  = array(
    'menu'  => __('Overview', 'phpleague'),
    'title' => __('Dashboard', 'phpleague'),
    'text'  => $output,
    'class' => 'full'
);

echo $ctl->admin_container($menu, $data, $message);