=== PHPLeague for WordPress ===
Contributors: Maxime Dizerens
Donate link: http://www.phpleague.com/
Tags: phpleague, football, sport, league, championship, soccer, handball, volleyball, basketball, hockey
Requires at least: 3.1
Tested up to: 3.2.1
Stable tag: 1.4.0

PHPLeague for WordPress is the best companion to manage your championships.

== Description ==

PHPLeague for WordPress is the best companion to manage your championships. This plugin is very customizable and easy to use. This plugin works perfectly for other sports than football (soccer) like handball, basketball, hockey or volleyball.

**Features**

* The League's table and fixtures can be published via your posts/pages
* Manage as many leagues as you want, no limitations
* Manage as many clubs as you want, no limitations
* Manage as many players as you want, no limitations
* A very powerful and nice backend to simplify your life
* A clean (un)installer developed in order to keep your database secure and safe

**Languages**

* This plugin is currently available in English and in French. Please help us translate it!

== Installation ==

PHPLeague is really easy to install so just follow the guide:

1. Upload the `phpleague` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Create your season and competition then generate a league
4. Create your clubs and affect them to your league
5. Insert the fixtures then the matches
6. Insert your results then generate the table & calendar
7. Place `[phpleague id=ID_LEAGUE]` in your post/page
8. Manage your options and enjoy the plugin!

== Frequently Asked Questions ==

= I've just activated PHPLeague, what do I need to do now? =

First, I strongly encourage you to read the guide I wrote for you. It may help you better understand the plugin and save you some precious time although PHPLeague is user-friendly oriented.

= Why did you choose not to use WordPress default tables? =

PHPLeague will be available as a standalone version for other users and in order to be able to import/export easily the data between those different environments, it wasn't possible to use the WP tables.
And let's face it, those tables are not really optimized to handle very well that kind of plugin.

= Does the plugin handle tournament (e.g. World Cup or Friendly)? =

At the moment, it does not. And it is not part of features for the next releases except if I receive a lot of requests.

= Do I need to have Javascript activated? =

I decided not to use AJAX technology but Javascript is necessary in order to manage almost everything in the administration area (thanks to the interface...).
In the front part, no Javascript at all. Always keeping in mind that plenty of browsers handle javascript badly or not at all.

= Where do I need to add my logos? =

Few folders are automatically created during the installation. They're located in your `uploads` directory.

= Another question? =

For more information, check out the PHPLeague project website: <http://www.phpleague.com/>.

== Screenshots ==

1. Help section
2. PHPLeague Dashboard
3. Clubs Listing

== Changelog ==

= 1.4.0 =
* NEW: Get the latest PHPLeague news directly on your dashboard
* NEW: New date hint system when required
* NEW: Show how many matches a team play at home or away
* NEW: Redirect automatically users to the plugin homepage after activation
* NEW: Add the new tables to manage Player and Prediction modules
* NEW: Possibility to delete a club and all data associated
* NEW: Possibility to delete a league and all data associated
* NEW: Possibility to get the ranking table as widget
* CHG: When we remove a team from a league, all data associated are now deleted
* CHG: Don't show a link in the table when links are disabled in the settings
* CHG: Remove edition name in the database
* CHG: The fixtures interface has been rebuilt with dropdown lists
* CHG: Remove input values when it's a field by default
* CHG: FRENCH language has been updated and countries list added!
* CHG: No more Premium Edition! FREE for Life...

= 1.3 =
* NEW: Possibility to display all the fixtures
* NEW: Possibility to display all the fixtures for a specific team
* NEW: Links available to the club if enabled in the league's settings
* CHG: Removed the "create" method from the sidebar
* CHG: Add an option to hide a section by default
* CHG: jQuery code refactoring
* CHG: Don't display club information if coach and venue are missing
* CHG: Global source-code refactoring
* FIX: Don't show (0-0) if the match hasn't been played yet

= 1.2.9 =
* FIX: Correct an issue with the club creation (thanks to Nicoletta)

= 1.2.8 =
* NEW: 2 new fields in the settings (team_link/default_time)
* NEW: Added the PHPLeague's Edition in the database
* NEW: Possibility to show club information in front-end
* CHG: Possibility to set the default time
* CHG: ALTER a couple of tables
* CHG: Useless queries removed
* CHG: Few i18n strings modifications
* CHG: Multiple notifications are now possible
* CHG: UI improvements (css & js)
* FIX: Better UTF-8 characters management
* FIX: Better directories management (create/delete)

= 1.2.7 =
* NEW: Create the logos folders automatically if don't exist
* CHG: Few code fixes
* CHG: Reduce queries in the Editor
* CHG: Update database version
* CHG: Delete SQL constraints

= 1.2.6 =
* NEW: Possibility adding logos to every club
* NEW: Display a mini logo - if exists - in the table
* CHG: Teams ordered by country to be more user-friendly

= 1.2.5 =
* FIX: The formula to calculate the number of fixtures has been updated
* FIX: We cannot show a fixture if the favorite team is empty in front-end
* FIX: Redirected to the current fixture when adding a match/result
* CHG: Show a notice if the home and away team are identical
* CHG: Show a notice if a team is twice in a fixture
* CHG: Add a couple of new language strings and update the .pot file
* CHG: An irrelevant query in the results page has been removed
* CHG: The saving button has been moved in the fixtures page
* CHG: The bonus/malus input accepts now negative figure
* CHG: The bonus/malus calculation method is now inverted
* CHG: Rename the bonus/malus to be clearer
* CHG: Delete the attribute `unsigned` in the penalty field
* CHG: Database Version option has been upgraded to 1.2.1
* CHG: Overall UI lifting

= 1.2.4 =
* FIX: Drop 2 brackets causing issues in matches and results
* CHG: Remove last hardcoded strings in the editor
* CHG: Improve UI in the matches and results pages

= 1.2.3 =
* NEW: French Translation
* NEW: PHPLeague.pot is now available
* FIX: Activation and deactivation methods are now static
* CHG: Remove last hardcoded strings left even those in the menu

= 1.2.2 =
* FIX: ALTER a few fields in order to accept `NULL` value by default
* FIX: No more blank row when inserting matches in a "odd" league
* CHG: Database Version option has been upgraded to 1.2
* CHG: Activate method has been modified with the above

= 1.2.1 =
* FIX: The home/away table are now showing only the accurate data
* FIX: Possible to add numbers/dashes/points in the league name
* FIX: Possible to add numbers/dashes/points in the club name
* FIX: Little more security controls in the league's settings section
* CHG: Add security controls in the club's information edition mode
* CHG: New rule to validate the above `preg_match('/^[A-Za-z0-9_\-. ]{3,}$/', $name)`
* CHG: Move the rendering method in a dedicated library

= 1.2 =
* NEW: PHPLeague button available in the editor
* NEW: New roles available (manage_phpleague and phpleague)
* NEW: Possibility to have an odd number of teams in a league
* CHG: Improvements during the results generation

= 1.1 =
* NEW: Possibility to rename a league and change his year
* CHG: Display table once generated
* CHG: Remove setting/competition/season tables
* CHG: New fields in the league table from old tables
* CHG: Remove last hardcoded strings left
* CHG: Enhance plugin security by using nonces
* CHG: Administration interface improvements (essentially the pagination)
* CHG: Minor PHP & SQL improvements
* REMOVED: pagination_fixtures() has been removed

= 1.0 =
* Initial release!