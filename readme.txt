INTRODUCTION

This plugin encapsulates overrides for the ENVA Moodle Project

HOW TO INSTALL

To be able to use this module you need first to install it using the usual Moodle admin install procedure (https://docs.moodle.org/28/en/Installing_plugins)
We use here the customsscript moodle setting (https://docs.moodle.org/dev/customscripts).
So to be able to override the view mechanisms of the quiz, you need to add the following line in your config.php file:

  $CFG->customscripts = dirname(__FILE__). '/local/enva/customscripts/';


TROUBLESHOOTING

<TO BE WRITTEN>

