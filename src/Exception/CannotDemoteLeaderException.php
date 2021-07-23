<?php

namespace App\Exception;

class CannotDemoteLeaderException extends AllianceManagementException
{
    public string $memberId;

    /**
     * CannotDemoteLeaderException constructor.
     */
    public function __construct(string $memberId)
    {
        $this->memberId = $memberId;
        parent::__construct("Trying to demote leader to simple member status ; must promote another leader first");
    }
}