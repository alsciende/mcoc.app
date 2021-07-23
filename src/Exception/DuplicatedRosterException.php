<?php

namespace App\Exception;

class DuplicatedRosterException extends \RuntimeException
{

    /**
     * DuplicatedRosterException constructor.
     */
    public function __construct()
    {
        parent::__construct("Trying to add a champion to roster but champion is already part of roster");
    }
}