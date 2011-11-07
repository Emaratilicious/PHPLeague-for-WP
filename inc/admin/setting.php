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
$base_url   = 'admin.php?page=phpleague_setting';
$menu       = array(__('Overview', 'phpleague') => '#');
$data       = array();
$message    = array();

// Do we have to handle some data?
if (isset($_POST['club']) && check_admin_referer('phpleague')) {

}

$output  = $fct->form_open(admin_url($base_url));
$output .= $fct->form_close();

$data[] = array(
    'menu'  => __('Overview', 'phpleague'),
    'title' => __('PHPLeague Settings', 'phpleague'),
    'text'  => $output,
    'class' => 'full'
);

// Show everything...
echo $ctl->admin_container($menu, $data, $message);