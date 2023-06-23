<?php

namespace App\Support\Exceptions;

use App\Support\Contracts\AbstractQueryConfiguration;
use Exception;

class DashboardConfigurationNotResolved extends Exception
{
    public function __construct(AbstractQueryConfiguration $queryConfiguration)
    {
        $queryConfigurationClass = get_class($queryConfiguration);

        $message = "Dashboard query configuration {$queryConfigurationClass} not resolved.";

        parent::__construct($message);
    }
}
