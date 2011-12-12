<?php

/*
 * This file is part of the PHPLeague package.
 *
 * (c) Maxime Dizerens <mdizerens@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$id_player  = ( ! empty($_GET['id_player']) ? (int) $_GET['id_player'] : NULL);

if ($db->is_player_unique($id_player) === FALSE)
    return require_once WP_PHPLEAGUE_PATH.'inc/admin/player_edit.php';    

// Variables
$per_page   = 7;
$page       = ( ! empty($_GET['p_nb']) ? (int) $_GET['p_nb'] : 1);
$offset     = ($page - 1 ) * $per_page;
$total      = $db->count_players();
$base_url   = 'admin.php?page=phpleague_player';
$pagination = $fct->pagination($total, $per_page, $page);
$menu       = array(__('Overview', 'phpleague') => '#');
$data       = array();
$message    = array();

// Data processing...
if (isset($_POST['player']) && check_admin_referer('phpleague'))
{
    // Secure data
    $firstname = (string) trim($_POST['firstname']);
    $lastname  = (string) trim($_POST['lastname']);

    if ($fct->valid_text($firstname, 3) === FALSE)
    {
        $message[] = __('The first name must be alphanumeric and 3 characters long at least.', 'phpleague');
    }
    elseif ($fct->valid_text($lastname, 3) === FALSE)
    {
        $message[] = __('The last name must be alphanumeric and 3 characters long at least.', 'phpleague');
    }
    else
    {
        $message[] = __('Player added successfully.', 'phpleague');
        $db->add_player($firstname, $lastname);
    }
}
elseif (isset($_POST['delete_player']) && check_admin_referer('phpleague'))
{
    // Secure data
    $id_player = ( ! empty($_POST['id_player'])) ? $_POST['id_player'] : 0;

    if ($id_player === 0)
    {
        $message[] = __('We are sorry but it seems that you did not select a player.', 'phpleague');
    }
    else
    {
        if (is_array($id_player))
        {
            $i = 0;
            
            // Delete player one by one
            foreach ($id_player as $value)
            {
                $db->delete_player($value);
                $i++;
            }
            
            if ($i === 1)
                $message[] = __('Player deleted successfully.', 'phpleague');
            else
                $message[] = __('Players deleted successfully.', 'phpleague');
        }
    }
}

if ($total == 0)
    $message[] = __('We did not find any player in the database.', 'phpleague');
    
$output  = $fct->form_open(admin_url($base_url));
$output .= $fct->input('firstname', __('Firstname', 'phpleague'), array('readonly' => 'readonly', 'class' => 'default'));
$output .= $fct->input('lastname', __('Lastname', 'phpleague'), array('readonly' => 'readonly', 'class' => 'default'));
$output .= $fct->input('player', __('Create', 'phpleague'), array('type' => 'submit', 'class' => 'button'));
$output .= $fct->form_close();

$data[] = array(
    'menu'  => __('Overview', 'phpleague'),
    'title' => __('New Player', 'phpleague'),
    'text'  => $output,
    'class' => 'full'
);

$output  = $fct->form_open(admin_url($base_url));
$output .= '<div class="tablenav top"><div class="alignleft actions">'.$fct->input('delete_player',
        __('Delete', 'phpleague'), array('type' => 'submit', 'class' => 'button')).'</div>';

if ($pagination)
    $output .= '<div class="tablenav-pages">'.$pagination.'</div>';

$output .= '</div><table class="widefat"><thead><tr>
        <th class="check-column"><input type="checkbox"/></th>
        <th>'.__('ID', 'phpleague').'</th>
        <th>'.__('Name', 'phpleague').'</th>
        <th>'.__('Birthdate', 'phpleague').'</th>
        <th>'.__('Height', 'phpleague').'</th>
        <th>'.__('Weight', 'phpleague').'</th></tr></thead><tbody>';
    
foreach ($db->get_every_player($offset, $per_page, 'ASC', TRUE) as $player)
{
    $output .= '<tr '.$fct->alternate('', 'class="alternate"').'>
            <th class="check-column"><input type="checkbox" name="id_player[]" value="'.intval($player->id).'" /></th>
            <td>'.intval($player->id).'</td>
            <td><a href="'.admin_url($base_url.'&id_player='.intval($player->id)).'">'
            .esc_html($player->lastname).' '.esc_html($player->firstname).'</a></td>
            <td>'.esc_html($player->birthdate).'</td>
            <td>'.intval($player->height).'</td>
            <td>'.intval($player->weight).'</td></tr>';
}

$output .= '</tbody></table>'.$fct->form_close();
$data[]  = array(
    'menu'  => __('Overview', 'phpleague'),
    'title' => __('Player Listing', 'phpleague'),
    'text'  => $output,
    'class' => 'full'
);

// Render the page
echo $ctl->admin_container($menu, $data, $message);