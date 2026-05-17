<?php

namespace App\Exceptions;

use RuntimeException;

class SeatUnavailableException extends RuntimeException
{
    /** @var array<int, string> */
    private array $unavailableSeats;

    /**
     * @param array<int, string> $unavailableSeats
     */
    public function __construct(array $unavailableSeats)
    {
        parent::__construct('Uno o mas asientos ya no estan disponibles');
        $this->unavailableSeats = array_values(array_unique($unavailableSeats));
    }

    /**
     * @return array<int, string>
     */
    public function unavailableSeats(): array
    {
        return $this->unavailableSeats;
    }
}
