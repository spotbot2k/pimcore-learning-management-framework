<?php

/**
 * This file is part of the Pimcore Learning Management Framework
 * Docs and updates: https://github.com/spotbot2k/pimcore-learning-management-framework
 *
 *  @license GPLv3
 */

namespace LearningManagementFrameworkBundle\Helper;

use Pimcore\Db;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ExamDefinition;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;
use Symfony\Component\Security\Core\Security;
use LearningManagementFrameworkBundle\Result\RejectionResult;

class ExamHelper
{
    private $user = null;

    private $attemptResetInterval;

    public function __construct(
        string $defaultStudentClass,
        int $attemptResetInterval = 0,
        Security $security
    ) {
        $user = $security->getUser();
        $class = sprintf("Pimcore\Model\DataObject\%s", $defaultStudentClass);
        if ($user instanceof $class) {
            $this->user = $security->getUser();
        }

        $this->attemptResetInterval = $attemptResetInterval;
    }

    public function process(ExamDefinition $exam, array $data): array
    {
        $correctAnswers = 0;
        $incorrectAnswers = [];
        $timeTaken = time() - $data['initts'];
        $isPassed = false;
        $gradeAchieved = null;

        if (array_key_exists('questions', $data)) {
            foreach ($exam->getQuestions() as $idx => $question) {
                $isCorrect = $this->processQuestion($question->getType(), $question, $data['questions']['items'][$idx]['answer']);
                if ($isCorrect) {
                    $correctAnswers += 1;
                } else {
                    $incorrectAnswers[] = [
                        'idx'       => $idx,
                        'title'     => $question->getQuestion(),
                        'submitted' => $data['questions']['items'][$idx]['answer'],
                    ];
                }
            }
        }

        $ratio = $correctAnswers / $exam->getQuestions()->getCount() * 100;

        foreach ($exam->getGrades() as $grade) {
            if (array_key_exists('ratio', $grade) && $ratio < $grade['ratio']->getData()) {
                continue;
            }
            if (array_key_exists('timeLimit', $grade)
                && !is_null($grade['timeLimit']->getData())
                && $timeTaken > $grade['timeLimit']->getData()
            ) {
                continue;
            }

            $gradeAchieved = $grade['title']->getData();
            $isPassed = $grade['unlockCertificate']->getData();

            break;
        }

        // Track user progress if allowed to
        if (!is_null($this->user) && $exam->getTrackStudentProgress()) {
            $this->trackProgress($exam, $this->user, $isPassed, $gradeAchieved, $ratio, $timeTaken);
        }

        return [
            'correct'   => $correctAnswers,
            'incorrect' => $incorrectAnswers,
            'ratio'     => $ratio,
            'time'      => $timeTaken,
            'grade'     => $gradeAchieved,
        ];
    }

    public function isSudentLoggedIn()
    {
        return !is_null($this->user);
    }

    public function getStudent()
    {
        return $this->user;
    }

    public function getAttemptsCountForCurrentUser(ExamDefinition $exam): ?int
    {
        if ($this->isSudentLoggedIn()) {
            return $this->getAttemptsCountForUser($exam, $this->user);
        }

        return null;
    }

    public function canAttend(ExamDefinition $exam): RejectionResult
    {
        if ($this->user) {
            if ($exam->getMaxAttempts() && $this->getAttemptsCountForUser($exam, $this->user) > $exam->getMaxAttempts()) {
                return new RejectionResult(RejectionResult::OUT_OF_ATTEMPTS);
            }

            $prerequisites = $exam->getPrerequisites();
            $unfulfilledPrerequisites = [];

            foreach ($prerequisites as $prerequisite) {
                if (!$this->userHasPassed($prerequisite, $this->user)) {
                    $unfulfilledPrerequisites[] = [
                        "id" => $prerequisite->getId(),
                        "title" => $prerequisite->getTitle(),
                    ];
                }
            }

            if (!empty($unfulfilledPrerequisites)) {
                $result = new RejectionResult(RejectionResult::UNFULFILLED_PREREQUISITE);

                return $result->setUnfulfilledPrerequisites($unfulfilledPrerequisites);
            }
        } elseif (!$exam->getAllowAnonymous()) {
            return new RejectionResult(RejectionResult::NOT_LOGGED_IN);
        }

        return new RejectionResult(RejectionResult::NOT_REJECTED);
    }

    public function hasPassed(ExamDefinition $exam): bool
    {
        if ($this->isSudentLoggedIn()) {
            return $this->userHasPassed($exam, $this->user);
        }

        return false;
    }

    public function userHasPassed(ExamDefinition $exam, $user)
    {
        $result = Db::get()->fetchRow('
            SELECT count(`id`)
                FROM `plugin_lmf_student_progress`
            WHERE `examId` = ? AND `studentId` = ? AND `isPassed` = 1 AND `isActive` = 1
            LIMIT 1',
            [ $exam->getId(), $user->getId() ]
        );
    }

    public function getCertificateByHash(string $hash): ?array
    {
        return Db::get()->fetchRow('
            SELECT *
            FROM `plugin_lmf_student_progress`
                WHERE `uuid` = ? AND `isPassed` = 1 and `isActive` = 1
            ORDER BY `date` DESC LIMIT 1',
            [ $hash ]
        );
    }

    private function processQuestion(string $type, AbstractData $question, $submitedValue): bool
    {
        $class = sprintf("LearningManagementFrameworkBundle\Question\%sQuestion", $type);

        if (class_exists($class)) {
            return $class::processQuestion($type, $question, $submitedValue);
        }

        return false;
    }

    private function trackProgress(ExamDefinition $exam, Concrete $user, bool $isPassed, ?string $grade, int $ratio, int $time)
    {
        $uuid = $this->getTrackingEntryHash($exam);

        return Db::get()->executeQuery(
            'INSERT INTO `plugin_lmf_student_progress` (`uuid`, `examId`, `studentId`, `isPassed`, `grade`, `ratio`, `time`) VALUES (?, ?, ?, ?, ?, ?, ?)',
            [ $uuid, $exam->getId(), $user->getId(), $isPassed, $grade, $ratio, $time ]
        );
    }

    private function getAttemptsCountForUser(ExamDefinition $exam, $user): int
    {
        $result = Db::get()->fetchOne(
            'SELECT COUNT(id) cnt FROM
                `plugin_lmf_student_progress`
            WHERE
                `examId` = ?
                AND `studentId` = ?
                AND `isActive` = 1
                AND TIMESTAMPDIFF(HOUR, date, CURRENT_TIMESTAMP) <= ?', [
            $exam->getId(),
            $user->getId(),
            $this->attemptResetInterval,
        ]);

        return $result;
    }

    private function getTrackingEntryHash(ExamDefinition $exam): string
    {
        return sprintf('%s-%s-%s', bin2hex(random_bytes(6)), sha1(time()), bin2hex($exam->getId()));
    }
}
