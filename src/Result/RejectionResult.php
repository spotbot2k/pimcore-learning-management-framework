<?php

namespace LearningManagementFrameworkBundle\Result;

class RejectionResult
{
    public const NOT_REJECTED = null;
    public const NOT_LOGGED_IN = 0;
    public const OUT_OF_ATTEMPTS = 1;
    public const UNFULFILLED_PREREQUISITE = 2;

    private ?int $reason;
    private array $unfulfilledPrerequisites;

    public function __construct(?int $reason = null)
    {
        $this->reason = $reason;
    }

    public function isRejected()
    {
        return !is_null($this->reason);
    }

    public function getReason()
    {
        return $this->reason;
    }

    public function getUnfulfilledPrerequisites()
    {
        return $this->unfulfilledPrerequisites;
    }

    public function setUnfulfilledPrerequisites(array $unfulfilledPrerequisites): self
    {
        $this->unfulfilledPrerequisites = $unfulfilledPrerequisites;

        return $this;
    }
}
