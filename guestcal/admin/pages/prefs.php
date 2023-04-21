<?php
/**
 * This file is part of GuestCal: http://guestcal.sourceforge.net/
 *
 * ©2012 dotplex e.K. / www.dotplex.de / info@dotplex.de
 * Licenced under GPLv3 Open Source Licence: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Functions
function echoChangeYesNo ($name, $selected) {
	$r = '';
	$r .= '<input type="radio" name="' . $name . '" id="' . $name . '1" value="1"';
	if ($selected == 1)
		$r .= ' checked="checked"';
	$r .= ' /> <label for="' . $name . '1">' . __('yes') . '</label>';
	$r .= ' <input type="radio" name="' . $name . '" id="' . $name . '0" value="0"';
	if ($selected == 0)
		$r .= ' checked="checked"';
	$r .= ' /> <label for="' . $name . '0">' . __('no') . '</label>';
	return $r;
}

function echoSelectLanguage ($selected) {
	$r = '';
	$r .= '<select name="languageDefault" size="1">';
	if (count ($GLOBALS['langsActive'])) {
		foreach ($GLOBALS['langsActive'] as $row) {
			$r .= '<option value="' . $row['id'] . '"';
			if ($selected == $row['id'])
				$r .= ' selected="selected"';
			$r .= '>' . $row['name'] . '</option>';
		}
	}
	$r .= '</select>';
	return $r;
}

function echoActivateLanguages () {
	$r = '<table cellspacing="0" cellpadding="2" border="0">';
	$result = readLanguages (true);
	foreach ($result as $row) {
		$r .= '<tr><td><input type="checkbox" name="languagesActive[]" value="' . $row['id'] . '"';
		if ($row['active'] == 1)
			$r .= ' checked="checked"';
		$r .= ' /></td>';
		$r .= '<td>' . $row['name'] . '</td>';
		$r .= '</tr>';
	}
	$r .= '</table>';
	return $r;
}

$pageTitle .= ' - ' . __('adminPrefs');

// Save changes
if (count ($_POST)) {
	$post = $db -> mysql_real_escape_mixed ($_POST);
	foreach ($prefs as $name => $value) {
		if (isset ($_POST[$name]) && $_POST[$name] != $value) {
			$db -> query ("UPDATE `prefs` SET `value`='" . $post[$name] . "' WHERE `name`='" . $name . "'");
		}
	}
	$prefs = readPrefs ($db);
	$languages = readLanguages (true);
	foreach ($languages as $language) {
		if (isset ($_POST['languagesActive']) && in_array ($language['id'], $_POST['languagesActive'])) {
			if ($language['active'] == 0)
				$db -> query ("UPDATE `languages` SET `active`=1 WHERE `id`=" . $language['id']);
		}
		else {
			if ($language['active'] == 1)
				$db -> query ("UPDATE `languages` SET `active`=0 WHERE `id`=" . $language['id']);
		}
	}
	$GLOBALS['langsActive'] = readLanguages ();
	unset ($_SESSION['lang']);
}

// Prefs table
$r .= '<form action="?page=prefs" method="post">';
$r .= '<table>';
$r .= '<tr><th>' . __('prefsLanguageDefault') . ':</th><td>' . echoSelectLanguage ($prefs['languageDefault']) . '</td></tr>';
$r .= '<tr><th>' . __('prefsLanguagesActive') . ':</th><td>' . echoActivateLanguages () . '</td></tr>';
$r .= '<tr><th>' . __('prefsObjectDefault') . ':</th><td>' . echoSelectObject ($db, $prefs['objectDefault'], 'objectDefault') . '</td></tr>';
$r .= '<tr><th>' . __('prefsObjectShowSelect') . ':</th><td>' . echoChangeYesNo ('objectShowSelect', $prefs['objectShowSelect']) . '</td></tr>';
$r .= '<tr><th>' . __('prefsClassDefault') . ':</th><td>' . echoSelectClass ($db, $prefs['classDefault'], 'classDefault') . '</td></tr>';
$r .= '<tr><th>' . __('prefsClassFirstAndLastDay') . ':</th><td>' . echoSelectClass ($db, $prefs['classFirstAndLastDay'], 'classFirstAndLastDay', __('none')) . '</td></tr>';
$r .= '<tr><th>' . __('prefsHideExpiredDates') . ':</th><td>' . echoChangeYesNo ('hideExpiredDates', $prefs['hideExpiredDates']) . '</td></tr>';
$r .= '<tr><th>' . __('prefsShowTitleInFrontend') . ':</th><td>' . echoChangeYesNo ('showTitleInFrontend', $prefs['showTitleInFrontend']) . '</td></tr>';
$r .= '</table>';
$r .= '<p><input type="submit" value="' . __('adminSubmit') . '" /> <input type="reset" value="' . __('adminCancel') . '" /></p>';
$r .= '</form>';
