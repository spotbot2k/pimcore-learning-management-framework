<?php

/**
 * This file is part of the Pimcore Learning Management Framework
 * Docs and updates: https://github.com/spotbot2k/pimcore-learning-management-framework
 *
 *  @license GPLv3
 */

namespace LearningManagementFrameworkBundle\Result;

class AttemptResult
{
    public int $correctAnswers = 0;

    public array $incorrectAnswers = [];

    public int $ratio = 0;

    public int $time = 0;

    public ?string $gradeAchieved = null;

    public bool $isPassed = false;

    public ?string $hash;

    public function __construct(
        int $correctAnswers,
        array $incorrectAnswers,
        int $ratio,
        int $time,
        ?string $gradeAchieved,
        bool $isPassed,
        ?string $hash
    ) {
        $this->correctAnswers = $correctAnswers;
        $this->incorrectAnswers = $incorrectAnswers;
        $this->ratio = $ratio;
        $this->time = $time;
        $this->gradAchieved = $gradeAchieved;
        $this->isPassed = $isPassed;
        $this->hash = $hash;
    }
}
