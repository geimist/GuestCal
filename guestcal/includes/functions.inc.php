<?php
/**
 * This file is part of GuestCal: http://guestcal.sourceforge.net/
 *
 * ©2012 dotplex e.K. / www.dotplex.de / info@dotplex.de
 * Licenced under GPLv3 Open Source Licence: http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 * returns language string for given index
 * @param	$index	index to search for
 * @param	$vars	each element of this array will replace one % of the string
 * @return			false if index not found
 */
function __($index, $vars = false) {
	global $str;
	if (isset ($str[$index])) {
		$string = $str[$index];
		if (is_array ($vars)) {
			foreach ($vars as $var) {
				$string = preg_replace ('/%/', $var, $string, 1);
			}
		}
		return $string;
	}
	else
		return $index;
}

/**
 * Print errors.
 * @param	$errors		Array or String
 * @return				String HTML
 */
function echoError ($errors) {
	$r = '';
	if (!is_array ($errors) && $errors != '')
		$errors = array ($errors);
	if (count ($errors)) {
		$r .= '<ul class="error">';
		foreach ($errors as $str) {
			$r .= '<li>' . $str . '</li>';
		};
		$r .= '</ul>';
	}
	return $r;
}

/**
 * Prints one * for each char of the given string.
 * @param	$str	String
 * @return			String
 */
function hidePassword ($str) {
	$r = '';
	for ($i = 0; $i < strlen ($str); $i++)
		$r .= '*';
	return $r;
}

/**
 * Prints a shortened version of given string
 */
function shortenString ($string, $maxlength = 25, $suffix = '...') {
	if (strlen ($string) > $maxlength) {
		$string = substr($string, 0, ($maxlength - strlen ($suffix))) . $suffix;
	}
	return ($string);
}

/**
 * Adds leading zeros integers until they fit the given length
 * @param	$int	Int
 * @param	$length	Int
 * @return			String
 */
function addLeadingZero ($int, $length = 2) {
	$lenInt = strlen ($int);
	if ($lenInt < $length) {
		for ($i = $lenInt; $i < $length; $i ++)
			$int = '0' . $int;
	}
	return $int;
}

/**
 * Simulates date ('N', $timestamp) which PHP <5.1.0 doesn't support
 * ISO-8601 numeric representation of the day of the week (1 (for Monday) through 7 (for Sunday))
 * @param	$timestamp	int
 * @return				int
 */
function dateN ($timestamp) {
	// get normal weekday (0 for sunday, 6 for saturday)
	$weekday = date ('w', $timestamp);
	// set sunday to 7
	if ($weekday == 0)
		$weekday = 7;
	return $weekday;
}

/**
 * Read preferences from db.
 * @param	$db		object db-link
 * @return			array
 */
function readPrefs (&$db) {
	$result = $db -> select ("SELECT * FROM `prefs`");
	foreach ($result as $entry) {
		$prefs[$entry['name']] = $entry['value'];
	}
	return $prefs;
}

function readLanguages ($inactive = false) {
	global $db;
	if ($inactive)
		$result = $db -> select ("SELECT * FROM `languages` ORDER BY `name`");
	else
		$result = $db -> select ("SELECT * FROM `languages` WHERE `active`=1 ORDER BY `name`");
	if ($result === false)
		$result = array ();
	return $result;
}

function echoChooseLanguage ($link) {
	if (count ($GLOBALS['langsActive']) > 1) {
		$r = '<div id="languages">';
		$array = array ();
		foreach ($GLOBALS['langsActive'] as $lang) {
			if ($lang['id'] == LANG_ID)
				$array[] = '<b>' . $lang['name'] . '</b>';
			else
				$array[] = '<a href="' . $link . 'lang=' . $lang['abbr'] . '">' . $lang['name'] . '</a>';
		}
		$r .= implode (' | ', $array);
		$r .= '</div>';
		return $r;
	}
	else
		return '';
}

/**
 * Displays select fields for a date.
 * @param	$prefix		string prefix for fields D, M and Y
 * @param	$date		string preselected date in format YYYY-MM-DD
 * @param	$selectOptions string additional options for <select>-tag
 * @return				HTML
 */
function echoSelectDate ($prefix, $date, $disabled = false, $onchange = false) {
	$r = '';
	$selected = array_reverse (explode ('-', $date));
	$selects = array (
		array (
			'name' => 'D',
			'start' => 1,
			'end' => 31
		),
		array (
			'name' => 'M',
			'start' => 1,
			'end' => 12
		),
		array (
			'name' => 'Y',
			'start' => 1971,
			'end' => 2037
		)
	);
	for ($i = 0; $i < 3; $i ++) {
		$r .= '<select name="' . $prefix . $selects[$i]['name'] . '" id="' . $prefix . $selects[$i]['name'] . '" size="1"';
		if ($disabled)
			$r .= ' disabled="disabled"';
		if ($onchange)
			$r .= ' onchange="' . $onchange . '"';
		$r .= '>';
		for ($j = $selects[$i]['start']; $j <= $selects[$i]['end']; $j ++) {
			$r .= '<option value="' . $j . '"';
			if ($j == $selected[$i])
				$r .= ' selected="selected"';
			$r .= '>' . $j . '</option>';
		}
		$r .= '</select>';
	}
	return $r;
}

function echoSelectClass (&$db, $selected, $name, $nullId = false) {
	$r = '';
	$r .= '<select name="' . $name . '" size="1">';
	if ($nullId)
		$r .= '<option value="0">' . $nullId . '</option>';
	$array = $db -> select ("SELECT `class_id`, `name` FROM `classes_per_language` WHERE `language_id`=" . LANG_ID . " ORDER BY `name`");
	if ($array) {
		foreach ($array as $row) {
			$r .= '<option value="' . $row['class_id'] . '"';
			if ($row['class_id'] == $selected)
				$r .= ' selected="selected"';
			$r .= '>' . $row['name'] . '</option>';
		}
	}
	$r .= '</select>';
	return $r;
}

function echoSelectObject (&$db, $selected, $name, $showAllObjects = true) {
	$r = '';
	$r .= '<select name="' . $name . '" size="1">';
	$array = $db -> select ("SELECT `object_id`, `name` FROM `objects_per_language` WHERE `language_id`=" . LANG_ID . " ORDER BY `name`");
	if ($showAllObjects)
		$options[0] = __('allObjects');
	if ($array) {
		foreach ($array as $row)
			$options[$row['object_id']] = $row['name'];
	}
	foreach ($options as $id => $name) {
		$r .= '<option value="' . $id . '"';
		if ($id == $selected)
			$r .= ' selected="selected"';
		$r .= '>' . $name . '</option>';
	}
	$r .= '</select>';
	return $r;
}
