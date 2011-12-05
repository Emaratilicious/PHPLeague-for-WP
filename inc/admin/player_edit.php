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
$id_player = ( ! empty($_GET['id_player']) ? (int) $_GET['id_player'] : 0);
$page_url  = admin_url('admin.php?page=phpleague_player&id_player='.$id_player);
$message   = array();
$data      = array();
$menu      = array(__('Player Information', 'phpleague') => '#', __('Player Record', 'phpleague') => '#');

// Security check
if ($db->is_player_unique($id_player) === TRUE)
    wp_die(__('We did not find the player in the database.', 'phpleague'));

// We edit the player basic information
if (isset($_POST['edit_player']) && check_admin_referer('phpleague'))
{
    // Secure data
    $firstname = (string) trim($_POST['firstname']);
    $lastname  = (string) trim($_POST['lastname']);
    $birthdate = (string) trim($_POST['birthdate']);
    $picture   = (string) trim($_POST['picture']);
    $desc      = (string) trim($_POST['description']);
    $weight    = (int) $_POST['weight'];
    $height    = (int) $_POST['height'];
    $country   = (int) $_POST['country'];
    $term      = (int) $_POST['term'];

    // We need to pass those tests to insert the data
    if ($id_player === 0)
    {
       $message[] = __('Busted! ID is not correct!', 'phpleague');
    }
    elseif ($fct->valid_text($firstname, 3) === FALSE)
    {
       $message[] = __('The first name must be alphanumeric and 3 characters long at least.', 'phpleague');
    }
    elseif ($fct->valid_text($lastname, 3) === FALSE)
    {
       $message[] = __('The last name must be alphanumeric and 3 characters long at least.', 'phpleague');
    }
    elseif ( ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthdate))
    {
       $message[] = __('The birthdate must follow the pattern: "YYYY-MM-DD".', 'phpleague');
    }
    elseif ($weight == 0 || $weight > 255)
    {
       $message[] = __('The weight must be bigger than 0 and lower than 255.', 'phpleague');
    }
    elseif ($height == 0 || $height > 255)
    {
       $message[] = __('The height must be bigger than 0 and lower than 255.', 'phpleague');
    }
    else
    {
        $message[] = __('Player edited with success!', 'phpleague');
        $db->update_player($id_player, $firstname, $lastname, $birthdate, $height, $weight, $desc, $picture, $country, $term);
    }
}
elseif (isset($_POST['player_history']) && check_admin_referer('phpleague')) // We update the player history
{
    // Secure data
    $data = ( ! empty($_POST['history'])) ? $_POST['history'] : NULL;

    if (is_array($data))
    {
        foreach ($data as $key => $item)
        {
            $db->update_player_history($id_player, $key, $item['number'], $item['id_position']);
        }
    }
    $message[] = __('Profile updated successfully.', 'phpleague');
}
elseif (isset($_POST['add_team']) && check_admin_referer('phpleague')) // We add one team in the player history
{
    // Secure data
    $id_team = ( ! empty($_POST['id_team'])) ? (int) $_POST['id_team'] : 0;

    if ($id_team === 0)
    {
        $message[] = __('No team has been selected!', 'phpleague');
    }
    elseif ($db->player_already_in_team($id_player, $id_team) === TRUE)
    {
        $message[] = __('A player cannot be twice in the same team.', 'phpleague');
    }
    else
    {
        $db->update_player_history($id_player, $id_team, 0, 0, 'insert');
        $message[] = __('Team added successfully to the profile.', 'phpleague');
    }
}

// Get countries list
foreach ($db->get_every_country(0, 250, 'ASC') as $array)
{
    $countries_list[$array->id] = esc_html($array->name);
}

// Get terms list
$tags_list[0] = __('-- Select a term --', 'phpleague');
foreach (get_tags(array('hide_empty' => FALSE)) as $tag)
{
    $tags_list[$tag->term_id] = esc_html($tag->name);
}

// -- Player information
$pics_list = $fct->return_dir_files(WP_PHPLEAGUE_UPLOADS_PATH.'players/', array('png', 'jpg'));
$player    = $db->get_player($id_player);
$output    = $fct->form_open($page_url);
$table     =
    '<table class="form-table">
        <tr>
            <td class="required">'.__('First Name:', 'phpleague').'</td>
            <td>'.$fct->input('firstname', esc_html($player->firstname)).'</td>
            <td class="required">'.__('Last Name:', 'phpleague').'</td>
            <td>'.$fct->input('lastname', esc_html($player->lastname)).'</td>
        </tr>
        <tr>
            <td class="required">'.__('Height:', 'phpleague').'</td> 
            <td>'.$fct->input('height', (int) $player->height).'</td>
            <td class="required">'.__('Weight:', 'phpleague').'</td>
            <td>'.$fct->input('weight', (int) $player->weight).'</td>
        </tr>
        <tr>
            <td class="required">'.__('Birthdate:', 'phpleague').'</td>
            <td>'.$fct->input('birthdate', esc_html($player->birthdate)).'</td>
            <td class="required">'.__('Country:', 'phpleague').'</td>
            <td>'.$fct->select('country', $countries_list, (int) $player->id_country).'</td>
        </tr>
        <tr>
            <td>'.__('Picture:', 'phpleague').'</td>
            <td>'.$fct->select('picture', $pics_list, esc_html($player->picture)).'</td>
            <td>'.__('Term:', 'phpleague').'</td>
            <td>'.$fct->select('term', $tags_list, (int) $player->id_term).'</td>
        </tr>
    </table>';
    
$output .= $table;

$data[] = array(
    'menu'  => __('Player Information', 'phpleague'),
    'title' => __('Player Information', 'phpleague'),
    'text'  => $output,
    'class' => 'full'
);

// Attach the editor to the textarea
// Not working with WP 3.3+
if (function_exists('wp_tiny_mce') && ! function_exists('wp_editor'))
{
    add_filter('teeny_mce_before_init', create_function('$a', '
        $a["theme"] = "advanced";
        $a["skin"] = "wp_theme";
        $a["height"] = "300";
        $a["width"] = "100%";
        $a["onpageload"] = "";
        $a["mode"] = "exact";
        $a["elements"] = "description";
        $a["editor_selector"] = "mceEditor";
        $a["plugins"] = "safari,inlinepopups,spellchecker";
        $a["forced_root_block"] = FALSE;
        $a["force_br_newlines"] = FALSE;
        $a["force_p_newlines"] = TRUE;
        $a["convert_newlines_to_brs"] = TRUE;
        return $a;')
    );

    wp_tiny_mce(TRUE);
}

$output  = $fct->textarea('description', esc_html($player->description), array('id' => 'description'));
$output .= '<div class="submit">'.$fct->input('id_player', $id_player, array('type' => 'hidden')).
        $fct->input('edit_player', __('Save', 'phpleague'), array('type' => 'submit')).'</div>';
$output .= $fct->form_close();

$data[] = array(
    'menu'  => __('Player Information', 'phpleague'),
    'title' => __('Player Biography', 'phpleague'),
    'text'  => $output,
    'class' => 'full'
);

// -- Add a new Team
$teams_list[0] = __('-- Select a Team --', 'phpleague');
foreach ($db->get_teams_from_leagues() as $team)
{
    $league = $team->league_name.' '.$team->league_year.'/'.($team->league_year + 1);
    $teams_list[$league][$team->team_id] = esc_html($team->club_name);
}

$output  = $fct->form_open($page_url);
$output .= $fct->select('id_team', $teams_list);
$output .= __(' Select one team from a league.', 'phpleague');
$output .= $fct->input('add_team', __('Add', 'phpleague'), array('type' => 'submit', 'class' => 'button'));
$output .= $fct->form_close();

$data[] = array(
    'menu'  => __('Player Record', 'phpleague'),
    'title' => __('Add a Team', 'phpleague'),
    'text'  => $output,
    'class' => 'full'
);

// -- Player history
$history = $db->get_player_history($id_player);
$output  = $fct->form_open($page_url);
$output .=
'<table class="widefat">
    <thead>
        <tr>
            <th>'.__('League', 'phpleague').'</th>
            <th>'.__('Team', 'phpleague').'</th>
            <th>'.__('Number', 'phpleague').'</th>
            <th colspan="2">'.__('Position', 'phpleague').'</th>
        </tr>
    </thead>
    <tbody>';
    
    // Display all the information...
    $positions_list[0] = __('-- Select a position --', 'phpleague');

    // Only display if we get an history...
    foreach ($history as $row)
    {
        // TODO - This is only a test..
        // Get positions list...
        foreach (PHPLeague_Sports_Football::$positions as $key => $value)
        {
            $positions_list[$key] = $value; 
        }

        $output .= '<tr id="'.$row->id_player_team.'"><td>'.esc_html($row->league).' '.$row->year.'/'.($row->year + 1).'</td>';
        $output .= '<td>'.esc_html($row->club).'</td>';
        $output .= '<td>'.$fct->input('history['.$row->id_team.'][number]', (int) $row->number, array('size' => 4)).'</td>';
        $output .= '<td>'.$fct->select('history['.$row->id_team.'][id_position]', $positions_list, (int) $row->position).'</td>';
        $output .= '<td>'.$fct->input('delete_player_team', __('Delete', 'phpleague'), array( 'type'  => 'button', 'class' => 'button delete_player_team')).'</td></tr>';
    }

$output .= '</tbody></table><div class="submit">';
$output .= $fct->input('player_history', __('Save Table', 'phpleague'), array('type' => 'submit')).'</div>'.$fct->form_close();
$data[]  = array(
    'menu'  => __('Player Record', 'phpleague'),
    'title' => __('Player History', 'phpleague'),
    'text'  => $output,
    'class' => 'full'
);

// Render the page
echo $ctl->admin_container($menu, $data, $message);