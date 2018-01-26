<?php

namespace App\Exceptions;

use Carbon\Carbon;
use Exception;
use Throwable;

class PeriodException extends Exception
{
    /** @var Carbon */
    protected $from;

    /** @var Carbon */
    protected $to;

    /**
     * @param string $message
     * @param Carbon $from
     * @param Carbon $to
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message, Carbon $from, Carbon $to = null, int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return Carbon
     */
    public function getFrom() : Carbon
    {
        return $this->from;
    }

    /**
     * @param Carbon $from
     */
    public function setFrom(Carbon $from)
    {
        $this->from = $from;
    }

    /**
     * @return Carbon
     */
    public function getTo() : Carbon
    {
        return $this->to;
    }

    /**
     * @param Carbon $to
     */
    public function setTo(Carbon $to)
    {
        $this->to = $to;
    }
}
