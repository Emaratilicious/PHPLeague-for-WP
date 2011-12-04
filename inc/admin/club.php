<?php

/*
 * This file is part of the PHPLeague package.
 *
 * (c) Maxime Dizerens <mdizerens@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Get ID club
$id_club = ( ! empty($_GET['id_club'])) ? (int) $_GET['id_club'] : NULL;

// Club edition mode...
if ($db->is_club_unique($id_club, 'id') === FALSE)
    return require_once WP_PHPLEAGUE_PATH.'inc/admin/club_edit.php';    

// Variables
$per_page   = 7;
$page       = ( ! empty($_GET['p_nb']) ? (int) $_GET['p_nb'] : 1);
$offset     = ($page - 1 ) * $per_page;
$total      = $db->count_clubs();
$base_url   = 'admin.php?page=phpleague_club';
$pagination = $fct->pagination($total, $per_page, $page);
$menu       = array(__('Overview', 'phpleague') => '#');
$data       = array();
$message    = array();

// Get countries list
foreach ($db->get_every_country(0, 250, 'ASC') as $array)
{
    $countries_list[$array->id] = esc_html($array->name);
}

// We add a new club...
if (isset($_POST['club']) && check_admin_referer('phpleague'))
{
    // Secure data
    $name    = (string) trim($_POST['club_name']);
    $country = (int) $_POST['club_country'];

    if (in_array($name, array(NULL, FALSE, '')))
    {
        $message[] = __('The name cannot be empty.', 'phpleague');
    }
    elseif ($fct->valid_text($name, 3) === FALSE)
    {
        $message[] = __('The name must be alphanumeric and 3 characters long at least.', 'phpleague');
    }
    elseif ($db->is_club_unique($name, 'name', $country) === FALSE)
    {
        $message[] = __('The club '.$name.' is already in your database.', 'phpleague');
    }
    else
    {
        $message[] = __('Club added successfully.', 'phpleague');
        $db->add_club($name, $country);
    }
}
elseif (isset($_POST['delete_club']) && check_admin_referer('phpleague')) // We delete one or few clubs
{
    // Secure data
    $id_club = ( ! empty($_POST['id_club'])) ? $_POST['id_club'] : 0;
    
    if ($id_club === 0)
    {
        $message[] = __('We are sorry but it seems that you did not select a club.', 'phpleague');
    }
    else
    {
        if (is_array($id_club))
        {
            $i = 0;
            foreach ($id_club as $value)
            {
                $db->delete_club($value);
                $i++;
            }
            
            if ($i === 1)
                $message[] = __('Club deleted successfully with all data associated.', 'phpleague');
            else
                $message[] = __('Clubs deleted successfully with all data associated.', 'phpleague');
        }
    }
}

if ($total == 0)
    $message[] = __('We did not find any club in the database.', 'phpleague');
    
$output  = $fct->form_open(admin_url($base_url));
$output .= $fct->input('club_name', '', array('size' => 15)).$fct->select('club_country', $countries_list).
        ' '.$fct->input('club', __('Create', 'phpleague'), array('type' => 'submit', 'class' => 'button'));
$output .= $fct->form_close();

$data[] = array(
    'menu'  => __('Overview', 'phpleague'),
    'title' => __('New Club', 'phpleague'),
    'text'  => $output,
    'hide'  => TRUE,
    'class' => 'full'
);

$output  = $fct->form_open(admin_url($base_url));
$output .= '<div class="tablenav top"><div class="alignleft actions">'.$fct->input('delete_club', __('Delete', 'phpleague'),
        array('type' => 'submit', 'class' => 'button')).'</div>';

if ($pagination)
    $output .= '<div class="tablenav-pages">'.$pagination.'</div>';

$output .= '</div><table class="widefat"><thead><tr>
            <th class="check-column"><input type="checkbox"/></th>
            <th>'.__('ID', 'phpleague').'</th>
            <th>'.__('Name', 'phpleague').'</th>
            <th>'.__('Country', 'phpleague').'</th>
            <th>'.__('Coach', 'phpleague').'</th>
            <th>'.__('Venue', 'phpleague').'</th>
        </tr></thead><tbody>';
    
    foreach ($db->get_every_club($offset, $per_page, 'ASC', TRUE) as $club)
    {
        $output .= '
        <tr '.$fct->alternate('', 'class="alternate"').'>
            <th class="check-column"><input type="checkbox" name="id_club[]" value="'.intval($club->id).'" /></th>
            <td>'.intval($club->id).'</td>
            <td><a href="'.admin_url($base_url.'&id_club='.intval($club->id)).'">'.esc_html($club->name).'</a></td>
            <td>'.esc_html($club->country).'</td>
            <td>'.esc_html($club->coach).'</td>
            <td>'.esc_html($club->venue).'</td>
        </tr>';
    }

$output .= '</tbody></table>'.$fct->form_close();
$data[] = array(
    'menu'  => __('Overview', 'phpleague'),
    'title' => __('Clubs Listing', 'phpleague'),
    'text'  => $output,
    'class' => 'full'
);

// Render the page
echo $ctl->admin_container($menu, $data, $message);