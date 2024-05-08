<?php

namespace App\Service;

use App\DTO\CostDetailsDto;
use DateTime;

class TotalCostCalculator
{
    public function calculateTotalCost(CostDetailsDto $costDetails): float
    {
        $baseCost = $costDetails->baseCost;
        $startDate = new DateTime($costDetails->startDate);
        $participantBirthdate = new DateTime($costDetails->participantBirthdate);
        $paymentDate = new DateTime($costDetails->paymentDate);

        // Рассчитываем детскую скидку
        $childDiscount = $this->calculateChildDiscount($startDate, $participantBirthdate, $baseCost);

        // Рассчитываем стоимость с учетом детской скидки
        $costAfterChildDiscount = $baseCost - $childDiscount;

        // Рассчитываем скидку за раннее бронирование
        $earlyBookingDiscount = $this->calculateEarlyBookingDiscount($startDate, $paymentDate, $costAfterChildDiscount);

        // Итоговая стоимость
        return $baseCost - $earlyBookingDiscount;
    }

    private function calculateChildDiscount(DateTime $startDate, DateTime $participantBirthdate, float $cost): float
    {
        // Рассчитываем возраст участника на момент начала путешествия
        $ageAtStart = $participantBirthdate->diff($startDate)->y;

        // Применяем детскую скидку в зависимости от возраста
        if ($ageAtStart < 6) {
            // Скидка 80% для детей от 3 до 6 лет
            return $cost * 0.8;
        } elseif ($ageAtStart < 12) {
            // Скидка 30%, но не более 4500 рублей для детей от 6 до 12 лет
            return min($cost * 0.3, 4500);
        } elseif ($ageAtStart < 18) {
            // Скидка 10% для детей от 12 лет и старше
            return $cost * 0.1;
            // совершеннолетним скидку не предоставляем
        } else return 0;
    }

    private function calculateEarlyBookingDiscount(DateTime $startDate, DateTime $paymentDate, float $cost): float
    {
        $discount = 0.0;

        // путешествия с датой старта с 1 апреля по 30 сентября следующего года
        if ($startDate >= new DateTime('next year April 1') && $startDate <= new DateTime('next year September 30')) {
            // при оплате весь ноябрь текущего и ранее скидка 7%
            if ($paymentDate <= new DateTime('last day of November this year')) {
                $discount = 0.07;
            // при оплате весь декабрь текущего года скидка 5%
            } elseif ($paymentDate >= new DateTime('December 1 this year') && $paymentDate <= new DateTime('last day of December this year')) {
                $discount = 0.05;
            // при оплате весь январь следующего года скидка 3%
            } elseif ($paymentDate >= new DateTime('January 1 next year') && $paymentDate <= new DateTime('last day of January next year')) {
                $discount = 0.03;
            }
        }
        // путешествия с датой старта с 1 октября текущего года по 14 января следующего года
        elseif ($startDate >= new DateTime('this year October 1') && $startDate <= new DateTime('next year January 14')) {
            // при оплате весь март текущего года и ранее скидка 7%
            if ($paymentDate <= new DateTime('last day of March this year')) {
                $discount = 0.07;
            // при оплате весь апрель текущего года скидка 5%
            } elseif ($paymentDate >= new DateTime('April 1 this year') && $paymentDate <= new DateTime('last day of April this year')) {
                $discount = 0.05;
            // при оплате весь май текущего года скидка 3%
            } elseif ($paymentDate < new DateTime('June 1 this year') && $paymentDate >= new DateTime('May 1 this year')) {
                $discount = 0.03;
            }
        }
        // путешествия с датой старта с 15 января следующего года и далее
        elseif ($startDate >= new DateTime('next year January 15')) {
            // при оплате весь август текущего года и ранее скидка 7%
            if ($paymentDate <= new DateTime('last day of August this year')) {
                $discount = 0.07;
            // весь сентябрь текущего года - 5%
            } elseif ($paymentDate >= new DateTime('September 1 this year') && $paymentDate <= new DateTime('last day of September this year')) {
                $discount = 0.05;
            // весь октябрь текущего года - 3%
            } elseif ($paymentDate >= new DateTime('October 1 this year') && $paymentDate <= new DateTime('last day of October this year')) {
                $discount = 0.03;
            }
        }

        // возвращаем скидку за раннее бронирование, но не более 1500 рублей
        return min($cost * $discount, 1500);
    }
}
