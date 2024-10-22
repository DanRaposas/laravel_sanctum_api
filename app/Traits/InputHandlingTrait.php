<?php declare(strict_types = 1);

namespace App\Traits;

trait InputHandlingTrait {
    // Methods
    /**
     * Method for sanitizing inputs
    */
    protected function sanitizeString(array $data): array
    {
        $values = [];

        foreach($data as $key => $value) {
            $values[$key] = trim(filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        }

        return $values;
    }

    /**
     * Method for sanitizing emails
    */
    protected function sanitizeEmail(array $data): array
    {
        $values = [];

        foreach($data as $key => $value) {
            $values[$key] = trim(filter_var($value, FILTER_SANITIZE_EMAIL));
        }

        return $values;
    }
}