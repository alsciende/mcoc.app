<?php

namespace App\Exception;

class TooManyAlliancesException extends AllianceManagementException
{
    public string $playerId;

    /**
     * TooManyAlliancesException constructor.
     */
    public function __construct(string $playerId)
    {
        $this->playerId = $playerId;
        parent::__construct("Trying to create an alliance but is already member of one");
    }
}