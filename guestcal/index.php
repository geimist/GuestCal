<?php
/**
 * This file is part of GuestCal: http://guestcal.sourceforge.net/
 *
 * ©2012 dotplex e.K. / www.dotplex.de / info@dotplex.de
 * Licenced under GPLv3 Open Source Licence: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Globals
define ('BASEDIR', './');

// Initiate page
require_once BASEDIR . 'includes/ini.inc.php';

if (!$db -> error) {
	// Calendar
	$cal = new Calendar ($db, $prefs);
	if (isset ($_GET['year']))
		$year = $_GET['year'];
	else
		$year = 0;
	if (isset ($_GET['object']))
		$object = $_GET['object'];
	else
		$object = $prefs['objectDefault'];
	$cal -> setObject ($object);
	$cal -> setYear ($year);
	$cal -> showTitle = $prefs['showTitleInFrontend'];
	$pageTitle = $cal -> objectName . ' - ' . $cal -> year;
	$pageHeader = $cal -> getHeader ();
	if ($prefs['objectShowSelect'])
		$r .= $cal -> showSelect ();
	$r .= $cal -> display ();
	$r .= $cal -> showLegend ();
}

$db -> close ();

// HTML-Head
if (file_exists ($templatePath . 'head.php')) {
	require_once $templatePath . 'head.php';
}

if (!isset ($pageHeader))
	$pageHeader = $pageTitle;
if (!$db -> error)
	echo echoChooseLanguage ('?year=' . $cal -> year . '&amp;object=' . $cal -> objectId . '&amp;');
echo '<h1>' . $pageHeader . '</h1>';
echo $r;

// HTML-Foot
if (file_exists ($templatePath . 'foot.php')) {
	require_once $templatePath . 'foot.php';
}
