<?php
/**
 * This file is part of GuestCal: http://guestcal.sourceforge.net/
 *
 * ©2012 dotplex e.K. / www.dotplex.de / info@dotplex.de
 * Licenced under GPLv3 Open Source Licence: http://www.gnu.org/licenses/gpl-3.0.html
 */

// JavaScript
$r .= '<script src="' . BASEDIR . 'admin/js/entries.js" type="text/javascript" charset="utf-8"></script>';

$pageTitle .= ' - ' . __('adminEntries');

if (isset ($_GET['entry']) && $db -> idExists ('entries', $_GET['entry']))
	$id = $_GET['entry'];
else
	$id = 0;

// initiate calendar
$cal = new Calendar ($db, $prefs);
$cal -> enableCellsAsLinks = true;
$cal -> hideExpiredDates = false;
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

$weekdaysAbbr = __('weekdaysAbbr');

// Delete entry
if (isset ($_GET['action']) && $_GET['action'] == 'del') {
	$db -> query ("DELETE FROM `entry_returning` WHERE `entry_id`=" . $id);
	$db -> query ("DELETE FROM `entries_static` WHERE `entry_id`=" . $id);
	$db -> query ("DELETE FROM `entries_per_language` WHERE `entry_id`=" . $id);
	$db -> query ("DELETE FROM `entries` WHERE `id`=" . $id);
	$id = 0;
}
// Save entry
elseif (count ($_POST)) {
	// check values
	if ($db -> idExists ('objects', $_POST['object']))
		$object = $_POST['object'];
	else
		$object = 0;
	$cal -> setObject ($object); // display object where changes were made
	if ($db -> idExists ('classes', $_POST['class']))
		$class = $_POST['class'];
	else
		$class = 0;
	if ($_POST['kind'] == 'static' || $_POST['kind'] == 'returning')
		$kind = $_POST['kind'];
	else
		$kind = 'static';
	// insert/update main table
	if ($id == 0)
		$id = $db -> query ("INSERT INTO `entries` SET `object_id`=" . $object . ", `class_id`=" . $class . ", `kind`='" . $kind . "'", 'insert_id');
	else {
		$db -> query ("UPDATE `entries` SET `object_id`=" . $object . ", `class_id`=" . $class . ", `kind`='" . $kind . "' WHERE `id`=" . $id);
		$db -> query ("DELETE FROM `entries_per_language` WHERE `entry_id`=" . $id);
		$db -> query ("DELETE FROM `entries_static` WHERE `entry_id`=" . $id);
		$db -> query ("DELETE FROM `entry_returning` WHERE `entry_id`=" . $id);
	}
	// insert language specific
	foreach ($GLOBALS['langsActive'] as $lang)
		$db -> query ("INSERT INTO `entries_per_language` SET `entry_id`=" . $id . ", `language_id`=" . $lang['id'] . ", `desc`='" . mysql_real_escape_string ($_POST['desc_' . $lang['abbr']]) . "'");
	// insert static
	if ($kind == 'static') {
		if (@checkdate ($_POST['static_fromM'], $_POST['static_fromD'], $_POST['static_fromY'])
			&& @checkdate ($_POST['static_toM'], $_POST['static_toD'], $_POST['static_toY'])
			&& (mktime (0, 0, 0, $_POST['static_toM'], $_POST['static_toD'], $_POST['static_toY'])
				>= mktime (0, 0, 0, $_POST['static_fromM'], $_POST['static_fromD'], $_POST['static_fromY']))) {
			$from = $_POST['static_fromY'] . '-' . $_POST['static_fromM'] . '-' . $_POST['static_fromD'];
			$to = $_POST['static_toY'] . '-' . $_POST['static_toM'] . '-' . $_POST['static_toD'];
			$db -> query ("INSERT INTO `entries_static` SET `entry_id`=" . $id . ", `from`='" . $from . "', `to`='" . $to . "'");
			$cal -> setYear ($_POST['static_fromY']); // display year where changes were made
		}
	}
	// insert returning
	else {
		foreach ($_POST['returning_wday'] as $wday) {
			if (is_numeric ($wday) && $wday > 0 && $wday < 8 && $wday % 1 == 0)
				$db -> query ("INSERT INTO `entry_returning` SET `entry_id`=" . $id . ", `wday`=" . $wday);
		}
	}
	// reset id if finished editing
	if (isset ($_POST['submit']))
		$id = 0;
}

// display calendar
$r .= '<h3>' . $cal -> getHeader () . '</h3>';
$r .= $cal -> showSelect ();
$r .= $cal -> display ();

// Values
$entry = array (
	'class_id' => 0,
	'object_id' => $cal -> objectIdViewed,
	'kind' => 'static',
	'from' => date ('Y-m-d'),
	'to' => date ('Y-m-d'),
	'disableStatic' => true,
	'disableReturning' => true
);
for ($i = 1; $i < 8; $i ++) {
	$entry['returning_wday' . $i] = '';
}
foreach ($GLOBALS['langsActive'] as $lang) {
	$entry['desc'][$lang['id']] = '';
}
if ($id == 0) {
	$r .= '<h3>' . __('adminNewEntry') . ':</h3>';
	$r .= '<p>(' . __('adminEntryHelpEdit') . ')</p>';
}
else {
	$r .= '<h3>' . __('adminEditEntry') . ':</h3>';
	$r .= '<p>(' . __('adminEntryHelpNew') . ')</p>';
	$array = $db -> select ("SELECT `object_id`, `class_id`, `kind` FROM `entries` WHERE `id`=" . $id);
	$entry = array_merge ($entry, $array[0]);
	if ($entry['kind'] == 'static') {
		$array = $db -> select ("SELECT `from`, `to` FROM `entries_static` WHERE `entry_id`=" . $id);
		$entry = array_merge ($entry, $array[0]);
	}
	else {
		$array = $db -> select ("SELECT `wday` FROM `entry_returning` WHERE `entry_id`=" . $id);
		foreach ($array as $row)
			$entry['returning_wday' . $row['wday']] = ' checked="checked"';
	}
	$array = $db -> select ("SELECT `language_id`, `desc` FROM `entries_per_language` WHERE `entry_id`=" . $id);
	foreach ($array as $row)
		$entry['desc'][$row['language_id']] = $row['desc'];
}
if ($entry['kind'] == 'static')
	$entry['disableStatic'] = false;
elseif ($entry['kind'] == 'returning')
	$entry['disableReturning'] = false;

// Form
$r .= '<form action="?year=' . $cal -> year . '&amp;object=' . $cal -> objectId . '&amp;entry=' . $id . '" method="post">';
$r .= '<table>';
// class & object
$r .= '<tr><th>' . __('adminClass') . ':</th><td>' . echoSelectClass ($db, $entry['class_id'], 'class') . '</td></tr>';
$r .= '<tr><th>' . __('adminObject') . ':</th><td>' . echoSelectObject ($db, $entry['object_id'], 'object') . '</td></tr>';
// description per language
foreach ($GLOBALS['langsActive'] as $lang)
	$r .= '<tr><th>' . __('adminDesc') . ' (' . $lang['name'] . '):</th><td><input type="text" name="desc_' . $lang['abbr'] . '" size="50" maxlength="100" value="' . $entry['desc'][$lang['id']] . '" /></td></tr>';
$r .= '<tr><th>' . __('adminEntryDates') . ':</th><td>';
$r .= '<table><tr><td>';
// static
$r .= '<input type="radio" name="kind" id="static_kind" value="static" onchange="disableKind();"';
if ($entry['kind'] == 'static')
	$r .= ' checked="checked"';
$r .= ' /> <label for="static_kind">' . __('adminEntryStatic') . '</label><br />';
$r .= '<table><tr><th>' . __('adminEntryFrom') . ':</th><td>' . echoSelectDate ('static_from', $entry['from'], $entry['disableStatic'], 'adjustToDate();') . '</td></tr>';
$r .= '<tr><th>' . __('adminEntryTo') . ':</th><td>' . echoSelectDate ('static_to', $entry['to'], $entry['disableStatic']) . '</td></tr></table>';
$r .= '</td><td>';
// returning
$r .= '<input type="radio" name="kind" id="returning_kind" value="returning" onchange="disableKind();"';
if ($entry['kind'] == 'returning')
	$r .= ' checked="checked"';
$r .= ' /> <label for="returning_kind">' . __('adminEntryReturning') . '</label>';
$r .= '<table><tr><th>' . __('adminEntryWeekly') . ':</th><td>';
for ($i = 1; $i < 8; $i ++) {
	$r .= '<input type="checkbox" name="returning_wday[]" id="returning_wday' . $i . '" value="' . $i . '"';
	if ($entry['disableReturning'])
		$r .= ' disabled="disabled"';
	$r .= $entry['returning_wday' . $i] . ' /> <label for="returning_wday' . $i . '">' . $weekdaysAbbr[($i - 1)] . '</label> ';
}
$r .= '</td></tr></table>';
$r .= '</td></tr></table></td></tr>';
$r .= '</table>';
$r .= '<p><input type="submit" name="submit" value="' . __('adminSubmit') . '" /> <input type="submit" name="submitAndEdit" value="' . __('adminSubmitAndEdit') . '" /> <input type="button" onclick="document.location=\'?year=' . $cal -> year . '&amp;object=' . $cal -> objectId . '\'" value="' . __('adminCancel') . '" />';
if ($id > 0)
	$r .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" onclick="document.location=\'?year=' . $cal -> year . '&amp;object=' . $cal -> objectId . '&amp;entry=' . $id . '&amp;action=del\'" value="' . __('adminDeleteEntry') . '" />';
$r .= '</p>';
$r .= '</form>';
