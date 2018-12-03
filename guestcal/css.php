<?php
/**
 * This file is part of GuestCal: http://guestcal.sourceforge.net/
 *
 * ©2012 dotplex e.K. / www.dotplex.de / info@dotplex.de
 * Licenced under GPLv3 Open Source Licence: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Globals
define ('BASEDIR', '');

// Initiate page
require_once BASEDIR . 'includes/ini.inc.php';

// Read data
$result = $db -> select ("SELECT `id`, `color` FROM `classes` WHERE `color` != ''");

$db -> close ();

// Output
header ('Content-Type: text/css; charset="utf-8"');
echo "/**\n * This file is part of GuestCal v2.1.2.\n * \n * License information available at http://www.guestcal.com/\n * ©2009 dotplex e.K. (info@dotplex.de)\n */\n\n";
foreach ($result as $class) {
	echo ".class" . $class['id'] . " {\n\tbackground-color: #" . $class['color'] . ";\n}\n";
}
