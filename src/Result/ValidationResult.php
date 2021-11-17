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
    private array $validationResult;

    public function __construct($result)
    {
        if (is_array($result)) {
            $this->validationResult = $result;
        }
    }

    public function isValid(): bool
    {
        return count($this->validationResult) > 0;
    }

    public function get($key)
    {
        if (array_key_exists($key, $this->validationResult)) {
            return $this->validationResult[$key];
        }

        return null;
    }
}
