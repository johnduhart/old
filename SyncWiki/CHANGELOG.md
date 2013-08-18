SyncWiki - Changelog
====================

Legend
------

[+] - Addtion
[-] - Removal
[~] - Change
[F] - Bugfix

0.1.12
------

* [+] Added edit counter
* [+] Counter rebuilder
* [+] Replaced SW Parser for MediaWiki's
*	[+] Bullets work
* 	[+] Intented bullets
*	[+] DT and DDs added to parser
* [~] Main Page is now know as Home
* [-] Removed layouts
* [+] Added brumbcumb link for navigation
* [+] Language file for translations
* [~] Moved tabhelper functions to the sw_helper file
* [+] Major overhaul of the Profiler
* [~] Lock levels are now constants (LOCK_STATUS_NONE, etc)
* [+] Documentated functions

0.1.11
------

* [+] Added page for system pages
* [+] Added user list
* [+] Rudementry user pages
* [-] Removed edit_page_helper, moved lone function into sw_helper
* [+] Added powered by to footer
* [+] User will now return to the page last visited when logging in and out
* [+] Toolbox at bottom of page

0.1.10
------

* [~] Updated Ion Auth
* [+] Version now stored in SyncWiki config file

0.1.9
-----

* [+] Edit previewing
* [F] Protection was not working on level 1

0.1.8
-----

* [+] Page deletion
* [+] Viewing revisions
* [+] System Controller
* [+]  Page List
* [~] Cancel buttons on page/edit changed to use form_button()

0.1.7
-----

* [+] Added page for page history
* [~] Changed function names from lock to protection
* [+] Cancel buttons on panels in page/edit

0.1.6
-----

* [~] Moved go home link above page title

0.1.5
-----

* [-] Removed SW_model
* [F] Fixed spelling on page/edit
* [~] Restructured Page_model

0.1.4
-----

* [+] Added link to go back to the home page for non-standard page
* [~] Changed login page not to use tables

0.1.3
-----

* [+] Added login system
* [~] Restructured views folder
