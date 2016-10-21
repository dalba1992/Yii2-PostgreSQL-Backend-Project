<?php

namespace app\components;

use Yii;
use yii\base\Component;

class DateHelper extends Component
{
    /**
     * Multi-purpose function to calculate the time elapsed between $start and optional $end
     * @param string|null $start the date string to start calculation
     * @param string|null $end the date string to end calculation
     * @param string $suffix the suffix string to include in the calculated string
     * @param string $format the format of the resulting date if limit is reached or no periods were found
     * @param string $separator the separator between periods to use when filter is not true
     * @param null|string $limit date string to stop calculations on and display the date if reached - ex: 1 month
     * @param bool|array $filter false to display all periods, true to display first period matching the minimum, or array of periods to display ['year', 'month']
     * @param int $minimum the minimum value needed to include a period
     * @return string
     */
    public function elapsedTimeString($start, $end = null, $limit = null, $filter = true, $suffix = 'ago', $format = 'Y-m-d', $separator = ' ', $minimum = 1)
    {
        $dates = (object) array(
            'start' => new \DateTime($start ? : 'now'),
            'end' => new \DateTime($end ? : 'now'),
            'intervals' => array('y' => 'year', 'm' => 'month', 'd' => 'day', 'h' => 'hour', 'i' => 'minute', 's' => 'second'),
            'periods' => array()
        );
        $elapsed = (object) array(
            'interval' => $dates->start->diff($dates->end),
            'unknown' => 'unknown'
        );
        if ($elapsed->interval->invert === 1) {
            return trim('0 seconds ' . $suffix);
        }
        if (false === empty($limit)) {
            $dates->limit = new \DateTime($limit);
            if (date_create()->add($elapsed->interval) > $dates->limit) {
                return $dates->start->format($format) ? : $elapsed->unknown;
            }
        }
        if (true === is_array($filter)) {
            $dates->intervals = array_intersect($dates->intervals, $filter);
            $filter = false;
        }
        foreach ($dates->intervals as $period => $name) {
            $value = $elapsed->interval->$period;
            if ($value >= $minimum) {
                $dates->periods[] = vsprintf('%1$s %2$s%3$s', array($value, $name, ($value !== 1 ? 's' : '')));
                if (true === $filter) {
                    break;
                }
            }
        }
        if (false === empty($dates->periods)) {
            return trim(vsprintf('%1$s %2$s', array(implode($separator, $dates->periods), $suffix)));
        }

        return $dates->start->format($format) ? : $elapsed->unknown;
    }
}