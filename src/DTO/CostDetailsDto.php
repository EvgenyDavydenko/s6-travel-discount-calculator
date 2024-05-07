<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use DateTime;

class CostDetailsDto
{
    #[Assert\NotBlank(message: "Базовая стоимость не должна быть пустой")]
    #[Assert\Type(type: 'float', message: "Базовая стоимость должна быть числом")]
    public float $baseCost;

    #[Assert\NotBlank(message: "Дата старта путешествия не должна быть пустой")]
    #[Assert\DateTime(format: 'Y-m-d\TH:i:sP', message: "Дата старта путешествия должна быть корректной датой")]
    public string $startDate;

    #[Assert\NotBlank(message: "Дата рождения участника не должна быть пустой")]
    #[Assert\DateTime(format: 'Y-m-d\TH:i:sP', message: "Дата рождения участника должна быть корректной датой")]
    public string $participantBirthdate;

    #[Assert\NotBlank(message: "Дата оплаты не должна быть пустой")]
    #[Assert\DateTime(format: 'Y-m-d\TH:i:sP', message: "Дата оплаты должна быть корректной датой")]
    public string $paymentDate;

    #[Assert\Callback]
    public function validateParticipantAge(ExecutionContextInterface $context): void
    {
        $birthdate = new DateTime($this->participantBirthdate);
        $startDate = new DateTime($this->startDate);
        $ageAtStart = $birthdate->diff($startDate)->y;

        if ($ageAtStart < 3) {
            $context->buildViolation('Участник должен быть старше трёх лет на момент начала путешествия.')
                ->atPath('participantBirthdate')
                ->addViolation();
        }
    }

}