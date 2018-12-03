<?php
/**
 * This file is part of GuestCal: http://guestcal.sourceforge.net/
 *
 * ©2012 dotplex e.K. / www.dotplex.de / info@dotplex.de
 * Licenced under GPLv3 Open Source Licence: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Globals
define ('BASEDIR', '../../');

// Initiate page
require_once BASEDIR . 'includes/ini.inc.php';

// Variables
$errors = array ();

// Continue only if db connected
if ($db -> error != 1) {
	// Start Installation
	if (isset ($_GET['install'])) {
		// Create db if necessary
		if ($db -> error == 2) {
			$result = $db -> createDB ();
			if ($result !== true)
				$errors[] = __('setupCreateDBFailed') . '<br /><i>' . $result . '</i>';
		}
		// Insert empty tables if new installation or upgrade from v1.x
		if (!count ($errors) && ($db -> error == 2 || $db -> error == 3 || $db -> error == 5)) {
			$result = $db -> importDump ('sql/current.mysql');
			if ($result !== true)
				$errors = $result;
			elseif ($db -> error == 5)
				$db -> importFromV1 ();
		}
		if ($db -> error == 4) { // DB-Version and Files-Version differ
			$result = $db -> update ();
			if ($result !== true)
				$errors = $result;
		}
		// Setup new database connection to display new installation status
		$db -> close ();
		$db -> connect ();
	}
	$db -> close ();
	// Overwrite possible db-error-output from ini
	$pageTitle = __('setup');
	$r = '';
	// Display errors if exist
	if (count ($errors)) {
		$r .= '<h3>' . __('error') . '</h3>';
		$r .= echoError ($errors);
	}
	// Welcome
	$r .= '<h3>' . __('setupWelcome') . '</h3>';
	$r .= '<p>' . __('setupIntro') . '</p>';
	$r .= '<p>' . __('setupDBConfiguration') . '</p>';
	$r .= '<table><tr><th>' . __('setupServer') . ':</th><td>' . $mysql['host'] . ':' . $mysql['port'] . '</td></tr>';
	$r .= '<tr><th>' . __('setupUser') . ':</th><td>' . $mysql['user'] . '</td></tr>';
	$r .= '<tr><th>' . __('setupPass') . ':</th><td>' . hidePassword ($mysql['pass']) . '</td></tr>';
	$r .= '<tr><th>' . __('setupDB') . ':</th><td>' . $mysql['name'] . '</td></tr>';
	$r .= '<tr><th>' . __('setupPfix') . ':</th><td>' . $mysql['pfix'] . '</td></tr></table>';
	$r .= '<h3>' . __('setupIndications') . '</h3><ul>';
	if (!$db -> error)
		$r .= '<li>' . __('setupIndicationSuccess') . '</li>';
	elseif ($db -> error == 2) {
		$r .= '<li>' . __('setupIndicationNewDB') . '</li>';
		$r .= '<li>' . __('setupIndicationNewTables') . '</li>';
	}
	elseif ($db -> error == 3) {
		$r .= '<li>' . __('setupIndicationNewTables') . '</li>';
	}
	elseif ($db -> error == 4) {
		$r .= '<li>' . __('setupIndicationUpgrade') . '</li>';
		$r .= '<li>' . __('setupIndicationsBackupWarning') . '</li>';
	}
	elseif ($db -> error == 5) {
		$r .= '<li>' . __('setupIndicationUpgradeFrom1') . '</li>';
		$r .= '<li>' . __('setupIndicationsBackupWarning') . '</li>';
	}
	$r .= '</ul>';
	// Link to continue
	if ($db -> error)
		$r .= '<p><a href="?install=1">' . __('setupStart') . '</a></p>';
	else
		$r .= '<p><a href="' . BASEDIR . 'admin/">' . __('setupContinueToAdmin') . '</a></p>';
}

// HTML-Head
if (file_exists ($templateAdminPath . 'head.php')) {
	require_once $templateAdminPath . 'head.php';
}

echo '<div id="languages"><a href="?lang=de">Deutsch</a> | <a href="?lang=en">English</a></div>';
echo '<h1>' . $pageTitle . '</h1>';
echo $r;

// HTML-Foot
if (file_exists ($templateAdminPath . 'foot.php')) {
	require_once $templateAdminPath . 'foot.php';
}
