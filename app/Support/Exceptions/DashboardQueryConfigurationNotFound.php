<?php

namespace App\Support\Exceptions;

use Exception;

class DashboardQueryConfigurationNotFound extends Exception
{
    public function __construct($key)
    {
        $message = "Dashboard Query Configuration with $key not found.";

        parent::__construct($message);
    }
}
