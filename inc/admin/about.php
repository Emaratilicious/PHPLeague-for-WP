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
    __('Dashboard', 'phpleague')  => '#',
    __('Clubs', 'phpleague')      => '#',
    __('Players', 'phpleague')    => '#',
    __('Shortcodes', 'phpleague') => '#',
    __('Widgets', 'phpleague')    => '#',
    __('About', 'phpleague')      => '#'
);

// Dashboard
$data[] = array(
    'menu'  => __('Dashboard', 'phpleague'),
    'title' => __('Overview', 'phpleague'),
    'text'  => __('This page is your dashboard and the main point of contact to manage most of the actions.', 'phpleague'),
    'class' => 'full'
);

$data[] = array(
    'menu'  => __('Dashboard', 'phpleague'),
    'title' => __('Create a League', 'phpleague'),
    'text'  => __('That is your first step ever. You need to chose a name and a year for your league. Duplicates are impossible :)', 'phpleague'),
    'class' => 'full'
);

$data[] = array(
    'menu'  => __('Dashboard', 'phpleague'),
    'title' => __('Manage Teams', 'phpleague'),
    'text'  => __('The first thing you need to do once your league has been created is to assign it teams. You can have an odd or even number of teams, PHPLeague is handling every case smoothly, but still two teams minimum are required. A little counter is showing you how many teams are in the current league.You can remove more than once team at the same time too.', 'phpleague'),
    'class' => 'full'
);

$data[] = array(
    'menu'  => __('Dashboard', 'phpleague'),
    'title' => __('Manage Fixtures', 'phpleague'),
    'text'  => __('In the fixtures section, you will be asked to fill in every fixtures date. Be aware that the fixtures time is automatically added in the database. This time can be override in the settings section but then you have to come back here and save it one more time to update all fixtures date time.', 'phpleague'),
    'class' => 'full'
);

$data[] = array(
    'menu'  => __('Dashboard', 'phpleague'),
    'title' => __('Manage Matches', 'phpleague'),
    'text'  => __('In the matches sections, you have to relate every match between each others. Take time to fill in all the data because it is very easy to make a mistake. You can check if the data are OK in your teams section. Be aware that if you do not fill in the fixtures, you will not be able to fill in the matches.', 'phpleague'),
    'class' => 'full'
);

$data[] = array(
    'menu'  => __('Dashboard', 'phpleague'),
    'title' => __('Manage Results', 'phpleague'),
    'text'  => __('In the results section, you will be asked to fill in all the results for a fixture. If you want to add the results of a team before all the others, that is not a problem. Let every input blank and fill in the one you are interested in.', 'phpleague'),
    'class' => 'full'
);

$data[] = array(
    'menu'  => __('Dashboard', 'phpleague'),
    'title' => __('Manage Settings', 'phpleague'),
    'text'  => __('In this section, you have the possibility to setup every option available with the plugin like the number of points for a victory, a draw or a defeat. It might happen that a club received a penalty point or more than one. No problem, the plugin handle this perfectly. You can also use this to add "bonus" point.', 'phpleague'),
    'class' => 'full'
);

$data[] = array(
    'menu'  => __('Dashboard', 'phpleague'),
    'title' => __('The Generator', 'phpleague'),
    'text'  => __('This is the final step in order to update your table. When you choose to generate your table, every result will be calculated according to your settings and the database table will be filled in with the latest data.', 'phpleague'),
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
    'text'  => __('Once your club is created, you can edit it by clicking on his name. You will be redirected to the club edition mode.', 'phpleague'),
    'class' => 'full'
);

$data[] = array(
    'menu'  => __('Clubs', 'phpleague'),
    'title' => __('Remove a Club', 'phpleague'),
    'text'  => __('Keep in mind that once you delete a club, all data associated to will be destroyed. You can delete more than once club at the time.', 'phpleague'),
    'class' => 'full'
);

$data[] = array(
    'menu'  => __('Clubs', 'phpleague'),
    'title' => __('The Countries', 'phpleague'),
    'text'  => __('By default, the countries are in English but you can update them from your MySQL Administration software. The French version is available in the i18n folder of your plugin.', 'phpleague'),
    'class' => 'full'
);

// players
$data[] = array(
    'menu'  => __('Players', 'phpleague'),
    'title' => __('Overview', 'phpleague'),
    'text'  => __('This section of the plugin is entirely dedicated to the players management. You can add, edit or even delete a player once created.', 'phpleague'),
    'class' => 'full'
);

// players
$data[] = array(
    'menu'  => __('Players', 'phpleague'),
    'title' => __('Edit a Player', 'phpleague'),
    'text'  => __('...', 'phpleague'),
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

$table .= __('All those short codes need to be activated with the following string: [phpleague].<br />The "id_team" parameter is only used by the fixtures so as the "style" option for the table.', 'phpleague');

// Shortcodes
$data[] = array(
    'menu'  => __('Shortcodes', 'phpleague'),
    'title' => __('Shortcodes Listing', 'phpleague'),
    'text'  => $table,
    'class' => 'full'
);

// Widgets
$data[] = array(
    'menu'  => __('Widgets', 'phpleague'),
    'title' => __('Overview', 'phpleague'),
    'text'  => '',
    'class' => 'full'
);

$data[] = array(
    'menu'  => __('Widgets', 'phpleague'),
    'title' => __('Widgets Listing', 'phpleague'),
    'text'  => '',
    'class' => 'full'
);

// Plugin Information
$data[] = array(
    'menu'  => __('About', 'phpleague'),
    'title' => __('Overview', 'phpleague'),
    'text'  => __('You are currently using the <b>ULTIMATE</b> edition of the PHPLeague for WordPress Plugin.', 'phpleague'),
    'class' => 'full'
);

$data[] = array(
    'menu'  => __('About', 'phpleague'),
    'title' => __('Requirements', 'phpleague'),
    'text'  => __('I did not test the plugin under all Operating Systems but I am pretty sure it must handle every environment. As WordPress 3.1+, the minimum version of PHP required is 5.2.4 or greater and MySQL version 5 or greater for your database.', 'phpleague'),
    'class' => 'full'
);

$data[] = array(
    'menu'  => __('About', 'phpleague'),
    'title' => __('Thanks', 'phpleague'),
    'text'  => __('I would like to thanks everybody who contribute - indirectly - to develop this Plugin. The names I have in mind are: Alexis Mangin, jQuery Developers and WordPress Developers.', 'phpleague'),
    'class' => 'full'
);

// Show everything...
echo $ctl->admin_container($menu, $data);