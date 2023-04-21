<?php
/**
 * This file is part of GuestCal: http://guestcal.sourceforge.net/
 *
 * ©2012 dotplex e.K. / www.dotplex.de / info@dotplex.de
 * Licenced under GPLv3 Open Source Licence: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Database connection
$mysql['host'] = 'localhost';	// In most cases this is localhost
$mysql['port'] = '3306';		// Port-Number or non-standard socket. You don't want to change this.
$mysql['user'] = 'DB_USER';		// Insert your database-username
$mysql['pass'] = 'DB_PASS';		// Insert your database-password
$mysql['name'] = 'DB_NAME';		// Database, where GuestCal should store its data. If it does not exist, the user must have rights to create it.
$mysql['pfix'] = 'guestcal_';	// Optional prefix of all GuestCal-tables in database. This must match the prefix selected at installation.

// You don't want to change this.
define ('DEBUG', false);
