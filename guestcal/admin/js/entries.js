/**
 * This file is part of GuestCal: http://guestcal.sourceforge.net/
 *
 * ©2012 dotplex e.K. / www.dotplex.de / info@dotplex.de
 * Licenced under GPLv3 Open Source Licence: http://www.gnu.org/licenses/gpl-3.0.html
 */

function disableKind () {
	var static = new Array (
		'static_fromD',
		'static_fromM',
		'static_fromY',
		'static_toD',
		'static_toM',
		'static_toY'
	);
	var returning = new Array (
		'returning_wday1',
		'returning_wday2',
		'returning_wday3',
		'returning_wday4',
		'returning_wday5',
		'returning_wday6',
		'returning_wday7'
	);
	if (document.getElementById ('static_kind').checked == true) {
		var disable = returning;
		var enable = static;
	}
	else {
		var disable = static;
		var enable = returning;
	}
	for (var i=0; i<disable.length; i++) {
		document.getElementById (disable[i]).disabled = true;
	}
	for (var i=0; i<enable.length; i++) {
		document.getElementById (enable[i]).disabled = false;
	}
}

function adjustToDate () {
	document.getElementById ('static_toD').selectedIndex = document.getElementById('static_fromD').selectedIndex;
	document.getElementById ('static_toM').selectedIndex = document.getElementById('static_fromM').selectedIndex;
	document.getElementById ('static_toY').selectedIndex = document.getElementById('static_fromY').selectedIndex;
}
