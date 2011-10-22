=== PHPLeague for WordPress ===
Contributors: Maxime Dizerens
Donate link: http://www.mika-web.com/
Tags: phpleague, football, sport, league, championship, soccer, handball, volleyball, basketball, hockey
Requires at least: 3.1
Tested up to: 3.2.1
Stable tag: 1.3.1

PHPLeague for WordPress is the best companion to manage your championships.

== Description ==

PHPLeague for WordPress is the best companion to manage your championships. This plugin is very customizable and easy to use. This plugin works perfectly for other sports than football (soccer) like handball, basketball, hockey or volleyball.

**Features**

* The League's table and fixtures can be published via your posts/pages
* Manage as many leagues as you want, no limitations
* Manage as many clubs as you want, no limitations
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
5. Choose the fixtures' dates then the matches (everything's generated automatically)
6. Insert your results then generate the table & calendar
7. Place `[phpleague id="YOUR-ID" type="general"]` in your post/page

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

== Screenshots ==

1. Help section
2. PHPLeague Dashboard
3. Clubs Listing

== Changelog ==

= 1.3.1 =
* CHANGED: Delete edition name in the database
* CHANGED: No more Premium Edition!
* CHANGED: FRENCH language has been updated!

= 1.3 =
* NEW: Possibility to display all the fixtures
* NEW: Possibility to display all the fixtures for a specific team
* NEW: Links available to the club if enabled in the league's settings
* CHANGED: Removed the "create" method from the sidebar
* CHANGED: Add an option to hide a section by default
* CHANGED: jQuery code refactoring
* CHANGED: Don't display club information if coach and venue are missing
* CHANGED: Global source-code refactoring
* FIX: Don't show (0-0) if the match hasn't been played yet

= 1.2.9 =
* FIX: Correct an issue with the club creation (thanks to Nicoletta)

= 1.2.8 =
* NEW: 2 new fields in the settings (team_link/default_time)
* NEW: Added the PHPLeague's Edition in the database
* NEW: Possibility to show club information in front-end
* CHANGED: Possibility to set the default time
* CHANGED: ALTER a couple of tables
* CHANGED: Useless queries removed
* CHANGED: Few i18n strings modifications
* CHANGED: Multiple notifications are now possible
* CHANGED: UI improvements (css & js)
* FIX: Better UTF-8 characters management
* FIX: Better directories management (create/delete)

= 1.2.7 =
* NEW: Create the logos folders automatically if don't exist
* CHANGED: Few code fixes
* CHANGED: Reduce queries in the Editor
* CHANGED: Update database version
* CHANGED: Delete SQL constraints

= 1.2.6 =
* NEW: Possibility adding logos to every club
* NEW: Display a mini logo - if exists - in the table
* CHANGED: Teams ordered by country to be more user-friendly

= 1.2.5 =
* FIX: The formula to calculate the number of fixtures has been updated
* FIX: We cannot show a fixture if the favorite team is empty in front-end
* FIX: Redirected to the current fixture when adding a match/result
* CHANGED: Show a notice if the home and away team are identical
* CHANGED: Show a notice if a team is twice in a fixture
* CHANGED: Add a couple of new language strings and update the .pot file
* CHANGED: An irrelevant query in the results page has been removed
* CHANGED: The saving button has been moved in the fixtures page
* CHANGED: The bonus/malus input accepts now negative figure
* CHANGED: The bonus/malus calculation method is now inverted
* CHANGED: Rename the bonus/malus to be clearer
* CHANGED: Delete the attribute `unsigned` in the penalty field
* CHANGED: Database Version option has been upgraded to 1.2.1
* CHANGED: Overall UI lifting

= 1.2.4 =
* FIX: Drop 2 brackets causing issues in matches and results
* CHANGED: Remove last hardcoded strings in the editor
* CHANGED: Improve UI in the matches and results pages

= 1.2.3 =
* NEW: French Translation
* NEW: PHPLeague.pot is now available
* FIX: Activation and deactivation methods are now static
* CHANGED: Remove last hardcoded strings left even those in the menu

= 1.2.2 =
* FIX: ALTER a few fields in order to accept `NULL` value by default
* FIX: No more blank row when inserting matches in a "odd" league
* CHANGED: Database Version option has been upgraded to 1.2
* CHANGED: Activate method has been modified with the above

= 1.2.1 =
* FIX: The home/away table are now showing only the accurate data
* FIX: Possible to add numbers/dashes/points in the league name
* FIX: Possible to add numbers/dashes/points in the club name
* FIX: Little more security controls in the league's settings section
* CHANGED: Add security controls in the club's information edition mode
* CHANGED: New rule to validate the above `preg_match('/^[A-Za-z0-9_\-. ]{3,}$/', $name)`
* CHANGED: Move the rendering method in a dedicated library

= 1.2 =
* NEW: PHPLeague button available in the editor
* NEW: New roles available (manage_phpleague and phpleague)
* NEW: Possibility to have an odd number of teams in a league
* CHANGED: Improvements during the results generation

= 1.1 =
* NEW: Possibility to rename a league and change his year
* CHANGED: Display table once generated
* CHANGED: Remove setting/competition/season tables
* CHANGED: New fields in the league table from old tables
* CHANGED: Remove last hardcoded strings left
* CHANGED: Enhance plugin security by using nonces
* CHANGED: Administration interface improvements (essentially the pagination)
* CHANGED: Minor PHP & SQL improvements
* REMOVED: pagination_fixtures() has been removed

= 1.0 =
* Initial release!