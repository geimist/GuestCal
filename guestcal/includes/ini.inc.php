<?php
/**
 * This file is part of GuestCal: http://guestcal.sourceforge.net/
 *
 * ©2012 dotplex e.K. / www.dotplex.de / info@dotplex.de
 * Licenced under GPLv3 Open Source Licence: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Initial environment checks
// GuestCal Version
define ('VERSION', '2.1.4');

// PHP version
$php_version_required = '5.0.0';
$php_version_current = phpversion ();
if (version_compare ($php_version_current, $php_version_required, '<')) {
	die ('ERROR: PHP version ' . $php_version_required . ' or later required! You have installed ' . $php_version_current . '.');
}

// Session
session_start ();

// Includes
require_once BASEDIR . 'includes/db.class.php';
require_once BASEDIR . 'includes/cal.class.php';
require_once BASEDIR . 'includes/functions.inc.php';
require_once BASEDIR . 'includes/config.inc.php';

// Variables
$r = ''; // Output buffer
$templatePath = BASEDIR . 'templates/default/';
$templateAdminPath = BASEDIR . 'admin/templates/default/';
$pageTitle = '';
$lang = '';
$langFromUser = false;

// PHP runtime settings
if (DEBUG) {
	ini_set ('display_errors', 1);
	error_reporting (E_ALL);
}
else {
	ini_set ('display_errors', 0);
}

// Language part 1: before db connection
// If language is stored in GET or SESSION, use this
if (isset ($_GET['lang']))
	$lang = $_GET['lang'];
elseif (isset ($_SESSION['lang']))
	$lang = $_SESSION['lang'];
// Validate or use de as default
$lang = preg_replace ('/[^a-z_]*/', '', $lang);
$langFile = BASEDIR . 'lang/' . $lang . '.inc.php';
if (file_exists ($langFile))
	$langFromUser = true;
else {
	$lang = 'de';
	$langFile = BASEDIR . 'lang/' . $lang . '.inc.php';
}
if (file_exists ($langFile)) {
	require_once $langFile; // Include
	$_SESSION['lang'] = $lang; // Store in SESSION
}
else
	trigger_error ('Language file "' . $langFile . '" not found.', E_USER_ERROR);

// Connect to db and check for errors
$db = new DatabaseConnection ($mysql);
if ($db -> error) {
	$pageTitle = __('error');
	switch ($db -> error) {
		case 1:
			$r .= echoError (__('errorDBAuth'));
			break;
		case 2:
			$r .= echoError (__('errorDBSetupNoDB'));
			break;
		case 3:
			$r .= echoError (__('errorDBSetupNoTables'));
			break;
		case 4:
		case 5:
			$r .= echoError (__('errorDBSetupVersionsDiffer'));
	}
}

// Jobs to do if successfull connected
else {
	// Read preferences
	$prefs = readPrefs ($db);
	// Language part 2: use default language from db, if hard-coded default chosen in part 1
	if (!$langFromUser) {
		$result = $db -> select ("SELECT `abbr` FROM `languages` WHERE `id`=" . $prefs['languageDefault']);
		$langFile = BASEDIR . 'lang/' . $result[0]['abbr'] . '.inc.php';
		if (file_exists ($langFile)) {
			$lang = $result[0]['abbr'];
			require_once $langFile; // Include
			$_SESSION['lang'] = $lang; // Store in SESSION
		}
	}
	// Get language id
	$result = $db -> select ("SELECT `id` FROM `languages` WHERE `abbr`='" . $lang . "'");
	define ('LANG_ID', $result[0]['id']);
	// Get active languages
	$GLOBALS['langsActive'] = readLanguages ();
}

// Define Language
define ('LANG_ABBR', $lang);
