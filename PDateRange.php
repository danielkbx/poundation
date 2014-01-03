<?php

namespace Poundation;

class PDateRange {

    /**
     * @var \DateTime
     */
    private $reference;

    /**
     * @var \DateInterval
     */
    private $interval;

    public function __construct(\DateTime $startDate, \DateTime $endDate) {

        $this->reference = $startDate;
        $this->interval = $this->reference->diff($endDate);

    }

    /**
     * Creates a date range from the start date to the end date.
     * @param $startDate
     * @param $endDate
     * @return null|PDateRange
     */
    static function createRange($startDate, $endDate) {

        $range = null;

        $doStartDate = null;
        if ($startDate instanceof \DateTime) {
            $doStartDate = $startDate;
        } else if ($startDate instanceof PDate) {
            $doStartDate = $startDate->getDateTime();
        }

        $doEndDate = null;
        if ($endDate instanceof \DateTime) {
            $doEndDate = $endDate;
        } else if ($endDate instanceof PDate) {
            $doEndDate = $endDate->getDateTime();
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
    public function getStartDate() {
        return clone $this->reference;
    }

    /**
     * Returns the end date.
     * @return \DateTime
     */
    public function getEndDate() {
        return $this->getStartDate()->add($this->interval);
    }

    /**
     * Returns the duration of the interval.
     * @return \DateInterval
     */
    public function getDuration() {
        return $this->interval;
    }

    /**
     * Returns true if the given date is between the start and the enddate.
     * @param $date
     * @return bool
     * @throws \Exception
     */
    public function isDateInRange($date) {

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
