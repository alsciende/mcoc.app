<?php

namespace App\Exception;

class CannotPromoteLeaderException extends AllianceManagementException
{
    public string $memberId;

    /**
     * CannotPromoteLeaderException constructor.
     */
    public function __construct(string $memberId)
    {
        $this->memberId = $memberId;
        parent::__construct("Trying to promote the current leader as new leader of Alliance");
    }
}