<?php

/*
 * This file is part of the PHPLeague package.
 *
 * (c) Maxime Dizerens <mdizerens@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$root = dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))));

if (file_exists($root.'/wp-load.php'))
	require_once($root.'/wp-load.php');
else
	die();

require_once ABSPATH.'/wp-admin/admin.php';

// check for rights
if ( ! current_user_can('phpleague')) die();

global $wpdb;

$leagues  = $wpdb->get_results("SELECT `id`, `name`, `year` FROM {$wpdb->league} ORDER BY `year` DESC");
$clubs    = $wpdb->get_results("SELECT `id`, `name` FROM {$wpdb->club} ORDER BY `name` ASC");
$site_url = get_option('siteurl');
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php _e('PHPLeague for WordPress', 'phpleague') ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<script language="javascript" type="text/javascript" src="<?php echo $site_url; ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo $site_url; ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo $site_url; ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo plugins_url('phpleague/assets/js/tinymce/tinymce.js'); ?>"></script>
	<base target="_self" />
</head>
<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';document.getElementById('league_id').focus();" style="display: none">
<form name="LeagueManager" action="#">
	<div class="tabs">
		<ul>
			<li id="tables_tab" class="current"><span><a href="javascript:mcTabs.displayTab('tables_tab', 'tables_panel');" onmouseover="return false;"><?php _e('Tables', 'phpleague'); ?></a></span></li>
			<li id="fixtures_tab"><span><a href="javascript:mcTabs.displayTab('fixtures_tab', 'fixtures_panel');" onmouseover="return false;"><?php _e('Fixtures', 'phpleague'); ?></a></span></li>
			<li id="clubs_tab"><span><a href="javascript:mcTabs.displayTab('clubs_tab', 'clubs_panel');" onmouseover="return false;"><?php _e('Clubs', 'phpleague'); ?></a></span></li>
		</ul>
	</div>
	<div class="panel_wrapper">
		<!-- tables panel -->
		<div id="tables_panel" class="panel current"><br />
			<table style="border: 0;" cellpadding="5">
				<tr>
					<td><label for="league_id"><?php _e('League', 'phpleague'); ?></label></td>
					<td>
						<select id="league_id" name="league_id" style="width: 200px">
						<?php
                        if ($leagues)
                        {
                            foreach($leagues as $league)
                            {
                                $year  = intval($league->year);
                                echo '<option value="'.$league->id.'" >'.esc_html($league->name).' '.$year.'/'.substr($year + 1, 2).'</option>'."\n";
                            }
                        }
						?>
				        </select>
					</td>
				</tr>
				<tr>
					<td><label for="type"><?php _e('Style', 'phpleague') ?></label></td>
					<td>
						<select size="1" name="style" id="style">
							<option value="general"><?php _e('Normal', 'phpleague') ?></option>
							<option value="home"><?php _e('Home', 'phpleague') ?></option>
							<option value="away"><?php _e('Away', 'phpleague') ?></option>
						</select>
					</td>
				</tr>
			</table>
		</div>
		<!-- fixtures panel -->
		<div id="fixtures_panel" class="panel"><br />
			<table style="border: 0;" cellpadding="5">
				<tr>
					<td><label for="league_id"><?php _e('League', 'phpleague'); ?></label></td>
					<td>
						<select id="league_id" name="league_id" style="width: 200px;">
						<?php
	                    if ($leagues)
	                    {
	                        foreach($leagues as $league)
	                        {
	                            $year  = intval($league->year);
	                            echo '<option value="'.$league->id.'" >'.esc_html($league->name).' '.$year.'/'.substr($year + 1, 2).'</option>'."\n";
	                        }
	                    }
	                    ?>
				        </select>
					</td>
				</tr>
			</table>
		</div>
		<!-- clubs panel -->
		<div id="clubs_panel" class="panel"><br />
			<table style="border: 0;" cellpadding="5">
				<tr>
					<td><label for="club_id"><?php _e('Club', 'phpleague'); ?></label></td>
					<td>
						<select id="club_id" name="club_id" style="width: 200px;">
						<?php
                        if ($clubs)
                        {
                            foreach($clubs as $club) {
                                echo '<option value="'.$club->id.'" >'.esc_html($club->name).'</option>'."\n";
                            }
                        }
                        ?>
				        </select>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="cancel" name="cancel" value="<?php _e('Cancel', 'phpleague'); ?>" onclick="tinyMCEPopup.close();" />
		</div>
		<div style="float: right">
			<input type="submit" id="insert" name="insert" value="<?php _e('Insert', 'phpleague'); ?>" onclick="insertPHPLeague();" />
		</div>
	</div>
</form>
</body>
</html>