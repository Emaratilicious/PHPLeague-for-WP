<?php
$ctl = new PHPLeague_Admin_Controller();

// Menu information
$menu = array(
	__('Dashboard', 'phpleague')  => '#',
	__('Clubs', 'phpleague')	  => '#',
	__('Settings', 'phpleague')	  => '#',
	__('Shortcodes', 'phpleague') => '#',
	__('Plugin', 'phpleague')	  => '#'
);

// data container
$data = array();

// dashboard
$data[] = array(
	'menu'  => __('Dashboard', 'phpleague'),
	'title' => __('Overview', 'phpleague'),
	'text'  => __('This page is the dashboard and you should be able to manage most of the actions directly from here.', 'phpleague'),
	'class' => 'full'
);

// dashboard
$data[] = array(
	'menu'  => __('Dashboard', 'phpleague'),
	'title' => __('Create a League', 'phpleague'),
	'text'  => __('That is the PHPLeague starting point. You need to chose a name and a year for your league.', 'phpleague'),
	'class' => 'full'
);

$data[] = array(
	'menu'  => __('Dashboard', 'phpleague'),
	'title' => __('Manage Teams', 'phpleague'),
	'text'  => __('The first thing you need to do once your league has been created is to assign it teams. In order to help you, the plugin will avoid duplicate teams in a league. You have also the possibility to remove one BUT only if there is no matches related with the team. Please, make sure that you are having an even number of teams in your league. A little security check will make sure that you do not remove a team currently used by a league.', 'phpleague'),
	'class' => 'full'
);

$data[] = array(
	'menu'  => __('Dashboard', 'phpleague'),
	'title' => __('Manage Fixtures', 'phpleague'),
	'text'  => __('In the fixtures section, you will be asked to fill in every fixtures date. Please, respect the following date format: "YYYY-MM-DD". Be aware that I have chosen to insert automatically the fixtures time in the database. This time can be override in the results section.	If anything is working fine, you should have the perfect number of fixtures to fill in but if anything went wrong, contact me. The option to choose the time might appear in the next release, nothing concrete about it yet.', 'phpleague'),
	'class' => 'full'
);

$data[] = array(
	'menu'  => __('Dashboard', 'phpleague'),
	'title' => __('Manage Matches', 'phpleague'),
	'text'  => __('In the matches sections, you have to relate every match between each others. Take time to fill in all the data because it is very easy to make a mistake. Fortunately, the plugin has a small counter whose able to count the numbers of matches for every team (home & away). Be aware that if you do not fill in the fixtures, you will not be able to fill in the matches.', 'phpleague'),
	'class' => 'full'
);

$data[] = array(
	'menu'  => __('Dashboard', 'phpleague'),
	'title' => __('Manage Results', 'phpleague'),
	'text'  => __('In the results section, you will be asked to fill in all the results for a fixture. If you want to add the results of a team before all the others, that is not a problem. Let every input blank and fill in the one you are interested in. Do not forget to fill in the "date time" input in order to have the calendar perfectly ordered by date. Be aware that if you do not fill in the matches, you will not be able to fill in the matches.', 'phpleague'),
	'class' => 'full'
);

$data[] = array(
	'menu'  => __('Dashboard', 'phpleague'),
	'title' => __('Manage Settings', 'phpleague'),
	'text'  => __('In this section, you have the possibility to setup every option used by the plugin like the number of points for a victory, a draw or a defeat. If might happen that a club received few penalty points. No problem, the plugin handle this perfectly. At the moment, all data must be equal or superior to zero but this might change in order to give "bonus" points in the next release.', 'phpleague'),
	'class' => 'full'
);

$data[] = array(
	'menu'  => __('Dashboard', 'phpleague'),
	'title' => __('The Generator', 'phpleague'),
	'text'  => __('This is the final step in order to update your table. When you choose to generate your table, every result will be calculated according to your settings and the database table will be filled in with the latest data. A sample will be displayed once the task is done.', 'phpleague'),
	'class' => 'full'
);

// clubs
$data[] = array(
	'menu'  => __('Clubs', 'phpleague'),
	'title' => __('Overview', 'phpleague'),
	'text'  => __('This section of the plugin is entirely dedicated to the clubs management. You can add, edit or even delete a club once created.', 'phpleague'),
	'class' => 'full'
);

$data[] = array(
	'menu'  => __('Clubs', 'phpleague'),
	'title' => __('Add a Club', 'phpleague'),
	'text'  => __('Create a club is really simple. You just need to choose a name and assign him a country. Unfortunately the plugin cannot handle having two identical names even if the country is different.', 'phpleague'),
	'class' => 'full'
);

$data[] = array(
	'menu'  => __('Clubs', 'phpleague'),
	'title' => __('Edit a Club', 'phpleague'),
	'text'  => __('Once your club is created, you can edit him by clicking on his name. You will be redirected to the club edition mode. Currently, you have only six different options in the edition mode (more are available in the Premium plugin).', 'phpleague'),
	'class' => 'full'
);

$data[] = array(
	'menu'  => __('Clubs', 'phpleague'),
	'title' => __('Remove a Club', 'phpleague'),
	'text'  => __('In order to remove a club, you need to go in the edition mode. A little security check will be executed during the process and you will be protected against mistakes if the club is currently used in a league.', 'phpleague'),
	'class' => 'full'
);

$data[] = array(
	'menu'  => __('Clubs', 'phpleague'),
	'title' => __('The Countries', 'phpleague'),
	'text'  => __('By default, the countries are in English but you can update them from your MySQL Administration software.', 'phpleague'),
	'class' => 'full'
);

// settings
$data[] = array(
	'menu'  => __('Settings', 'phpleague'),
	'title' => __('Overview', 'phpleague'),
	'text'  => __('This section of the plugin is entirely dedicated to the settings management. You can for instance import or export your league from here.', 'phpleague'),
	'class' => 'full'
);

$data[] = array(
	'menu'  => __('Settings', 'phpleague'),
	'title' => __('Export', 'phpleague'),
	'text'  => __('Not functional yet.', 'phpleague'),
	'class' => 'full'
);

$data[] = array(
	'menu'  => __('Settings', 'phpleague'),
	'title' => __('Import', 'phpleague'),
	'text'  => __('Not functional yet.', 'phpleague'),
	'class' => 'full'
);

$table = '
<table class="widefat">
<thead>
	<tr>
		<th scope="col">'.__('Parameter', 'phpleague').'</th>
		<th scope="col">'.__('Description', 'phpleague').'</th>
		<th scope="col">'.__('Type', 'phpleague').'</th>
		<th scope="col">'.__('Default', 'phpleague').'</th>
		<th scope="col">'.__('Optional', 'phpleague').'</th>
	</tr>
</thead>
<tbody>
	<tr class="" valign="top">
		<td>id</td>
		<td>'.__('League ID', 'phpleague').'</td>
		<td><em>integer</em></td>
		<td>&#160;</td>
		<td>'.__('No', 'phpleague').'</td>
	</tr>
	<tr class="alternate" valign="top">
		<td>type</td>
		<td>'.__('What kind of information to display?', 'phpleague').'</td>
		<td><em>fixtures/table</em></td>
		<td>table</td>
		<td>'.__('Yes', 'phpleague').'</td>
	</tr>
	<tr class="" valign="top">
		<td>style</td>
		<td>'.__('Do we want a specific ranking table?', 'phpleague').'</td>
		<td><em>general/home/away</em></td>
		<td>general</td>
		<td>'.__('Yes', 'phpleague').'</td>
	</tr>
	<tr class="alternate" valign="top">
		<td>id_team</td>
		<td>'.__('For which team do we want to display the fixtures?', 'phpleague').'</td>
		<td><em>integer</em></td>
		<td>your favorite team</td>
		<td>'.__('Yes', 'phpleague').'</td>
	</tr>
</tbody>
</table>';

$table .= __('All those short codes need to be activated with the following string: [phpleague]. The "id_team" parameter is only used by the fixtures so as the "style" option for the table.', 'phpleague');

// shortcodes
$data[] = array(
	'menu'  => __('Shortcodes', 'phpleague'),
	'title' => __('Shortcodes Listing', 'phpleague'),
	'text'  => $table,
	'class' => 'full'
);

// plugin info
$data[] = array(
	'menu'  => __('Plugin', 'phpleague'),
	'title' => __('Overview', 'phpleague'),
	'text'  => __('You are currently using the <b>Core</b> edition of the PHPLeague for WordPress Plugin. This edition is mostly a demonstration of what PHPLeague can do. If you need more features, you should upgrade to the <b>Premium</b> Edition.', 'phpleague'),
	'class' => 'full'
);

$data[] = array(
	'menu'  => __('Plugin', 'phpleague'),
	'title' => __('Requirements', 'phpleague'),
	'text'  => __('I did not test the plugin under all Operating Systems but I am pretty sure he must handle every environment. As WordPress 3.2+, the minimum version of PHP required is 5.2.4 or greater and MySQL version 5 or greater for your database. PHPLeague is fully using the power of the SQL Constraints so you have to use InnoDB as storage engine.', 'phpleague'),
	'class' => 'full'
);

$data[] = array(
	'menu'  => __('Plugin', 'phpleague'),
	'title' => __('Thanks', 'phpleague'),
	'text'  => __('I would like to thanks everybody who contribute - indirectly - to develop this Plugin. The names I have in mind are: Alexis Mangin, Woo Themes Developers, jQuery Developers and WordPress Developers.', 'phpleague'),
	'class' => 'full'
);

echo $ctl->admin_container($menu, $data);