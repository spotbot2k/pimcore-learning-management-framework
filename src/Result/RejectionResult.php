<?php

/**
 * This file is part of the Pimcore Learning Management Framework
 * Docs and updates: https://github.com/spotbot2k/pimcore-learning-management-framework
 *
 *  @license GPLv3
 */

namespace LearningManagementFrameworkBundle\Result;

class RejectionResult
{
    public const NOT_REJECTED = null;

    public const NOT_LOGGED_IN = 'lmf_rejection_reason_not_logged_in';

    public const OUT_OF_ATTEMPTS = 'lmf_rejection_reason_out_of_attempts';

    public const UNFULFILLED_PREREQUISITE = 'lmf_rejection_reason_unfullfilled_prerequisite';

    public const ALLREADY_PASSED = 'lmf_rejection_reason_already_passed';

    private ?string $reason;

    private array $unfulfilledPrerequisites;

    public function __construct(?string $reason = null)
    {
        $this->reason = $reason;
    }

    public function isRejected()
    {
        return !is_null($this->reason);
    }

    public function getReason(): ?string
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
