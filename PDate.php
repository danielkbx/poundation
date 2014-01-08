<?php

namespace Poundation;

class PDate extends PObject implements \JsonSerializable
{

	private $data;

	public function __construct($value = null)
	{

		$dateValue = null;

		if (is_integer($value)) {
			$dateValue = new \DateTime();
			$dateValue->setTimestamp($value);
		} else if ($value instanceof \DateTime) {
			$dateValue = clone $value;
		} else if (is_string($value)) {
			$dateValue = new \DateTime($value);
		} else if ($value instanceof PDate) {
            $dateValue = $value->getDateTime();
        } else {
			$dateValue = new \DateTime('now');
		}

		if ($dateValue == null) {
			throw new \Exception('Cannot create dat object with this value "' + $value + '".');
		} else {
			$this->data = $dateValue;
		}
	}

	/**
	 * Returns a new PDate object set to now.
	 *
	 * @return PDate
	 */
	static function now()
	{
		return new self();
	}

	/**
	 * Returns a new PDate object set to midnight today.
	 *
	 * @return PDate
	 */
	static function today()
	{
		return self::now()->setToMidnight();
	}

	/**
	 * Creates a new date.
	 *
	 * @param null $value
	 *
	 * @return PDate|null
	 */
	static function createDate($value = null)
	{
		$date = null;

		if ($value == null) {
			$date = self::now();
		} else {
			$date = new self($value);
		}

		return $date;
	}

	public function __clone()
	{

		$isoValue   = $this->getInISO8601Format();
		$newValue   = new \DateTime($isoValue);
		$this->data = $newValue;

	}

	public function __toString()
	{
		return $this->getInISO8601Format();
	}

	public function jsonSerialize()
	{
		return $this->getInISO8601Format();
	}

	/**
	 * Returns the seconds.
	 *
	 * @return string
	 */
	public function seconds()
	{
		return (int)$this->data->format('s');
	}

	/**
	 * Returns the minutes.
	 *
	 * @return string
	 */
	public function minutes()
	{
		return (int)$this->data->format('i');
	}

	/**
	 * Returns the hours.
	 *
	 * @return string
	 */
	public function hours()
	{
		return (int)$this->data->format('H');
	}


	/**
	 * Returns the days of the month.
	 *
	 * @return string
	 */
	public function day()
	{
		return (int)$this->data->format('d');
	}

	/**
	 * Returns the month of the year.
	 *
	 * @return string
	 */
	public function month()
	{
		return (int)$this->data->format('m');
	}

	/**
	 * Returns the year.
	 *
	 * @return string
	 */
	public function year()
	{
		return (int)$this->data->format('Y');
	}


	/**
	 * Adds the specified amount of time items to the date. You can mix negative and positive values.
	 *
	 * @param int $seconds
	 * @param int $minutes
	 * @param int $hours
	 * @param int $days
	 * @param int $months
	 * @param int $years
	 *
	 * @return PDate
	 */
	public function add($seconds = 0, $minutes = 0, $hours = 0, $days = 0, $months = 0, $years = 0)
	{

		if ($years != 0) {
			$this->addYears($years);
		}
		if ($months != 0) {
			$this->addMonths($months);
		}
		if ($days != 0) {
			$this->addDays($days);
		}
		if ($hours != 0) {
			$this->addHours($hours);
		}
		if ($minutes != 0) {
			$this->addMinutes($minutes);
		}
		if ($seconds != 0) {
			$this->addSeconds($seconds);
		}

		return $this;

	}

	/**
	 * Adds the given number of seconds to the date.
	 *
	 * @param $seconds
	 *
	 * @return PDate
	 */
	public function addSeconds($seconds)
	{
		$interval = new \DateInterval('PT' . abs($seconds) . 'S');
		if ($seconds > 0) {
			$this->data->add($interval);
		} else {
			$this->data->sub($interval);
		}

		return $this;
	}

	/**
	 * Adds the given number of minutes to the date.
	 *
	 * @param $minutes
	 *
	 * @return PDate
	 */
	public function addMinutes($minutes)
	{
		return $this->addSeconds($minutes * 60);
	}

	/**
	 * Adds the given number of hours to the date.
	 *
	 * @param $hours
	 *
	 * @return PDate
	 */
	public function addHours($hours)
	{
		return $this->addMinutes($hours * 60);
	}

	/**
	 * Adds the given number of days to the date.
	 *
	 * @param $days
	 *
	 * @return PDate
	 */
	public function addDays($days)
	{
		$interval = new \DateInterval('P' . abs($days) . 'D');
		if ($days > 0) {
			$this->data->add($interval);
		} else {
			$this->data->sub($interval);
		}

		return $this;
	}

	/**
	 * Adds the given number of weeks to the date.
	 *
	 * @param $weeks
	 *
	 * @return PDate
	 */
	public function addWeeks($weeks)
	{
		$interval = new \DateInterval('P' . abs($weeks) . 'W');
		if ($weeks > 0) {
			$this->data->add($interval);
		} else {
			$this->data->sub($interval);
		}

		return $this;
	}

	/**
	 * Adds the given number of months to the date.
	 *
	 * @param $months
	 *
	 * @return PDate
	 */
	public function addMonths($months)
	{
		$interval = new \DateInterval('P' + abs($months) + 'M');
		if ($months > 0) {
			$this->data->add($interval);
		} else {
			$this->data->sub($interval);
		}

		return $this;
	}

	/**
	 * Adds the given number of years to the date.
	 *
	 * @param $years
	 *
	 * @return PDate
	 */
	public function addYears($years)
	{
		$interval = new \DateInterval('P' + abs($years) + 'Y');
		if ($years > 0) {
			$this->data->add($interval);
		} else {
			$this->data->sub($interval);
		}

		return $this;
	}

	/**
	 * Sets the date to midnight.
	 *
	 * @return PDate
	 */
	public function setToMidnight()
	{
		$hours   = $this->hours();
		$minutes = $this->minutes();
		$seconds = $this->seconds();

		return $this->add($seconds * -1, $minutes * -1, $hours * -1);
	}


	/**
	 * Returns the native DateTime value.
	 *
	 * @return \DateTime
	 */
	public function getDateTime()
	{
		return $this->data;
	}

	/**
	 * Adjust the date to the timezone with the given name.
	 *
	 * @param $timezoneString
	 *
	 * @return bool
	 */
	public function adjustTimezone($timezoneString)
	{

		$timezone = new \DateTimeZone($timezoneString);
		if ($timezone) {
			$this->data->setTimezone($timezone);

			return true;
		}

		return false;
	}

	/**
	 * Returns a string formated in the native Doctrine way.
	 *
	 * @return string|null
	 */
	public function getInDoctrineFormat($includeTime = false)
	{
		$format = 'Y-m-d';
		if ($includeTime) {
			$format .= ' H:i:s.u';
		}

		return ($this->data) ? $this->getDateTime()->format($format) : null;
	}

	/**
	 * Returns a ISO8601 formated string.
	 *
	 * @return null|string
	 */
	public function getInISO8601Format()
	{
		return ($this->data) ? $this->getDateTime()->format(\DateTime::ISO8601) : null;
	}

	/**
	 * Returns a formated string. See date command for valid format strings.
	 *
	 * @param $format
	 *
	 * @return string
	 */
	public function getFormatedString($format)
	{
		return $this->data->format($format);
	}


	public function isEqualToDate(PDate $otherDate) {

		if (parent::isEqual($otherDate)) {
			return true;
		}

		return ($this->data == $otherDate->data);

	}

	/**
	 * Returns true if this date is earlier than the other date.
	 *
	 * @param PDate $otherDate
	 *
	 * @return bool
	 */
	public function isBefore(PDate $otherDate)
	{
		return ($this->data < $otherDate->data);
	}

	/**
	 * Returns true if the date is already in the past.
	 * @return bool
	 */
	public function isPast() {
		return $this->isBefore(self::now());
	}

	/**
	 * Returns true if the date is later that the other date.
	 *
	 * @param PDate $otherDate
	 *
	 * @return bool
	 */
	public function isAfter(PDate $otherDate)
	{
		return ($this->data > $otherDate->data);
	}

	/**
	 * Returns true if the date is yet in the future.
	 * @return bool
	 */
	public function isFuture()
	{
		 return $this->isAfter(self::now());
	}

	/**
	 * Returns true is the date is between last midnight and next midnight.
	 * @return bool
	 */
	public function isToday() {

		$lastMidnight = PDate::today();
		$nextMidnight = PDate::today()->addDays(1);

		if ($this->isAfter($lastMidnight) || $this->isEqualToDate($lastMidnight)) {
			if ($this->isBefore($nextMidnight)) {
				return true;
			}
		}

		return false;
	}

    /**
     * Returns the balanced time range (which is a date range from now to this date).
     * @return PDateRange
     */
    public function getBalancedTimeRange() {

        $now = PDate::now();
        $range = PDateRange::createRange($now, $this);

        return $range;
    }

}
