=== PHPLeague for WordPress ===
Contributors: Maxime Dizerens
Donate link: http://www.mika-web.com/
Tags: phpleague, football, sport, league, championship, soccer, handball, volleyball, basketball, hockey
Requires at least: 3.1
Tested up to: 3.2.1
Stable tag: 1.2.5

PHPLeague for WordPress is the best plugin to manage properly your sports leagues. Get the Premium version to enjoy more features.

== Description ==

PHPLeague for WordPress is the best plugin to manage properly your sports leagues. This plugin is very customizable and easy to use.
From now on, get your data directly from your own database - powered by WordPress - instead of relying on another website or widget.
For people who need more customizations and modularity, the Premium version is waiting for you as soon as it will be released :)
This plugin can work perfectly for other sports than football like handball, basketball, hockey or volleyball.

**Some "Core" Features**

* The League's table and fixtures can be published via your posts/pages
* Manage as many leagues as you want, no limitations
* Most of the fill-in part is done automatically
* A powerful administration area who simplify your life
* A clean (un)installer developed in order to keep your database secure and safe
* A nice administration area based on the WooThemes Theme.

**Some "Premium" Features**

* Match live score system with possibility to link with your posts
* Prediction system for every league with minimal settings - handle native WordPress users
* Manage the players profile of your favorite team with stats / charts rendered automatically
* Link your players' profile with tags and get all their related news
* Player of the Week/Month of your favorite team through widgets
* Possibility to vote for the Man of the Match
* And much more...

**Languages**

* This plugin is currently available in English and in French. Please help us translate it!

== Installation ==

PHPLeague is really easy to install so just follow the guide:

1. Upload the `phpleague` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Create your season & competition then generate a league
4. Create your clubs and affect them to your league
5. Choose the fixtures' dates then the matches (everything's generated automatically)
6. Insert your results then generate the table & calendar
7. Place `[phpleague id="YOUR-ID" type="general"]` in your post/page
8. Find all the information you need on the About page in the admin area.

== Frequently Asked Questions ==

= I've just activated PHPLeague, what do I need to do now? =

First, I strongly encourage you to read the guide I wrote for you. It may help you better understand the plugin and save you some precious time although PHPLeague is user-friendly oriented.

= Why did you choose not to use WordPress default tables? =

PHPLeague will be available as a standalone version for other users and in order to be able to import/export easily the data between those different environments, it wasn't possible to use the WP tables.
And let's face it, those tables are not really optimized to handle very well that kind of plugin.

= Does the plugin handle tournament (e.g. World Cup or Friendly)? =

At the moment, it does not. And it is not part of features for the next releases except if I receive a lot of requests.

= Do I need to have Javascript activated? =

I decided not to use AJAX but Javascript is necessary in order to manage almost everything in the administration area (thanks to the interface...).
In the front part, no Javascript at all. Always keeping in mind that plenty of browsers handle javascript badly or not at all.

== Screenshots ==

1. Help section
2. PHPLeague Dashboard
3. Clubs Listing

== Changelog ==

= 1.3 (Coming Soon) =
* NEW: Import/Export function
* NEW: Possibility to select your club's logo from the uploads folder
* NEW: Display the mini logo in the table if it exists
* NEW: Add a new tab "Clubs" in the PHPLeague button
* NEW: New shortcode to display the club's information in a post/page
* CHANGED: Show a notice if we suspect an error with the number of matches
* CHANGED: Possibility to add more than one message per page
* CHANGED: Update and finalize the documentation on the about page

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