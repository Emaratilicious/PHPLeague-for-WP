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

// Variables
$league_name = $db->return_league_name($id_league);
$setting     = $db->get_league_settings($id_league);
$nb_teams    = (int) $setting->nb_teams;
$nb_legs     = (int) $setting->nb_leg;
$output      = '';
$data        = array();
$menu        = array(
    __('Teams', 'phpleague')    => admin_url('admin.php?page=phpleague_overview&option=team&id_league='.$id_league),
    __('Fixtures', 'phpleague') => '#',
    __('Matches', 'phpleague')  => admin_url('admin.php?page=phpleague_overview&option=match&id_league='.$id_league),
    __('Results', 'phpleague')  => admin_url('admin.php?page=phpleague_overview&option=result&id_league='.$id_league),
    __('Settings', 'phpleague') => admin_url('admin.php?page=phpleague_overview&option=setting&id_league='.$id_league)
);

// Data processing...
if (isset($_POST['fixtures']) && check_admin_referer('phpleague'))
{
    // Secure data
    $post_years  = ( ! empty($_POST['year']) && is_array($_POST['year']))   ? $_POST['year']  : NULL;
    $post_months = ( ! empty($_POST['month']) && is_array($_POST['month'])) ? $_POST['month'] : NULL;
    $post_days   = ( ! empty($_POST['day']) && is_array($_POST['day']))     ? $_POST['day']   : NULL;
    
    if ($post_years === NULL || $post_months === NULL || $post_days === NULL)
    {
        $message[] = __('The fixtures format is not good!', 'phpleague');   
    }
    else
    {
        // It does not matter which one we count...
        $count = count($post_years);
        for ($i = 1; $i <= $count; $i++)
        {
            // We get each data separately...
            $year  = (int) $post_years[$i];
            $month = (int) $post_months[$i];
            $day   = (int) $post_days[$i];

            // Bring the data together
            $date  = $year.'-'.$month.'-'.$day;

            // Edit the fixtures in the database...
            $db->edit_league_fixtures($i, $date, $id_league);

            // We update the match datetime using the default time
            foreach ($db->get_fixture_id($i, $id_league) as $row)
            {
                $db->edit_game_datetime($date.' '.$setting->default_time, $row->fixture_id);
            }
        }
        
        $message[] = __('Fixtures updated successfully!', 'phpleague');
    }
}

// Odd number of teams is probably not desired...
if (($nb_teams % 2) != 0)
    $message[] = __('Be aware that your league has an odd number of teams.', 'phpleague');

// We need to have at least 2 teams...
if ($nb_teams == 0 || $nb_teams == 1)
{
    $message[] = __('It seems that '.$league_name.' does not have teams registered or only one.', 'phpleague');
}
else
{   
    $output = $fct->form_open(admin_url('admin.php?page=phpleague_overview&option=fixture&id_league='.$id_league));
    
    // Count how many fixtures in the league
    $nb_fixtures = $db->nb_fixtures_league($id_league);
    
    if (($nb_teams % 2) != 0)
        $fixtures_number = $nb_teams * $nb_legs;
    else
        $fixtures_number = ($nb_teams * $nb_legs) - $nb_legs;

    // Security check
    if ($nb_fixtures != $fixtures_number)
    {
        // We removed the "old" data
        $db->remove_fixtures_league($id_league);

        // Add the new fixtures in the database...
        $number = 1;
        while ($number <= $fixtures_number)
        {
            $db->add_fixtures_league($number, $id_league);
            $number++;
        }
    }

    // Years list
    for ($i = 1900; $i <= 2050; $i++)
    {
        $years[$i] = $i;
    }

    // Months list
    for ($i = 1; $i <= 12; $i++)
    {
        $months[$i] = $i;
    }

    // Days list
    for ($i = 1; $i <= 31; $i++)
    {
        $days[$i] = $i;
    }

    $output .= '<div class="tablenav top"><div class="alignleft actions">'.$fct->input('fixtures', __('Save', 'phpleague'), array('type' => 'submit', 'class' => 'button')).'</div></div>';

    $output .=
    '<table class="widefat text-centered"><thead>
        <tr>
            <th>'.__('Fixture', 'phpleague').'</th>
            <th>'.__('Year', 'phpleague').'</th>
            <th>'.__('Month', 'phpleague').'</th>
            <th>'.__('Day', 'phpleague').'</th>
        </tr>
    </thead><tbdody>';
            
    foreach ($db->get_fixtures_league($id_league) as $row)
    {
        // Get years, months and days separately...
        list($year, $month, $day) = explode('-', $row->scheduled);
        // Used as a key...
        $number = (int) $row->number;

        // Render rows...
        $output .= '<tr>';
        $output .= '<td>'.$number.'</td>';
        $output .= '<td>'.$fct->select('year['.$number.']', $years, (int) $year).'</td>';
        $output .= '<td>'.$fct->select('month['.$number.']', $months, (int) $month).'</td>';
        $output .= '<td>'.$fct->select('day['.$number.']', $days, (int) $day).'</td>';
        $output .= '</tr>';

    }

    $output .= '</tbody></table>'.$fct->form_close();
}

$data[] = array(
    'menu'  => __('Fixtures', 'phpleague'),
    'title' => __('Fixtures of ', 'phpleague').$league_name,
    'text'  => $output,
    'class' => 'full'
);

// Render the page
echo $ctl->admin_container($menu, $data, $message);