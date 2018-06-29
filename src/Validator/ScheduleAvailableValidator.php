<?php

namespace App\Validator;

use App\Entity\Schedule;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ScheduleAvailableValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ScheduleAvailableValidator constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint ScheduleAvailable */

        /** @var Schedule $schedule */
        $schedule = $this->context->getObject();

        if ($schedule->getStartDateAt() && $schedule->getEndDateAt()) {
            $schedulesNotAvailable = $this->em->getRepository(Schedule::class)->findByDateStartEnd(
                $schedule->getStartDateAt()->format('m/d/Y H:i'),
                $schedule->getEndDateAt()->format('m/d/Y H:i')
            );

            $checkCount = 0;

            /** @var Schedule $item */
            foreach ($schedulesNotAvailable as $item) {
                if ($item->getId() !== $schedule->getId()) {
                    $checkCount++;
                }
            }

            if ($checkCount > 0) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
}
