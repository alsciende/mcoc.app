<?php

namespace App\Exception;

class LeaderCannotLeaveException extends AllianceManagementException
{
    public string $memberId;

    /**
     * LeaderCannotLeaveException constructor.
     */
    public function __construct(string $memberId)
    {
        $this->memberId = $memberId;
        parent::__construct("Trying to leave alliance as leader ; must promote another leader first");
    }
}