<?php

namespace BusinessDays;

class Calculator
{
    const MONDAY    = 1;
    const TUESDAY   = 2;
    const WEDNESDAY = 3;
    const THURSDAY  = 4;
    const FRIDAY    = 5;
    const SATURDAY  = 6;
    const SUNDAY    = 7;

    const WEEK_DAY_FORMAT = 'N';
    const HOLIDAY_FORMAT  = 'm-d';
    const FREE_DAY_FORMAT = 'Y-m-d';

    private ?\DateTime $date = null;

    /** @var \DateTime[] */
    private array $holidays = [];

    /** @var \DateTime[] */
    private array $freeDays = [];

    /** @var int[] */
    private array $freeWeekDays = [];

    public function setStartDate(\DateTime $startDate): static
    {
        $this->date = clone $startDate;

        return $this;
    }

    /** @param \DateTime[] $holidays */
    public function setHolidays(array $holidays): static
    {
        $this->holidays = $holidays;

        return $this;
    }

    /** @return \DateTime[] */
    private function getHolidays(): array
    {
        return $this->holidays;
    }

    /** @param \DateTime[] $freeDays */
    public function setFreeDays(array $freeDays): static
    {
        $this->freeDays = $freeDays;

        return $this;
    }

    /** @return \DateTime[] */
    private function getFreeDays(): array
    {
        return $this->freeDays;
    }

    /** @param int[] $freeWeekDays */
    public function setFreeWeekDays(array $freeWeekDays): static
    {
        $this->freeWeekDays = $freeWeekDays;

        return $this;
    }

    /** @return int[] */
    private function getFreeWeekDays(): array
    {
        if (count($this->freeWeekDays) >= 7) {
            throw new \InvalidArgumentException('Too many non business days provided');
        }

        return $this->freeWeekDays;
    }

    public function addBusinessDays(int $howManyDays): static
    {
        $iterator = 0;
        while ($iterator < $howManyDays) {
            $this->getDate()->modify('+1 day');
            if ($this->isBusinessDay($this->getDate())) {
                $iterator++;
            }
        }

        return $this;
    }

    public function getDate(): \DateTime
    {
        if ($this->date === null) {
            $this->date = new \DateTime();
        }

        return $this->date;
    }

    public function isBusinessDay(\DateTime $date): bool
    {
        if ($this->isFreeWeekDayDay($date)) {
            return false;
        }

        if ($this->isHoliday($date)) {
            return false;
        }

        if ($this->isFreeDay($date)) {
            return false;
        }

        return true;
    }

    public function isFreeWeekDayDay(\DateTime $date): bool
    {
        $currentWeekDay = (int) $date->format(self::WEEK_DAY_FORMAT);

        return in_array($currentWeekDay, $this->getFreeWeekDays(), strict: true);
    }

    public function isHoliday(\DateTime $date): bool
    {
        $holidayFormatValue = $date->format(self::HOLIDAY_FORMAT);
        foreach ($this->getHolidays() as $holiday) {
            if ($holidayFormatValue === $holiday->format(self::HOLIDAY_FORMAT)) {
                return true;
            }
        }

        return false;
    }

    public function isFreeDay(\DateTime $date): bool
    {
        $freeDayFormatValue = $date->format(self::FREE_DAY_FORMAT);
        foreach ($this->getFreeDays() as $freeDay) {
            if ($freeDayFormatValue === $freeDay->format(self::FREE_DAY_FORMAT)) {
                return true;
            }
        }

        return false;
    }
}
