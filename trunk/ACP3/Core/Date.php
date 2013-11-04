<?php

namespace ACP3\Core;

/**
 * Stellt Funktionen zur Datumsformatierung und Ausrichtung an den Zeitzonen bereit
 *
 * @author Tino Goratsch
 */
class Date {

	/**
	 * Langes Datumsformat
	 * 
	 * @var string
	 */
	private $date_format_long = CONFIG_DATE_FORMAT_LONG;

	/**
	 * Kurzes Datumsformat
	 * 
	 * @var string
	 */
	private $date_format_short = CONFIG_DATE_FORMAT_SHORT;

	/**
	 * PHP DateTimeZone-Object
	 *
	 * @var object 
	 */
	private $date_time_zone = null;

	/**
	 * Falls man sich als User authentifiziert hat, eingestellte Zeitzone + Sommerzeiteinstellung holen
	 *
	 */
	function __construct() {
		$info = Registry::get('Auth')->getUserInfo();

		if (!empty($info)) {
			$this->date_format_long = $info['date_format_long'];
			$this->date_format_short = $info['date_format_short'];
			$time_zone = $info['time_zone'];
		} else {
			$time_zone = CONFIG_DATE_TIME_ZONE;
		}
		$this->date_time_zone = new \DateTimeZone($time_zone);
	}

	/**
	 * Gibts ein Array mit den möglichen Datumsformaten aus,
	 * um diese als Dropdownmenü darstellen zu können
	 *
	 * @param string $format
	 * 	Optionaler Parameter für das aktuelle Datumsformat
	 * @return array 
	 */
	public function dateformatDropdown($format = '') {
		$dateformatLang = array(
			Registry::get('Lang')->t('system', 'date_format_short'),
			Registry::get('Lang')->t('system', 'date_format_long')
		);
		return Functions::selectGenerator('dateformat', array('short', 'long'), $dateformatLang, $format);
	}

	/**
	 * Zeigt Dropdown-Menüs für die Veröffentlichungsdauer von Inhalten an
	 *
	 * @param mixed $name
	 * 	Name des jeweiligen Inputfeldes
	 * @param mixed $value
	 * 	Der Zeitstempel des jeweiligen Eintrages
	 * @param string $format
	 * 	Das anzuzeigende Format im Textfeld
	 * @param array $params
	 * 	Dient dem Festlegen von weiteren Parametern
	 * @param integer $range
	 * 	1 = Start- und Enddatum anzeigen
	 * 	2 = Einfaches Inputfeld mitsamt Datepicker anzeigen
	 * @param integer $with_time
	 * @return string
	 */
	public function datepicker($name, $value = '', $format = 'Y-m-d H:i', array $params = array(), $range = 1, $with_time = true, $input_only = false) {
		$datepicker = array(
			'range' => is_array($name) === true && $range === 1 ? 1 : 0,
			'with_time' => (bool) $with_time,
			'length' => $with_time === true ? 16 : 10,
			'input_only' => (bool) $input_only,
			'params' => array(
				'firstDay' => '\'1\'',
				'dateFormat' => '\'yy-mm-dd\'',
				'constrainInput' => 'true',
				'changeMonth' => 'true',
				'changeYear' => 'true',
			)
		);
		if ($with_time === true) {
			$datepicker['params']['timeFormat'] = '\'HH:mm\'';
		}

		// Zusätzliche Datepicker-Parameter hinzufügen
		if (!empty($params) && is_array($params) === true) {
			$datepicker['params'] = array_merge($datepicker['params'], $params);
		}

		// Veröffentlichungszeitraum
		if (is_array($name) === true && $range === 1) {
			if (!empty($_POST[$name[0]]) && !empty($_POST[$name[1]])) {
				$value_start = $_POST[$name[0]];
				$value_end = $_POST[$name[1]];
				$value_start_r = $this->format($_POST[$name[0]], 'r', false);
				$value_end_r = $this->format($_POST[$name[1]], 'r', false);
			} elseif (is_array($value) === true && Validate::date($value[0], $value[1]) === true) {
				$value_start = $this->format($value[0], $format);
				$value_end = $this->format($value[1], $format);
				$value_start_r = $this->format($value[0], 'r');
				$value_end_r = $this->format($value[1], 'r');
			} else {
				$value_start = $this->format('now', $format, false);
				$value_end = $this->format('now', $format, false);
				$value_start_r = $this->format('now', 'r', false);
				$value_end_r = $this->format('now', 'r', false);
			}

			$datepicker['name_start'] = $name[0];
			$datepicker['name_end'] = $name[1];
			$datepicker['value_start'] = $value_start;
			$datepicker['value_start_r'] = $value_start_r;
			$datepicker['value_end'] = $value_end;
			$datepicker['value_end_r'] = $value_end_r;
			// Einfaches Inputfeld mit Datepicker
		} else {
			if (!empty($_POST[$name])) {
				$value = $_POST[$name];
			} elseif (Validate::date($value) === true) {
				$value = $this->format($value, $format);
			} else {
				$value = $this->format('now', $format, false);
			}

			$datepicker['name'] = $name;
			$datepicker['value'] = $value;
		}

		Registry::get('View')->assign('datepicker', $datepicker);

		return Registry::get('View')->fetchTemplate('system/date.tpl');
	}

	/**
	 * Gibt ein formatiertes Datum zurück
	 *
	 * @param string $time
	 * @param string $format
	 * @param integer $toLocal
	 * @param integer $isLocal
	 * @return string
	 */
	public function format($time = 'now', $format = 'long', $toLocal = true, $isLocal = true) {
		// Datum in gewünschter Formatierung ausgeben
		switch ($format) {
			case 'long':
				$format = $this->date_format_long;
				break;
			case 'short':
				$format = $this->date_format_short;
				break;
		}

		$replace = array();
		// Wochentage lokalisieren
		if (strpos($format, 'D') !== false) {
			$replace = $this->localizeDaysAbbr();
		} elseif (strpos($format, 'l') !== false) {
			$replace = $this->localizeDays();
		}

		// Monate lokalisieren
		if (strpos($format, 'M') !== false) {
			$replace = array_merge($replace, $this->localizeMonthsAbbr());
		} elseif (strpos($format, 'F') !== false) {
			$replace = array_merge($replace, $this->localizeMonths());
		}

		if (is_numeric($time)) {
			$time = date('c', $time);
		}

		$dateTime = new \DateTime($time, $this->date_time_zone);
		if ($toLocal === true) {
			if ($isLocal === true) {
				$dateTime->setTimestamp($dateTime->getTimestamp() + $dateTime->getOffset());
			} else {
				$dateTime->setTimestamp($dateTime->getTimestamp() - $dateTime->getOffset());
			}
		}
		return strtr($dateTime->format($format), $replace);
	}

	private function localizeDaysAbbr() {
		return array(
			'Mon' => Registry::get('Lang')->t('system', 'date_mon'),
			'Tue' => Registry::get('Lang')->t('system', 'date_tue'),
			'Wed' => Registry::get('Lang')->t('system', 'date_wed'),
			'Thu' => Registry::get('Lang')->t('system', 'date_thu'),
			'Fri' => Registry::get('Lang')->t('system', 'date_fri'),
			'Sat' => Registry::get('Lang')->t('system', 'date_sat'),
			'Sun' => Registry::get('Lang')->t('system', 'date_sun')
		);
	}

	private function localizeDays() {
		return array(
			'Monday' => Registry::get('Lang')->t('system', 'date_monday'),
			'Tuesday' => Registry::get('Lang')->t('system', 'date_tuesday'),
			'Wednesday' => Registry::get('Lang')->t('system', 'date_wednesday'),
			'Thursday' => Registry::get('Lang')->t('system', 'date_thursday'),
			'Friday' => Registry::get('Lang')->t('system', 'date_friday'),
			'Saturday' => Registry::get('Lang')->t('system', 'date_saturday'),
			'Sunday' => Registry::get('Lang')->t('system', 'date_sunday')
		);
	}

	private function localizeMonths() {
		return array(
			'January' => Registry::get('Lang')->t('system', 'date_january'),
			'February' => Registry::get('Lang')->t('system', 'date_february'),
			'March' => Registry::get('Lang')->t('system', 'date_march'),
			'April' => Registry::get('Lang')->t('system', 'date_april'),
			'May' => Registry::get('Lang')->t('system', 'date_may_full'),
			'June' => Registry::get('Lang')->t('system', 'date_june'),
			'July' => Registry::get('Lang')->t('system', 'date_july'),
			'August' => Registry::get('Lang')->t('system', 'date_august'),
			'September' => Registry::get('Lang')->t('system', 'date_september'),
			'October' => Registry::get('Lang')->t('system', 'date_october'),
			'November' => Registry::get('Lang')->t('system', 'date_november'),
			'December' => Registry::get('Lang')->t('system', 'date_december')
		);
	}

	private function localizeMonthsAbbr() {
		return array(
			'Jan' => Registry::get('Lang')->t('system', 'date_jan'),
			'Feb' => Registry::get('Lang')->t('system', 'date_feb'),
			'Mar' => Registry::get('Lang')->t('system', 'date_mar'),
			'Apr' => Registry::get('Lang')->t('system', 'date_apr'),
			'May' => Registry::get('Lang')->t('system', 'date_may_abbr'),
			'Jun' => Registry::get('Lang')->t('system', 'date_jun'),
			'Jul' => Registry::get('Lang')->t('system', 'date_jul'),
			'Aug' => Registry::get('Lang')->t('system', 'date_aug'),
			'Sep' => Registry::get('Lang')->t('system', 'date_sep'),
			'Oct' => Registry::get('Lang')->t('system', 'date_oct'),
			'Nov' => Registry::get('Lang')->t('system', 'date_nov'),
			'Dec' => Registry::get('Lang')->t('system', 'date_dec')
		);
	}

	/**
	 * Liefert ein Array mit allen Zeitzonen dieser Welt aus
	 *
	 * @param string $current_value
	 * @return array
	 */
	public static function getTimeZones($current_value = '') {
		$timeZones = array(
			'Africa' => \DateTimeZone::listIdentifiers(\DateTimeZone::AFRICA),
			'America' => \DateTimeZone::listIdentifiers(\DateTimeZone::AMERICA),
			'Antarctica' => \DateTimeZone::listIdentifiers(\DateTimeZone::ANTARCTICA),
			'Arctic' => \DateTimeZone::listIdentifiers(\DateTimeZone::ARCTIC),
			'Asia' => \DateTimeZone::listIdentifiers(\DateTimeZone::ASIA),
			'Atlantic' => \DateTimeZone::listIdentifiers(\DateTimeZone::ATLANTIC),
			'Australia' => \DateTimeZone::listIdentifiers(\DateTimeZone::AUSTRALIA),
			'Europe' => \DateTimeZone::listIdentifiers(\DateTimeZone::EUROPE),
			'Indian' => \DateTimeZone::listIdentifiers(\DateTimeZone::INDIAN),
			'Pacitic' => \DateTimeZone::listIdentifiers(\DateTimeZone::PACIFIC),
			'UTC' => \DateTimeZone::listIdentifiers(\DateTimeZone::UTC),
		);

		foreach ($timeZones as $key => $values) {
			$i = 0;
			foreach ($values as $row) {
				unset($timeZones[$key][$i]);
				$timeZones[$key][$row]['selected'] = Functions::selectEntry('date_time_zone', $row, $current_value);
				++$i;
			}
		}
		return $timeZones;
	}

	/**
	 * Gibt die Formularfelder für den Veröffentlichungszeitraum aus
	 *
	 * @param string $start
	 * @param string $end
	 * @param string $format
	 * @return string
	 */
	public function formatTimeRange($start, $end = '', $format = 'long') {
		$datetime_format = 'Y-m-d H:i';
		if ($end === '' || $start >= $end) {
			$title = $end === '' ? $this->format($start, $format) : sprintf(Registry::get('Lang')->t('system', 'date_published_since'), $this->format($start, $format));
			return '<time datetime="' . $start . '" title="' . $title . '">' . $this->format($start, $datetime_format) . '</time>';
		} else {
			$title = sprintf(Registry::get('Lang')->t('system', 'date_time_range'), $this->format($start, $format), $this->format($end, $format));
			return '<time datetime="' . $start . '/' . $end . '" title="' . $title . '">' . $this->format($start, $datetime_format) . '&ndash;' . $this->format($end, $datetime_format) . '</time>';
		}
	}

	/**
	 * Gibt einen einfachen Zeitstempel zurück, welcher sich an UTC ausrichtet
	 *
	 * @param string $value
	 * @return integer
	 */
	public function timestamp($value = 'now', $is_local = false) {
		return $this->format($value, 'U', true, $is_local);
	}

	/**
	 * Gibt die aktuelle Uhrzeit im MySQL-Datetime Format zurück
	 * 
	 * @return string
	 */
	public function getCurrentDateTime($is_local = false) {
		return $this->format('now', 'Y-m-d H:i:s', true, $is_local);
	}

	/**
	 * Gibt einen an UTC ausgerichteten Zeitstempelim MySQL DateTime Format zurück
	 *
	 * @param string $value
	 * @return string
	 */
	public function toSQL($value = '') {
		return $this->format(empty($value) === true ? $this->getCurrentDateTime() : $value, 'Y-m-d H:i:s', true, false);
	}

	/**
	 * Konvertiert einen Unixstamp in das MySQL-Datetime Format
	 * 
	 * @param integer $value
	 * @return string
	 */
	public function timestampToDateTime($value, $is_local = false) {
		return $this->format($value, 'Y-m-d H:i:s', true, $is_local);
	}

}
