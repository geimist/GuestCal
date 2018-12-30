<?php
/**
 * This file is part of GuestCal: http://guestcal.sourceforge.net/
 *
 * ©2012 dotplex e.K. / www.dotplex.de / info@dotplex.de
 * Licenced under GPLv3 Open Source Licence: http://www.gnu.org/licenses/gpl-3.0.html
 */

$pageTitle .= ' - ' . __('adminImport');

// Import from version 1.x
$r .= '<h3>' . __('adminImportv1') . '</h3>';
if (isset ($_GET['import']) && $_GET['import'] == 'guestcal1' && count ($_POST)) {
	$notices = array ();
	if ($db -> idExists ('objects', $_POST['object'])) {
		$object = $_POST['object'];
		$prefix = mysqli_real_escape_string ($_POST['prefix']);
		$nrImported = $db -> importFromV1 ($prefix, $object);
		if ($nrImported)
			$notices[] = __('adminImportv1NoticeSuccess', array ($nrImported));
		else
			$notices[] = __('adminImportv1NoticeFail');
	}
	else
		$notices[] = __('adminImportNoticeMissingObject');
	$r .= echoError ($notices);
}
$r .= '<p>' . __('adminImportv1Desc') . '</p>';
$r .= '<form action="?page=import&amp;import=guestcal1" method="post"><table>';
$r .= '<tr><th>' . __('adminImportv1Prefix') . ':</th><td><input type="text" name="prefix" size="30" maxlength="50" /></td></tr>';
$r .= '<tr><th>' . __('adminImportToObject') . ':</th><td>' . echoSelectObject ($db, 0, 'object', false) . '</td></tr>';
$r .= '</table><p><input type="submit" value="' . __('adminContinue') . '" /></p></form>';
