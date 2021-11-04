<?php

/**
 * This file is part of the Pimcore Learning Management Framework
 * Docs and updates: https://github.com/spotbot2k/pimcore-learning-management-framework
 *
 *  @license GPLv3
 */

namespace LearningManagementFrameworkBundle\Processor;

interface QuestionProcessorInterface
{
    public static function processQuestion(string $type, iterable $answers, $submitedValue): bool;
}
