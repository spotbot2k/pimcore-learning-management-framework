<?php

/**
 * This file is part of the Pimcore Learning Management Framework
 * Docs and updates: https://github.com/spotbot2k/pimcore-learning-management-framework
 *
 *  @license GPLv3
 */

namespace LearningManagementFrameworkBundle\Result;

class ValidationResult
{
    private ?array $validationResult = null;

    public function __construct($result)
    {
        if (is_array($result)) {
            $this->validationResult = $result;
        }
    }

    public function isValid(): bool
    {
        return !is_null($this->validationResult);
    }

    public function get($key)
    {
        if (!is_null($this->validationResult) && array_key_exists($key, $this->validationResult)) {
            return $this->validationResult[$key];
        }

        return null;
    }

    public static function negative()
    {
        return new ValidationResult(false);
    }
}
