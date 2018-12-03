<?php
/**
 * This file is part of GuestCal: http://guestcal.sourceforge.net/
 *
 * ©2012 dotplex e.K. / www.dotplex.de / info@dotplex.de
 * Licenced under GPLv3 Open Source Licence: http://www.gnu.org/licenses/gpl-3.0.html
 */

class Calendar {

	public $enableCellsAsLinks = false;
	public $hideExpiredDates = false;
	public $showTitle = true;
	public $year = false;
	public $objectId = false;
	public $objectIdViewed = false;
	public $objectName = false;
	public $objectDesc = false;
	private $prefs = false;
	private $db = false;

	function calendar (&$db, &$prefs) {
		$this -> db =& $db;
		$this -> prefs =& $prefs;
		$this -> hideExpiredDates = $this -> prefs['hideExpiredDates'];
	}

	/**
	 * Sets year to be displayed.
	 * @param	$year	String  from GET
	 * @return			true
	*/
	function setYear ($year) {
		// Check year
		if (is_numeric ($year) && $year % 1 == 0 && $year > 1970 && $year < 2038)
			$this -> year = $year;
		else
			$this -> year = date ('Y');
		return true;
	}

	/**
	 * Sets object to be displayed.
	 * @param	$object	String  from GET
	 * @return			true
	*/
	function setObject ($object) {
		if ($this -> db -> idExists ('objects', $object)) {
			$this -> objectId = $object;
			$array = $this -> db -> select ("SELECT `name`, `desc` FROM `objects_per_language` WHERE `object_id`=" . $this -> objectId . " AND `language_id`=" . LANG_ID);
			$this -> objectName = $array[0]['name'];
			$this -> objectDesc = $array[0]['desc'];
		}
		else {
			$this -> objectId = 0;
			$this -> objectName = __('allObjects');
		}
		if ($this -> objectIdViewed === false)
			$this -> objectIdViewed = $this -> objectId;
		return true;
	}

	/**
	 * Display title with links to change year
	 * @return		HTML
	 */
	function getHeader () {
		$r = '<a href="?object=' . $this -> objectId . '&amp;year=' . ($this -> year - 1) . '">&lt;&lt;</a> ';
		$r .= $this -> year;
		$r .= ' <a href="?object=' . $this -> objectId . '&amp;year=' . ($this -> year + 1) . '">&gt;&gt;</a> ';
		return $r;
	}

	/**
	 * Display <select> for object changing
	 * @return		HTML
	 */
	function showSelect () {
		$r = '<div id="objectSelection">';
		$r .= '<p>' . __('selectObject') . ': ';
		$r .= '<select size="1" onchange="document.location=\'?year=' . $this -> year . '&amp;object=\'+this.value">';
		$array = $this -> db -> select ("SELECT `object_id`, `name` FROM `objects_per_language` WHERE `language_id`=" . LANG_ID . " ORDER BY `name`");
		$options[0] = __('allObjects');
		if ($array) {
			foreach ($array as $row)
				$options[$row['object_id']] = $row['name'];
		}
		foreach ($options as $id => $name) {
			$r .= '<option value="' . $id . '"';
			if ($id == $this -> objectId)
				$r .= ' selected="selected"';
			$r .= '>' . $name . '</option>';
		}
		$r .= '</select>';
		$r .= '</p>';
		$r .= '</div>';
		return $r;
	}

	/**
	 * Display legend
	 * @return		HTML
	 */
	function showLegend () {
		$r = '';
		$r .= '<div id="legend">';
		$classes = $this -> db -> select ("
			SELECT `class_id`,
					`color`,
					`content`,
					`name`
				FROM `classes_per_language`
				LEFT JOIN `classes` ON `classes`.`id`=`classes_per_language`.`class_id`
				WHERE `language_id`=" . LANG_ID . "
				ORDER BY `class_id`"
		);
		foreach ($classes as $class) {
			if (!$class['content'])
				$class['content'] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$r .= '<span class="class' . $class['class_id'] . '"';
			if (!$class['color'] || $class['color'] == 'ffffff')
				$r .= ' style="border: 1px solid black"';
			$r .= '>' . $class['content'] . '</span> ' . $class['name'];
		}
		$r .= '</div>';
		return $r;
	}

	/**
	 * Display calendar for selected year and object.
	 * @return		HTML
	 */
	function display () {
		$r = '';
		if ($this -> objectId > 0)
			$r .= $this -> echoTable ($this -> readData ());
		else {
			$objects = $this -> db -> select ("SELECT `object_id` FROM `objects_per_language` WHERE `language_id`=" . LANG_ID . " ORDER BY `name`");
			if ($objects) {
				foreach ($objects as $object) {
					$this -> setObject ($object['object_id']);
					$r .= $this -> echoTable ($this -> readData ());
				}
			}
		}
		return $r;
	}

	/**
	 * Display calendar table for selected year and object.
	 * @param	$dates	array with cell contents
	 * @return			HTML
	 */
	private function echoTable ($dates) {
		$r = '';
		$months = __('months');
		$r .= '<h3>' . $this -> objectName . '</h3>';
		if (strlen ($this -> objectDesc) > 0)
			$r .= '<p class="objectDesc">' . $this -> objectDesc . '</p>';
		$r .= '<table class="calendar" cellspacing="0">';
		for ($m = 0; $m <=12; $m++) { // months
			$r .= '<tr>';
			$r .= '<th>' . $months[$m] . '</th>';
			for ($d = 1; $d <= 31; $d++) { // days
				if ($m == 0)
					$r .= '<th>' . addLeadingZero ($d) . '</th>';
				else
					if (isset ($dates[$m][$d])) {
						$r .= '<td';
						if ($dates[$m][$d]['class'])
							$r .= ' class="' . $dates[$m][$d]['class'] . '"';
						if ($this -> showTitle && $dates[$m][$d]['desc'])
							$r .= ' title="' . $dates[$m][$d]['desc'] . '"';
						if ($this -> enableCellsAsLinks && isset ($dates[$m][$d]['id']))
							$r .= ' style="cursor: pointer;" onclick="document.location=\'?year=' . $this -> year . '&amp;object=' . $this -> objectId . '&amp;entry=' . $dates[$m][$d]['id'] . '\';"';
						$r .= '>';
						$r .= $dates[$m][$d]['content'];
						$r .= '</td>';
					}
					else
						$r .= '<td></td>';
			}
			$r .= '</tr>';
		}
		$r .= '</table>';
		return $r;
	}

	/**
	* Reads data from db and creates an array with all valid dates and values.
	* @return		Array (month[day][class|content|desc])
	*/
	private function readData () {
		$dates = array (); // main array with all valid dates
		if ($this -> hideExpiredDates && $this -> year == date ('Y')) {
			$dateStart = date ('Ymd');
			$dateStartMonth = date ('m');
			$dateStartDay = date ('d');
		}
		elseif ($this -> hideExpiredDates && $this -> year < date ('Y')) {
			$dateStart = date ('Y') . '1232';
			$dateStartMonth = '12';
			$dateStartDay = '32';
		}
		else {
			$dateStart = date ('Y') . '0101';
			$dateStartMonth = '01';
			$dateStartDay = '01';
		}
		// classes
		$array = $this -> db -> select ("SELECT `id`, `color` FROM `classes`");
		if ($array) {
			foreach ($array as $row)
				$classes[$row['id']] = array (
					'content' => '',
					'color' => $row['color']
				);
		}
		// classes: language specific (only added if available)
		$array = $this -> db -> select ("SELECT `class_id`, `content` FROM `classes_per_language` WHERE `language_id`=" . LANG_ID);
		if ($array) {
			foreach ($array as $row)
				$classes[$row['class_id']]['content'] = $row['content'];
		}
		// default class
		if ($this -> prefs['classDefault']) {
			$defaultClass = 'class' . $this -> prefs['classDefault'];
			$defaultContent = $classes[$this -> prefs['classDefault']]['content'];
		}
		else {
			$defaultClass = '';
			$defaultContent = '';
		}
		// read returning entries
		$array = $this -> db -> select (
			"SELECT `entries`.`id`,
					`entries`.`class_id`,
					`entry_returning`.`wday`,
					`entry_returning`.`day`,
					`entry_returning`.`month`
				FROM `entries`
				LEFT JOIN `entry_returning` ON `entry_returning`.`entry_id`=`entries`.`id`
				WHERE `entries`.`kind`='returning'
					AND (`entries`.`object_id`=" . $this -> objectId . " OR `entries`.`object_id`=0)"
		);
		$wdaysReturning = array ();
		if ($array) {
			foreach ($array as $row) {
				if ($row['wday'])
					$wdaysReturning[$row['wday']] = array (
						'class_id' => $row['class_id'],
						'id' => $row['id']
					);
				elseif ($row['day'])
					$daysReturning[$row['month']][$row['day']] = array (
						'class_id' => $row['class_id'],
						'id' => $row['id']
					);
			}
		}
		// read static entries
		$array = $this -> db -> select (
			"SELECT `entries`.`id`,
					`entries`.`class_id`,
					`entries_static`.`from`,
					`entries_static`.`to`
				FROM `entries`
				LEFT JOIN `entries_static` ON `entries_static`.`entry_id`=`entries`.`id`
				WHERE `entries`.`kind`='static'
					AND (`entries`.`object_id`=" . $this -> objectId . " OR `entries`.`object_id`=0)
					AND (YEAR(`entries_static`.`from`)=" . $this -> year . "
						OR YEAR(`entries_static`.`to`)=" . $this -> year . ")"
		);
		if ($array) {
			foreach ($array as $row) {
				$date = strtotime ($row['from']);
				$dateEnd = strtotime ($row['to']);
				// optional special class for first and last day
				if ($this -> prefs['classFirstAndLastDay']) {
					$firstAndLastDay = array ($date, $dateEnd);
					$date = strtotime ('+1 day', $date);
					$dateEnd = strtotime ('-1 day', $dateEnd);
					foreach ($firstAndLastDay as $dateSpecial) {
						$year = date ('Y', $dateSpecial);
						$month = date ('n', $dateSpecial);
						$day = date ('j', $dateSpecial);
						if ($year == $this -> year) {
							$daysStatic[$month][$day] = array (
								'class_id' => $this -> prefs['classFirstAndLastDay'],
								'id' => $row['id']
							);
						}
					}
				}
				while ($date <= $dateEnd) {
					$year = date ('Y', $date);
					$month = date ('n', $date);
					$day = date ('j', $date);
					if ($year == $this -> year) {
						$daysStatic[$month][$day] = array (
							'class_id' => $row['class_id'],
							'id' => $row['id']
						);
					}
					$date = strtotime ('+1 day', $date);
				}
			}
		}
		// static entries: language specific descriptions (only added if available)
		$array = $this -> db -> select (
			"SELECT `entry_id`,
					`desc`
				FROM `entries_per_language`
				WHERE `language_id`=" . LANG_ID
		);
		if ($array) {
			foreach ($array as $row)
				$entriesDesc[$row['entry_id']] = $row['desc'];
		}
		// fill $dates with valid dates and entries
		for ($m = 1; $m <=12; $m++) { // months
			$dates[$m] = array ();
			if ($m >= $dateStartMonth) {
				for ($d = 1; $d <= 31; $d++) { // days
					if ($d >= $dateStartDay || $m > $dateStartMonth) {
						if (checkdate ($m, $d, $this -> year)) {
							// returning on wdays
							$wday = dateN (mktime (0, 0, 0, $m, $d, $this -> year));
							if (isset ($wdaysReturning[$wday])) {
								$dates[$m][$d]['id'] = $wdaysReturning[$wday]['id'];
								if ($classes[$wdaysReturning[$wday]['class_id']]['color'])
									$dates[$m][$d]['class'] = 'class' . $wdaysReturning[$wday]['class_id'];
								if ($classes[$wdaysReturning[$wday]['class_id']]['content'])
									$dates[$m][$d]['content'] = $classes[$wdaysReturning[$wday]['class_id']]['content'];
								if ($entriesDesc[$wdaysReturning[$wday]['id']])
									$dates[$m][$d]['desc'] = $entriesDesc[$wdaysReturning[$wday]['id']];
							}
							// returning on days or month&days
							if (isset ($daysReturning[$m][$d]))
								$tempM = $m;
							elseif (isset ($daysReturning[0][$d]))
								$tempM = 0;
							if (isset ($tempM)) {
								$dates[$m][$d]['id'] = $daysReturning[$tempM][$d]['id'];
								if ($classes[$daysReturning[$tempM][$d]['class_id']]['color'])
									$dates[$m][$d]['class'] = 'class' . $daysReturning[$tempM][$d]['class_id'];
								if ($classes[$daysReturning[$tempM][$d]['class_id']]['content'])
									$dates[$m][$d]['content'] = $classes[$daysReturning[$tempM][$d]['class_id']]['content'];
								if ($entriesDesc[$daysReturning[$tempM][$d]['id']])
									$dates[$m][$d]['desc'] = $entriesDesc[$daysReturning[$tempM][$d]['id']];
								unset ($tempM);
							}
							// static
							if (isset ($daysStatic[$m][$d])) {
								$dates[$m][$d]['id'] = $daysStatic[$m][$d]['id'];
								if ($classes[$daysStatic[$m][$d]['class_id']]['color'])
									$dates[$m][$d]['class'] = 'class' . $daysStatic[$m][$d]['class_id'];
								if ($classes[$daysStatic[$m][$d]['class_id']]['content'])
									$dates[$m][$d]['content'] = $classes[$daysStatic[$m][$d]['class_id']]['content'];
								if (isset ($entriesDesc[$daysStatic[$m][$d]['id']]))
									$dates[$m][$d]['desc'] = $entriesDesc[$daysStatic[$m][$d]['id']];
							}
							// defaults
							if (!isset ($dates[$m][$d]['class']) || !$dates[$m][$d]['class'])
								$dates[$m][$d]['class'] = $defaultClass;
							if (!isset ($dates[$m][$d]['content']) || !$dates[$m][$d]['content'])
								$dates[$m][$d]['content'] = $defaultContent;
							if (!isset ($dates[$m][$d]['desc']) || !$dates[$m][$d]['desc'])
								$dates[$m][$d]['desc'] = '';
						}
					}
				}
			}
		}
		return $dates;
	}
}
