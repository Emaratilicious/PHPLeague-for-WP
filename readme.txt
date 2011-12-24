=== PHPLeague for WordPress ===
Contributors: Maxime Dizerens
Donate link: http://www.phpleague.com/
Tags: phpleague, football, sport, league, championship, soccer, handball, volleyball, basketball, hockey, sports, leagues
Requires at least: 3.2
Tested up to: 3.3
Stable tag: 1.4.5

PHPLeague offers you the possibility to manage your leagues from A to Z without any hassles.

== Description ==
PHPLeague for WordPress is a plugin that let you manage your own sports leagues instead of relying on a third-party website.
This lets you have an integrated site without a lot of coding, and still letting you customize it exactly the way you’d like.

First, you activate and set up the plugin, which makes your site have all functionalities available. Then, you can get it started by inserting every data you want, one by one.

Bonus: I'm always listening to your requests and most of these are added regularly in the sourcecode improving the quality of PHPLeague. And by the way, PHPLeague will never be a premium plugin. It's 100% FREE and will stay that way!

Requires WordPress 3.1+ and PHP 5.2+.

**Current features**

* Unlimited leagues
* Unlimited clubs
* Unlimited players
* Accessible easily through your posts or pages
* Display full or partial data, it's up to you
* Get widgets everywhere on your website
* A very powerful and user-friendly back-end
* A clean and efficient (un)installer keeping your data safe.

**Coming features**

* A prediction module
* More and more statistics
* More and more sports
* A complete team dashboard section
* Export your data and use PHPLeague CMS instead.

If you have suggestions for a new add-on, feel free to email me at mdizerens@gmail.com.
Want regular updates? Go on PHPLeague.com!

http://www.phpleague.com/

Or follow me on Twitter!

http://twitter.com/mdizerens

**Languages**

* This plugin is currently available in English, French and German. Please help me translate it!

== Installation ==

PHPLeague is really easy to install so just follow the guide:

1. Upload the `phpleague` folder to the `/wp-content/plugins/` directory or install it from the repository
2. Activate the plugin through the 'Plugins' menu in WordPress
3. First of all, you are required to create a first league with a name and year
4. Then, you need to create the clubs - accessible on your PHPLeague menu
5. Go back on the dashboard and click on "Teams" to assign the clubs in this particular league
6. Then, follow the steps on your sub-menu till the last one named "Generate"
7. Once the data generated, go in your news editor and use the PHPLeague button to insert the data in your post
8. Still some trouble? Have a look to the "About" menu or go on PHPLeague.com.

== Frequently Asked Questions ==

= I've just activated PHPLeague, what do I need to do now? =

First, I strongly encourage you to read the guide I wrote for you. It may help you better understand the plugin and save you some precious time although PHPLeague is supposed to be user-friendly.

= Why did you choose not to use WordPress default tables? =

PHPLeague will be available as a standalone version for other users and in order to be able to import/export easily the data between those different environments, it wasn't possible to use the WP tables.
And let's face it, those tables are not really optimized to handle very well that kind of plugin.

= Does the plugin handle tournament (e.g. World Cup or Friendly)? =

At the moment, it does not. And it is not part of features for the next releases except if I receive a lot of requests.

= Do I need to have Javascript activated? =

I decided not to use AJAX technology but Javascript is necessary in order to manage almost everything in the administration area (thanks to the interface...).
In the front part, no Javascript at all. Always keeping in mind that plenty of browsers handle javascript badly or not at all.

= Where do I need to add my logos? =

A new `phpleague` is - supposedly - automatically created during the installation in your `uploads` directory. Then, add your logos in the right folders (mini or big).

= Another question? =

For more information, check out the PHPLeague project website: <http://www.phpleague.com/>.

== Screenshots ==

1. PHPLeague Editor Button
2. PHPLeague Ranking Table
3. PHPLeague Administration Dashboard

== Changelog ==

= 1.4.5 =
* NEW: GIF and BMP files are now accepted
* FIX: Modified the valid_text method to accept everything
* FIX: Alter YEAR type because it ranges only from 1901 to 2055

= 1.4.4 =
* NEW: Add a new method to detect the file extension
* CHG: Removed the "hide" feature for category
* CHG: Can now use "jpg" images for logos
* CHG: The notification messages are now grouped together
* CHG: The valid_text method has been improved
* CHG: Birthdate, height and weight are not mandatory anymore
* FIX: Replace click() by focusin() in the readonly inputs

= 1.4.3 =
* CHG: Change the editor button description
* FIX: Correct an issue with the editor button

= 1.4.2 =
* NEW: Pages available in the admin bar
* NEW: Possibility to create 2 clubs with identic name
* CHG: French countries list has been updated
* FIX: Admin CSS updated to work with WP 3.3+
* FIX: The `wp_tiny_mce method` is deprecated from WP 3.3+
* FIX: Deprecated variable in the widget ranking table

= 1.4.1 =
* FIX: Correct an issue showing SQL errors

= 1.4.0 =
* NEW: Get the latest PHPLeague news directly on your dashboard
* NEW: New date hint system when required
* NEW: Show how many matches a team play at home or away
* NEW: Redirect automatically users to the plugin homepage after activation
* NEW: Add the new tables to manage Player and Prediction modules
* NEW: Possibility to delete a club and all data associated
* NEW: Possibility to delete a league and all data associated
* NEW: Possibility to get the ranking table as widget
* NEW: Plugin translated in German (thanks to Michael Fürst)
* NEW: Possibility to show the 5 latest results in the table
* NEW: Possibility to show a mini ranking table as widget
* CHG: When we remove a team from a league, all data associated are now deleted
* CHG: Don't show a link in the table when links are disabled in the settings
* CHG: Remove edition name in the database
* CHG: The fixtures interface has been rebuilt with dropdown lists
* CHG: Remove input values when it's a field by default
* CHG: FRENCH language has been updated and countries list added!
* CHG: No more Premium Edition! FREE for life...
* FIX: If someone try to access a non-existing fixture, he can't no more!

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