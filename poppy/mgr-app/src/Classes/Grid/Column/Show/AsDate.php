<?php

namespace Poppy\MgrApp\Classes\Grid\Column\Show;

use Carbon\Carbon;

trait AsDate
{

    /**
     * Returns a string formatted according to the given format string.
     *
     * @param string $format
     *
     * @return $this
     */
    public function asDate(string $format): self
    {
        return $this->display(function ($value) use ($format) {
            return date($format, strtotime($value));
        });
    }


    /**
     * Return a human readable format time.
     *
     * @param null $locale
     *
     * @return $this
     */
    public function asDiffForHumans($locale = null): self
    {
        if ($locale) {
            Carbon::setLocale($locale);
        }

        return $this->display(function ($value) {
            return Carbon::parse($value)->diffForHumans();
        });
    }

    /**
     * @param string $format
     * @return $this
     * @deprecated 4.0-dev
     */
    public function date(string $format): self
    {
        return $this->asDate($format);
    }


    /**
     * @param null $locale
     * @return $this
     * @deprecated 4.0-dev
     */
    public function diffForHumans($locale = null): self
    {
        $this->asDiffForHumans($locale);
    }

}
