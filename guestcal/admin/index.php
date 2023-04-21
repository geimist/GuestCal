<?php
/**
 * This file is part of GuestCal: http://guestcal.sourceforge.net/
 *
 * ©2012 dotplex e.K. / www.dotplex.de / info@dotplex.de
 * Licenced under GPLv3 Open Source Licence: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Globals
define ('BASEDIR', '../');

// Initiate page
require_once BASEDIR . 'includes/ini.inc.php';

if (!$db -> error) {
	$pageTitle = __('admin');
	// Page selection
	$r .= '<p id="menu"><a href="?page=entries">' . __('adminEntries') . '</a> | <a href="?page=classes">' . __('adminClasses') . '</a> | <a href="?page=objects">' . __('adminObjects') . '</a> | <a href="?page=import">' . __('adminImport') . '</a> | <a href="?page=prefs">' . __('adminPrefs') . '</a></p>';
	$pages = array ('entries', 'classes', 'objects', 'import', 'prefs');
	if (isset ($_GET['page']) && in_array ($_GET['page'], $pages))
		$page = $_GET['page'];
	else
		$page = 'entries';
	require_once (BASEDIR . 'admin/pages/' . $page . '.php');
}

$db -> close ();

// HTML-Head
if (file_exists ($templateAdminPath . 'head.php')) {
	require_once $templateAdminPath . 'head.php';
}
if (!$db -> error)
	echo echoChooseLanguage ('?');
echo '<h1>' . $pageTitle . '</h1>';
echo $r;

// Check for updates
if (!$db -> error)
	//echo '<iframe width="600" height="50" scrolling="no" frameborder="0" src="http://www.guestcal.com/versioncheck.php?lang=' . LANG_ABBR . '&amp;version=' . VERSION . '"></iframe>';

// HTML-Foot
if (file_exists ($templateAdminPath . 'foot.php')) {
	require_once $templateAdminPath . 'foot.php';
}
