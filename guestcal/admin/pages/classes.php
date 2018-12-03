<?php
/**
 * This file is part of GuestCal: http://guestcal.sourceforge.net/
 *
 * ©2012 dotplex e.K. / www.dotplex.de / info@dotplex.de
 * Licenced under GPLv3 Open Source Licence: http://www.gnu.org/licenses/gpl-3.0.html
 */

$pageTitle .= ' - ' . __('adminClasses');

// Save or delete
if (isset ($_POST['class'])) {
	if ($db -> idExists ('classes', $_POST['class'])) {
		$id = $_POST['class'];
		$db -> query ("UPDATE `classes` SET `color`='" . mysql_real_escape_string ($_POST['color']) . "' WHERE `id`=" . $id);
		$db -> query ("DELETE FROM `classes_per_language` WHERE `class_id`=" . $id);
	}
	else
		$id = $db -> query ("INSERT INTO `classes` SET `color`='" . mysql_real_escape_string ($_POST['color']) . "'", 'insert_id');
	foreach ($GLOBALS['langsActive'] as $lang)
		$db -> query ("INSERT INTO `classes_per_language` SET `name`='" . mysql_real_escape_string ($_POST['name_' . $lang['abbr']]) . "', `content`='" . mysql_real_escape_string ($_POST['content_' . $lang['abbr']]) . "', `language_id`=" . $lang['id'] . ", `class_id`=" . $id);
}
elseif (isset ($_GET['action']) && $_GET['action'] == 'del2' && $db -> idExists ('classes', $_GET['class'])) {
	$id = $_GET['class'];
	$db -> query ("DELETE `entries_per_language` FROM `entries_per_language` LEFT JOIN `entries` ON `entries`.`id`=`entries_per_language`.`entry_id` WHERE `entries`.`class_id`=" . $id);
	$db -> query ("DELETE `entries_static` FROM `entries_static` LEFT JOIN `entries` ON `entries`.`id`=`entries_static`.`entry_id` WHERE `entries`.`class_id`=" . $id);
	$db -> query ("DELETE `entry_returning` FROM `entry_returning` LEFT JOIN `entries` ON `entries`.`id`=`entry_returning`.`entry_id` WHERE `entries`.`class_id`=" . $id);
	$db -> query ("DELETE FROM `entries` WHERE `class_id`=" . $id);
	$db -> query ("DELETE FROM `classes_per_language` WHERE `class_id`=" . $id);
	$db -> query ("DELETE FROM `classes` WHERE `id`=" . $id);
}

// Read classes
$array = $db -> select ("SELECT `id`, `color` FROM `classes` ORDER BY `id`");
if ($array) {
	foreach ($array as $row)
		$classes[$row['id']] = array ('color' => $row['color']);
	$langs = array ();
	foreach ($GLOBALS['langsActive'] as $lang)
		$langs[] = $lang['id'];
	$array = $db -> select ("SELECT `class_id`, `language_id`, `name`, `content` FROM `classes_per_language` WHERE `language_id`=" . implode (' OR `language_id`=', $langs));
	foreach ($array as $row)
		$classes[$row['class_id']][$row['language_id']] = array (
			'name' => $row['name'],
			'content' => $row['content']
		);
}
else
	$classes = array ();

// Create, edit or delete class
if (isset ($_GET['class']) && array_key_exists ($_GET['class'], $classes)) {
	$id = $_GET['class'];
}
else {
	$id = 0;
}
if (isset ($_GET['action']) && $_GET['action'] == 'del' && $id > 0) {
	$r .= '<h3>' . __('adminDeleteClass') . ' (' . $id . '):</h3>';
	$r .= '<p>' . __('adminDeleteClassReally') . '</p>';
	$r .= '<p><input type="button" onclick="document.location=\'?page=classes&amp;action=del2&amp;class=' . $id . '\'" value="' . __('adminContinue') . '" /> <input type="button" onclick="document.location=\'?page=classes\'" value="' . __('adminCancel') . '" /></p>';
}
else {
	if ($id == 0) {
		$r .= '<h3>' . __('adminNewClass') . ':</h3>';
	}
	else {
		$r .= '<h3>' . __('adminEditClass') . ' (' . $id . '):</h3>';
	}
	// Form
	$r .= '<form action="?page=classes" method="post">';
	$r .= '<table>';
	foreach ($GLOBALS['langsActive'] as $lang) {
		$r .= '<tr><th>' . __('adminName') . ' (' . $lang['name'] . '):</th><td><input type="text" name="name_' . $lang['abbr'] . '" size="30" maxlength="30"';
		if (isset ($classes[$id][$lang['id']]['name']))
			$r .= ' value="' . $classes[$id][$lang['id']]['name'] . '"';
		$r .= ' /></td></tr>';
	}
	foreach ($GLOBALS['langsActive'] as $lang) {
		$r .= '<tr><th>' . __('adminCellContent') . ' (' . $lang['name'] . '):</th><td><input type="text" name="content_' . $lang['abbr'] . '" size="2" maxlength="2"';
		if (isset ($classes[$id][$lang['id']]['content']))
			$r .= ' value="' . $classes[$id][$lang['id']]['content'] . '"';
		$r .= ' /></td></tr>';
	}
	$r .= '<tr><th>' . __('adminColor') . '</th><td><input type="text" name="color" size="6" maxlength="6"';
	if (isset ($classes[$id]['color']))
		$r .= ' value="' . $classes[$id]['color'] . '"';
	$r .= ' /></td></tr>';
	$r .= '</table>';
	$r .= '<p><input type="submit" value="' . __('adminSubmit') . '" /> <input type="button" onclick="document.location=\'?page=classes\'" value="' . __('adminCancel') . '" /><input type="hidden" name="class" value="' . $id . '" /></p>';
	$r .= '</form>';
}

// List classes
$r .= '<h3>' . __('adminClasses') . ':</h3>';
$r .= '<table>';
$r .= '<tr><th>' . __('adminNo') . '</th>';
foreach ($GLOBALS['langsActive'] as $lang) {
	$r .= '<th>' . __('adminName') . ' (' . $lang['name'] . ')</th>';
}
foreach ($GLOBALS['langsActive'] as $lang) {
	$r .= '<th>' . __('adminCellContent') . ' (' . $lang['name'] . ')</th>';
}
$r .= '<th>' . __('adminColor') . '</th>';
$r .= '</tr>';
foreach ($classes as $id => $values) {
	$r .= '<tr><td>' . $id . '</td>';
	foreach ($GLOBALS['langsActive'] as $lang) {
		$r .= '<td>';
		if (isset ($values[$lang['id']]['name']))
			$r .= $values[$lang['id']]['name'];
		$r .= '</td>';
	}
	foreach ($GLOBALS['langsActive'] as $lang) {
		$r .= '<td>';
		if (isset ($values[$lang['id']]['content']))
			$r .= $values[$lang['id']]['content'];
		$r .= '</td>';
	}
	$r .= '<td style="background-color: #' . $values['color'] . '">' . $values['color'] . '</td>';
	$r .= '<td><a href="?page=classes&amp;class=' . $id . '">' . __('adminEdit') . '</a> | <a href="?page=classes&amp;class=' . $id . '&amp;action=del">' . __('adminDelete') . '</a></td>';
	$r .= '</tr>';
}
$r .= '</table>';
