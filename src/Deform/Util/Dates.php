<?php
namespace Deform\Util;

class Dates
{
    const TIMEZONE_LONDON = "Europe/London";

    public static $defaultTimezone = self::TIMEZONE_LONDON;

    const UK_DATE_FORMAT = "d/m/Y";
    const UK_DATE_HOURS_MINS_FORMAT = "d/m/Y H:i";
    const UK_DATE_HOURS_MINS_SECS_FORMAT = "d/m/Y H:i:s";
    const MSSQL_DATETIME_STRING_FORMAT = 'Y-m-d H:i:s.u?';

    /**
     * strtotime with support for uk style d/m/y dates rather than stupid US default m/d/y
     *
     * @param string $date
     *
     * @return int
     */
    public static function strtotimeUk(string $date): int
    {
        if(preg_match("%^\\d{1,2}/\\d{1,2}/\\d{2,4}(.*)$%", $date)) {
            if(strpos($date, " ")) {
                list($date, $time) = explode(" ", $date);
            }
            list($day, $month, $year) = explode("/", $date);
            $date = $year . "-" . $month . "-" . $day . (isset($time) ? " " . $time : " +0000");
        }
        return strtotime($date, self::getTimezone());
    }

    /**
     * ensure that the thing string is a \Datetime if possible
     *
     * @param mixed $thing
     *
     * @return \DateTime|null
     */
    public static function dateTimeUk($thing): ?\DateTime
    {
        if($thing instanceof \DateTime) {
            return $thing;
        }
        $ts = self::strtotimeUk($thing);
        return $ts ? \DateTime::createFromFormat('U', $ts) : null;
    }

    /**
     * generate a period of time as a human-readable string from a \DateTime object
     *
     * @param \DateTimeInterface $datetime
     * @param int $significance
     * @param string $separator
     * @param \DateTimeInterface|null $targetDate Use this to get the date diff between 2 date objects otherwise uses now.
     *
     * @return string
     */
    public static function readableDateDiff(\DateTimeInterface $datetime,int $significance = 1,string $separator = ', ', \DateTimeInterface $targetDate = null): string
    {
        if(!$targetDate instanceof \DateTimeInterface) {
            $targetDate = new \DateTime();
        }
        $interval = $targetDate->diff($datetime);

        $doPlural = function ($nb, $str) { return $nb > 1 ? $str . 's' : $str; }; // adds plurals

        $format = [];
        if($interval->y !== 0) {
            $format[] = "%y " . $doPlural($interval->y, "year");
        }
        if($interval->m !== 0) {
            $format[] = "%m " . $doPlural($interval->m, "month");
        }
        if($interval->d !== 0) {
            $format[] = "%d " . $doPlural($interval->d, "day");
        }
        if($interval->h !== 0) {
            $format[] = "%h " . $doPlural($interval->h, "hour");
        }
        if($interval->i !== 0) {
            $format[] = "%i " . $doPlural($interval->i, "minute");
        }

        if(!count($format)) {
            return "less than a minute";
        } else {
            $format[] = "%s " . $doPlural($interval->s, "second");
        }

        $actual_significance = count($format);
        if($actual_significance < $significance){
            $significance = $actual_significance;
        }

        $format_parts = [];
        for($i = 1; $i <= $significance; $i++) {
            $format_parts[] = array_shift($format);
        }

        return $interval->format(implode($separator, $format_parts));
    }

    public static function getTimezone() : \DateTimeZone
    {
        static $timezone = null;
        if ($timezone===null) {
            $timezone = new \DateTimeZone(self::$defaultTimezone);
        }
        return $timezone;
    }

}

