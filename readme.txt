INTRODUCTION

This plugin encapsulates overrides for the ENVA Moodle Project

HOW TO SETUP


To be able to use this module you need first to install it using the usual Moodle admin install procedure (https://docs.moodle.org/28/en/Installing_plugins)
We use here the customsscript moodle setting (https://docs.moodle.org/dev/customscripts).
So to be able to override the view mechanisms of the quiz, you need to add the following line in your config.php file:

  $CFG->customscripts = dirname(__FILE__). '/local/enva/customscripts/';

WHAT TO EXPECT

This plugin manages the following workflow for a subset of users (External users):
- Allow to select a course as a test course
- Enrol any new user assigned to the external user role into the test course
- Once the user completes the test course he/she is enrolled onto any course that has a specific 'external_courses' tag
- The dashboard will display courses in an order of priority depending on the answers in the test course

External users have the External User role assigned to them. It is a usual student role but it flags this user as an external user.

TROUBLESHOOTING

tbd
