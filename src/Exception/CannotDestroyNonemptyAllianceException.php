<?php

namespace App\Exception;

class CannotDestroyNonemptyAllianceException extends AllianceManagementException
{
    public string $allianceId;

    /**
     * CannotDestroyNonemptyAllianceException constructor.
     */
    public function __construct(string $allianceId)
    {
        $this->allianceId = $allianceId;
        parent::__construct("Trying to destroy an alliance which still has more than one member");
    }
}