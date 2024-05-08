<?php

namespace App\Service;

use App\DTO\CostDetailsDto;
use DateTime;

class CostCalculator
{
    public function calculate(CostDetailsDto $costDetails): float
    {
        $baseCost = $costDetails->baseCost;
        $startDate = new DateTime($costDetails->startDate);
        $participantBirthdate = new DateTime($costDetails->participantBirthdate);
        $paymentDate = new DateTime($costDetails->paymentDate);

        // Рассчитываем детскую скидку
        $childDiscount = $this->childDiscount($startDate, $participantBirthdate, $baseCost);
        
        // Рассчитываем стоимость с учетом детской скидки
        $costAfterChildDiscount = $baseCost - $childDiscount;
        
        //dd($costAfterChildDiscount);
        // Рассчитываем скидку за раннее бронирование
        $earlyBookingDiscount = $this->earlyBookingDiscount($startDate, $paymentDate, $costAfterChildDiscount);
        //dd($earlyBookingDiscount);
        // Итоговая стоимость
        return $costAfterChildDiscount - $earlyBookingDiscount;
    }

    private function childDiscount(DateTime $startDate, DateTime $participantBirthdate, float $cost): float
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

    private function earlyBookingDiscount(DateTime $startDate, DateTime $paymentDate, float $cost): float
    {
        $paymentYear = $paymentDate->format('Y');
        $nextPaymentYear = (int)$paymentYear + 1;
        $discount = 0.0;

        // путешествия с датой старта с 1 апреля по 30 сентября следующего года(не от настоящего момента, а от года оплаты)
        if ($startDate >= new DateTime("April 1 $nextPaymentYear") && $startDate <= new DateTime("September 30 $nextPaymentYear")) {
            // при оплате весь ноябрь текущего и ранее скидка 7%
            if ($paymentDate <= new DateTime("November 30 $paymentYear")) {
                $discount = 0.07;
            // при оплате весь декабрь текущего года скидка 5%
            } elseif ($paymentDate >= new DateTime("December 1 $paymentYear") && $paymentDate <= new DateTime("December 31 $paymentYear")) {
                $discount = 0.05;
            }
        }
        // путешествия с датой старта с 1 апреля по 30 сентября текущего года
        if ($startDate >= new DateTime("April 1 $paymentYear") && $startDate <= new DateTime("September 30 $paymentYear")) {
            // при оплате весь январь текущего года скидка 3% (года старта путешествия)
            if ($paymentDate >= new DateTime("January 1 $paymentYear") && $paymentDate <= new DateTime("January 31 $paymentYear")) {
                $discount = 0.03;
            }
        }

        // путешествия с датой старта с 1 октября текущего года по 14 января следующего года
        if ($startDate >= new DateTime("October 1 $paymentYear") && $startDate <= new DateTime("January 14 $nextPaymentYear")) {
            // при оплате весь март текущего года и ранее скидка 7%
            if ($paymentDate <= new DateTime("March 31 $paymentYear")) {
                $discount = 0.07;
            // при оплате весь апрель текущего года скидка 5%
            } elseif ($paymentDate >= new DateTime("April 1 $paymentYear") && $paymentDate <= new DateTime("April 30 $paymentYear")) {
                $discount = 0.05;
            // при оплате весь май текущего года скидка 3%
            } elseif ($paymentDate >= new DateTime("May 1 $paymentYear") && $paymentDate <= new DateTime("May 31 $paymentYear")) {
                $discount = 0.03;
            }
        }

        // путешествия с датой старта с 15 января следующего года и далее
        if ($startDate >= new DateTime("January 15 $nextPaymentYear")) {
            // при оплате весь август текущего года и ранее скидка 7%
            if ($paymentDate <= new DateTime("August 31 $paymentYear")) {
                $discount = 0.07;
            // весь сентябрь текущего года - 5%
            } elseif ($paymentDate >= new DateTime("September 1 $paymentYear") && $paymentDate <= new DateTime("September 30 $paymentYear")) {
                $discount = 0.05;
            // весь октябрь текущего года - 3%
            } elseif ($paymentDate >= new DateTime("October 1 $paymentYear") && $paymentDate <= new DateTime("October 31 $paymentYear")) {
                $discount = 0.03;
            }
        }

        // возвращаем скидку за раннее бронирование, но не более 1500 рублей
        return min($cost * $discount, 1500);
    }
}
