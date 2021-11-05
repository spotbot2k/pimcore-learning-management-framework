<?php

/**
 * This file is part of the Pimcore Learning Management Framework
 * Docs and updates: https://github.com/spotbot2k/pimcore-learning-management-framework
 *
 *  @license GPLv3
 */

namespace LearningManagementFrameworkBundle\Question;

use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;
use Symfony\Component\Form\FormEvent;

interface QuestionInterface
{
    public static function renderQuestion(FormEvent &$event): void;

    public static function processQuestion(string $type, AbstractData $question, $submitedValue): bool;
}
