<?php

/*
 * This file is part of the PHPLeague package.
 *
 * (c) Maxime Dizerens <mdizerens@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Variables
$get_option = ( ! empty($_GET['option'])    ? trim($_GET['option'])    : '');
$id_league  = ( ! empty($_GET['id_league']) ? (int) $_GET['id_league'] : 0);
$message    = array();

// Sub-requests
switch ($get_option)
{
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

// Fill-in the ranking table?
if ($get_option === 'generator' && $id_league)
{
    // Secure data
    $setting  = $db->get_league_settings($id_league);
    $max      = $db->get_max_fixtures_played($id_league);
    $nb_teams = (int) $setting->nb_teams;
    $pt_v     = (int) $setting->pt_victory;
    $pt_d     = (int) $setting->pt_draw;
    $pt_l     = (int) $setting->pt_defeat;
    $start    = 0;
    
    // Fill in the table
    $db->fill_league_table($id_league, $start, $max, $nb_teams, $pt_v, $pt_d, $pt_l);
    $message[] = __('Table updated successfully.', 'phpleague');
}
elseif (isset($_POST['add_league']) && check_admin_referer('phpleague')) // Add a new league
{
    // Secure data
    $year = ( ! empty($_POST['year'])) ? (int) $_POST['year'] : 0;
    $name = ( ! empty($_POST['name'])) ? trim($_POST['name']) : NULL;

    if ( ! preg_match('/^([0-9]{4})$/', $year))
    {
       $message[] = __('The year must be 4 digits.', 'phpleague');
    }
    elseif (in_array($name, array(NULL, FALSE, '')))
    {
        $message[] = __('The name cannot be empty.', 'phpleague');
    }
    elseif ($fct->valid_text($name, 3) === FALSE)
    {
        $message[] = __('The name must be alphanumeric and 3 characters long at least.', 'phpleague');
    }
    elseif ($db->is_league_unique($name, $year) === FALSE)
    {
        $message[] = __('The league is already in your database.', 'phpleague');
    }
    else
    {
        $db->add_league($name, $year);
        $message[] = __('League created successfully.', 'phpleague');
    }
}
elseif (isset($_POST['delete_league']) && check_admin_referer('phpleague')) // Delete a league
{
    // Secure data
    $id_league = ( ! empty($_POST['id_league'])) ? $_POST['id_league'] : 0;

    if ($id_league === 0)
    {
        $message[] = __('We are sorry but it seems that you did not select a league.', 'phpleague');
    }
    else
    {
        if (is_array($id_league))
        {
            $i = 0;
            foreach ($id_league as $value)
            {
                $db->delete_league($value);
                $i++;
            }
            
            if ($i === 1)
                $message[] = __('League deleted successfully.', 'phpleague');
            else
                $message[] = __('Leagues deleted successfully.', 'phpleague');
        }
    }
}

// Variables
$per_page   = 7;
$activation = ( ! empty($_GET['activation']) ? (int) $_GET['activation'] : 0);
$p_number   = ( ! empty($_GET['p_nb']) ? (int) $_GET['p_nb'] : 1);
$offset     = ($p_number - 1 ) * $per_page;
$total      = $db->count_leagues();
$pagination = $fct->pagination($total, $per_page, $p_number);
$page_url   = 'admin.php?page=phpleague_overview';
$output     = '';
$data       = array();
$menu       = array(__('Overview', 'phpleague') => '#');

if ($total == 0)
    $message[] = __('No league found in the database!', 'phpleague');
    
$output  = $fct->form_open(admin_url($page_url));
$output .= $fct->input('name', __('Name', 'phpleague'), array('readonly' => 'readonly', 'class' => 'default'));
$output .= $fct->input('year', __('Year', 'phpleague'), array('readonly' => 'readonly', 'class' => 'default'));
$output .= $fct->input('add_league', __('Create', 'phpleague'), array('type' => 'submit', 'class' => 'button'));
$output .= $fct->form_close();

$data[] = array(
    'menu'  => __('Overview', 'phpleague'),
    'title' => __('New League', 'phpleague'),
    'text'  => $output,
    'class' => 'full'
);

if ($activation === 1)
    $message[] = __('PHPLeague has been activated with success!', 'phpleague');

$output  = $fct->form_open(admin_url($page_url));
$output .= '<div class="tablenav top"><div class="alignleft actions">'
        .$fct->input('delete_league', __('Delete', 'phpleague'),
        array('type' => 'submit', 'class' => 'button')).'</div>';

if ($pagination)
    $output .= '<div class="tablenav-pages">'.$pagination.'</div>';

$output .= '</div><table class="widefat"><thead><tr>'
        .'<th class="check-column"><input type="checkbox"/></th>'
        .'<th>'.__('League', 'phpleague').'</th>'
        .'<th colspan="5">'.__('Options', 'phpleague').'</th></tr></thead><tbody>';
    
    foreach ($db->get_every_league($offset, $per_page) as $league)
    {
        $id      = (int) $league->id;
        $year    = (int) $league->year;
        $output .= '<tr '.$fct->alternate('', 'class="alternate"').'>'
                .'<th class="check-column"><input type="checkbox" name="id_league[]" value="'.$id.'" /></th>'
                .'<td>'.esc_html($league->name) .' '. $year.'/'.substr($year + 1, 2).'</td>'
                .'<td><a href="'.admin_url($page_url.'&option=team&id_league='.$id).'">'.__('Teams', 'phpleague').'</a></td>'
                .'<td><a href="'.admin_url($page_url.'&option=fixture&id_league='.$id).'">'.__('Fixtures', 'phpleague').
                '</a></td><td><a href="'.admin_url($page_url.'&option=match&id_league='.$id).'">'.__('Matches', 'phpleague').
                '</a></td><td><a href="'.admin_url($page_url.'&option=result&id_league='.$id).'">'.__('Results', 'phpleague').
                '</a></td><td><a href="'.admin_url($page_url.'&option=setting&id_league='.$id).'">'.__('Settings', 'phpleague').
                '</a></td></tr>';
    }

$output .= '</tbody></table>'.$fct->form_close();
$data[]  = array(
    'menu'  => __('Overview', 'phpleague'),
    'title' => __('Dashboard', 'phpleague'),
    'text'  => $output,
    'class' => 'full'
);

// Render the page
echo $ctl->admin_container($menu, $data, $message);