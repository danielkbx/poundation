<?php

namespace Poundation;

class PDateRange
{

    const DURATION_YEARS = 'y';
    const DURATION_MONTHS = 'm';
    const DURATION_DAYS = 'd';
    const DURATION_HOURS = 'h';
    const DURATION_MINUTES = 'i';
    const DURATION_SECONDS = 's';


    /**
     * @var \DateTime
     */
    private $reference;

    /**
     * @var \DateInterval
     */
    private $interval;


    public function __construct(\DateTime $startDate, \DateTime $endDate)
    {
        $format = 'Y-m-d H:i:s';
        $timezone = new \DateTimeZone('UTC');
        $calcStartDate = new \DateTime($startDate->format($format), $timezone);
        $calcEndDate = new \DateTime($endDate->format($format), $timezone);
        $calcDiff = $calcStartDate->diff($calcEndDate);

        $this->reference = clone($startDate);
        $this->interval = $calcDiff;

    }

    /**
     * Creates a date range from the earlier start date to the later date.
     * @param $startDate
     * @param $endDate
     * @return null|PDateRange
     */
    static function createRange($date1, $date2)
    {

        $range = null;

        $doStartDate = null;
        if ($date1 instanceof \DateTime) {
            $doStartDate = $date1;
        } else {
            if ($date1 instanceof PDate) {
                $doStartDate = $date1->getDateTime();
            }
        }

        $doEndDate = null;
        if ($date2 instanceof \DateTime) {
            $doEndDate = $date2;
        } else {
            if ($date2 instanceof PDate) {
                $doEndDate = $date2->getDateTime();
            }
        }

        if (!is_null($doStartDate) && !is_null($doEndDate)) {

            if ($doStartDate->getTimestamp() > $doEndDate->getTimestamp()) {
                // we need to swap them
                $parking = $doStartDate;
                $doStartDate = $doEndDate;
                $doEndDate = $parking;
                unset($parking);
            }

            $range = new self($doStartDate, $doEndDate);
        }

        return $range;
    }

    /**
     * Returns the start date.
     * @return \DateTime
     */
    public function getStartDate()
    {
        return clone $this->reference;
    }

    /**
     * Returns the end date.
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->getStartDate()->add($this->interval);
    }

    /**
     * Returns the duration of the interval.
     * @return \DateInterval
     */
    public function getDuration()
    {
        return $this->interval;
    }

    public function getDurationComponents()
    {

        $duration = $this->getDuration();

        $components = array(
            self::DURATION_YEARS => (int)$duration->format('%' . self::DURATION_YEARS),
            self::DURATION_MONTHS => (int)$duration->format('%' . self::DURATION_MONTHS),
            self::DURATION_DAYS => (int)$duration->format('%' . self::DURATION_DAYS),
            self::DURATION_HOURS => (int)$duration->format('%' . self::DURATION_HOURS),
            self::DURATION_MINUTES => (int)$duration->format('%' . self::DURATION_MINUTES),
            self::DURATION_SECONDS => (int)$duration->format('%' . self::DURATION_SECONDS)
        );

        return $components;
    }

    /**
     * Returns true if the given date is between the start and the enddate.
     * @param $date
     * @return bool
     * @throws \Exception
     */
    public function isDateInRange($date)
    {

        $result = false;

        if ($date instanceof PDate) {
            $date = $date->getDateTime();
        }

        if ($date instanceof \DateTime) {

            $result = ($this->getStartDate() <= $date && $date <= $this->getEndDate());

        } else {
            throw new \Exception('Cannot change date range with given type. Pass a PDate or a DateTime object!');
        }

        return $result;
    }


}
