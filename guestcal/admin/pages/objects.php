<?php
/**
 * This file is part of GuestCal: http://guestcal.sourceforge.net/
 *
 * ©2012 dotplex e.K. / www.dotplex.de / info@dotplex.de
 * Licenced under GPLv3 Open Source Licence: http://www.gnu.org/licenses/gpl-3.0.html
 */

$pageTitle .= ' - ' . __('adminObjects');

// Save or delete
if (isset ($_POST['object'])) {
	$post = $db -> mysql_real_escape_mixed ($_POST);
	if ($db -> idExists ('objects', $post['object'])) {
		$id = $post['object'];
		$db -> query ("DELETE FROM `objects_per_language` WHERE `object_id`=" . $id);
	}
	else
		$id = $db -> query ("INSERT INTO `objects` SET `id`=NULL", 'insert_id');
	foreach ($GLOBALS['langsActive'] as $lang)
		$db -> query ("INSERT INTO `objects_per_language` SET `name`='" . $post['name_' . $lang['abbr']] . "', `desc`='" . $post['desc_' . $lang['abbr']] . "', `language_id`=" . $lang['id'] . ", `object_id`=" . $id);
}
elseif (isset ($_GET['action']) && $_GET['action'] == 'del2' && $db -> idExists ('objects', $_GET['object'])) {
	$id = $_GET['object'];
	$db -> query ("DELETE `entries_per_language` FROM `entries_per_language` LEFT JOIN `entries` ON `entries`.`id`=`entries_per_language`.`entry_id` WHERE `entries`.`object_id`=" . $id);
	$db -> query ("DELETE `entries_static` FROM `entries_static` LEFT JOIN `entries` ON `entries`.`id`=`entries_static`.`entry_id` WHERE `entries`.`object_id`=" . $id);
	$db -> query ("DELETE `entry_returning` FROM `entry_returning` LEFT JOIN `entries` ON `entries`.`id`=`entry_returning`.`entry_id` WHERE `entries`.`object_id`=" . $id);
	$db -> query ("DELETE FROM `entries` WHERE `object_id`=" . $id);
	$db -> query ("DELETE FROM `objects_per_language` WHERE `object_id`=" . $id);
	$db -> query ("DELETE FROM `objects` WHERE `id`=" . $id);
}

// Read objects
$array = $db -> select ("SELECT `id` FROM `objects` ORDER BY `id`");
if ($array) {
	foreach ($array as $row)
		$objects[$row['id']] = array ();
	$langs = array ();
	foreach ($GLOBALS['langsActive'] as $lang)
		$langs[] = $lang['id'];
	$array = $db -> select ("SELECT `object_id`, `language_id`, `name`, `desc` FROM `objects_per_language` WHERE `language_id`=" . implode (' OR `language_id`=', $langs));
	foreach ($array as $row)
		$objects[$row['object_id']][$row['language_id']] = array (
			'name' => $row['name'],
			'desc' => $row['desc']
		);
}
else
	$objects = array ();

// Create, edit or delete object
if (isset ($_GET['object']) && array_key_exists ($_GET['object'], $objects)) {
	$id = $_GET['object'];
}
else {
	$id = 0;
}
if (isset ($_GET['action']) && $_GET['action'] == 'del' && $id > 0) {
	$r .= '<h3>' . __('adminDeleteObject') . ' (' . $id . '):</h3>';
	$r .= '<p>' . __('adminDeleteObjectReally') . '</p>';
	$r .= '<p><input type="button" onclick="document.location=\'?page=objects&amp;action=del2&amp;object=' . $id . '\'" value="' . __('adminContinue') . '" /> <input type="button" onclick="document.location=\'?page=objects\'" value="' . __('adminCancel') . '" /></p>';
}
else {
	if ($id == 0) {
		$r .= '<h3>' . __('adminNewObject') . ':</h3>';
	}
	else {
		$r .= '<h3>' . __('adminEditObject') . ' (' . $id . '):</h3>';
	}
	// Form
	$r .= '<form action="?page=objects" method="post">';
	$r .= '<table>';
	foreach ($GLOBALS['langsActive'] as $lang) {
		$r .= '<tr><th>' . __('adminName') . ' (' . $lang['name'] . '):</th>';
		$r .= '<td><input type="text" name="name_' . $lang['abbr'] . '" size="30" maxlength="50"';
		if (isset ($objects[$id][$lang['id']]))
			$r .= ' value="' . $objects[$id][$lang['id']]['name'] . '"';
		$r .= ' /></td></tr>';
	}
	foreach ($GLOBALS['langsActive'] as $lang) {
		$r .= '<tr><th>' . __('adminDesc') . ' (' . $lang['name'] . '):</th>';
		$r .= '<td><textarea name="desc_' . $lang['abbr'] . '" rows="5" cols="50">';
		if (isset ($objects[$id][$lang['id']]))
			$r .= $objects[$id][$lang['id']]['desc'];
		$r .= '</textarea></td></tr>';
	}
	$r .= '</table>';
	$r .= '<p><input type="submit" value="' . __('adminSubmit') . '" /> <input type="button" onclick="document.location=\'?page=objects\'" value="' . __('adminCancel') . '" /><input type="hidden" name="object" value="' . $id . '" /></p>';
	$r .= '</form>';
}




// List objects
$r .= '<h3>' . __('adminObjects') . ':</h3>';
$r .= '<table>';
$r .= '<tr><th>' . __('adminNo') . '</th>';
foreach ($GLOBALS['langsActive'] as $lang) {
	$r .= '<th>' . __('adminName') . ' (' . $lang['name'] . ')</th>';
	$r .= '<th>' . __('adminDesc') . ' (' . $lang['name'] . ')</th>';
}
$r .= '</tr>';
foreach ($objects as $id => $content) {
	$r .= '<tr><td>' . $id . '</td>';
	foreach ($GLOBALS['langsActive'] as $lang) {
		$r .= '<td>';
		if (isset ($content[$lang['id']]))
			$r .= $content[$lang['id']]['name'];
		$r .= '</td><td>';
		if (isset ($content[$lang['id']]))
			$r .= shortenString ($content[$lang['id']]['desc']);
		$r .= '</td>';
	}
	$r .= '<td><a href="?page=objects&amp;object=' . $id . '">' . __('adminEdit') . '</a> | <a href="?page=objects&amp;object=' . $id . '&amp;action=del">' . __('adminDelete') . '</a></td>';
	$r .= '</tr>';
}
$r .= '</table>';
