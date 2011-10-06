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
$data = array();
$menu = array(
	__('Export', 'phpleague') => '#',
	__('Import', 'phpleague') => '#'
);

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

echo $ctl->admin_container($menu, $data);