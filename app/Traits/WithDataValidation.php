<?php

namespace App\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait WithDataValidation
{
    /**
     * Validate user input data.
     *
     * @param array $data
     * @param array $rules
     * @return array
     * @throws ValidationException
     */
    protected function validateInput(array $data, array $rules)
    {
        $validator = Validator::make($data, $rules);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        return $validator->validated();
    }

    /**
     * Sanitize user input.
     *
     * @param array $data
     * @param array $fields
     * @return array
     */
    protected function sanitizeInput(array $data, array $fields = [])
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (empty($fields) || in_array($key, $fields)) {
                if (is_string($value)) {
                    $sanitized[$key] = htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
                } else {
                    $sanitized[$key] = $value;
                }
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }

    /**
     * Validate email format.
     *
     * @param string $email
     * @return bool
     */
    protected function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate phone number format.
     *
     * @param string $phone
     * @return bool
     */
    protected function isValidPhone(string $phone): bool
    {
        return preg_match('/^[0-9\+\-\(\)\s]{10,20}$/', $phone) === 1;
    }

    /**
     * Validate URL format.
     *
     * @param string $url
     * @return bool
     */
    protected function isValidUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Check if data is duplicate in database.
     *
     * @param string $table
     * @param string $column
     * @param mixed $value
     * @param int|null $excludeId
     * @return bool
     */
    protected function isDuplicate(string $table, string $column, $value, $excludeId = null): bool
    {
        $query = \DB::table($table)->where($column, $value);
        
        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
}
