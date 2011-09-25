<?php
// Instances
$db  = new PHPLeague_Database();
$ctl = new PHPLeague_Admin_Controller();
$fct = new MWD_Plugin_Tools();
$message = '';

// Menu information
$menu = array(
	__('Export', 'phpleague') => '#',
	__('Import', 'phpleague') => '#'
);
$data = array();

$output = __('Coming Soon...', 'phpleague');

$data[] = array(
	'menu'  => __('Export', 'phpleague'),
	'title' => __('Export your Database', 'phpleague'),
	'text'  => $output,
	'class' => 'full'
);

$output = __('Coming Soon...', 'phpleague');

$data[] = array(
	'menu'  => __('Import', 'phpleague'),
	'title' => __('Import a Database', 'phpleague'),
	'text'  => $output,
	'class' => 'full'
);

echo $ctl->admin_container($menu, $data, $message);