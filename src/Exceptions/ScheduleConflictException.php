<?php
namespace App\Exceptions;

use Exception;

class ScheduleConflictException extends Exception {
    protected $code = 409; // HTTP status Conflict

    public function __construct($message = "Schedule conflict detected.", $code = 409, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}