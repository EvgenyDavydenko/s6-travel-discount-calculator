<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\CostCalculator;
use App\DTO\CostDetailsDto;

class AdultCostCalculatorTest extends TestCase
{
    private $calculator;

    protected function setUp(): void
    {
        $this->calculator = new CostCalculator();
    }

    public function test1_1(): void
    {
        // путешествия с датой старта с 1 апреля по 30 сентября следующего года
        // при оплате весь ноябрь текущего и ранее скидка 7%
        $costDetails = new CostDetailsDto();
        $costDetails->baseCost = 10000;
        $costDetails->participantBirthdate = '2000-05-01';
        $costDetails->startDate = '2027-05-01';
        $costDetails->paymentDate = '2026-11-30';

        $totalCost = $this->calculator->calculate($costDetails);

        $this->assertEquals(9300, $totalCost);
    }

    public function test1_2(): void
    {
        // путешествия с датой старта с 1 апреля по 30 сентября следующего года
        // при оплате весь декабрь текущего года скидка 5%
        $costDetails = new CostDetailsDto();
        $costDetails->baseCost = 10000;
        $costDetails->participantBirthdate = '2000-05-01';
        $costDetails->startDate = '2027-05-01';
        $costDetails->paymentDate = '2026-12-31';

        $totalCost = $this->calculator->calculate($costDetails);

        $this->assertEquals(9500, $totalCost);
    }

    public function test1_3(): void
    {
        // путешествия с 1 апреля по 30 сентября следующего года
        // при оплате весь январь следующего года скидка 3%
        $costDetails = new CostDetailsDto();
        $costDetails->baseCost = 10000;
        $costDetails->participantBirthdate = '2000-05-01';
        $costDetails->startDate = '2027-05-01';
        $costDetails->paymentDate = '2027-01-31';

        $totalCost = $this->calculator->calculate($costDetails);

        $this->assertEquals(9700, $totalCost);
    }

    public function test2_1(): void
    {
        // путешествия с 1 октября текущего года по 14 января следующего года
        // при оплате весь март текущего года и ранее скидка 7%
        $costDetails = new CostDetailsDto();
        $costDetails->baseCost = 10000;
        $costDetails->participantBirthdate = '2000-05-01';
        $costDetails->startDate = '2026-11-02';
        $costDetails->paymentDate = '2026-03-31';

        $totalCost = $this->calculator->calculate($costDetails);

        $this->assertEquals(9300, $totalCost);
    }

    public function test2_2(): void
    {
        // путешествия с 1 октября текущего года по 14 января следующего года
        // при оплате весь апрель текущего года скидка 5%
        $costDetails = new CostDetailsDto();
        $costDetails->baseCost = 10000;
        $costDetails->participantBirthdate = '2000-05-01';
        $costDetails->startDate = '2026-11-02';
        $costDetails->paymentDate = '2026-04-30';

        $totalCost = $this->calculator->calculate($costDetails);

        $this->assertEquals(9500, $totalCost);
    }

    public function test2_3(): void
    {
        // путешествия с 1 октября текущего года по 14 января следующего года
        // при оплате весь май текущего года скидка 3%
        $costDetails = new CostDetailsDto();
        $costDetails->baseCost = 10000;
        $costDetails->participantBirthdate = '2000-05-01';
        $costDetails->startDate = '2026-11-02';
        $costDetails->paymentDate = '2026-05-31';

        $totalCost = $this->calculator->calculate($costDetails);

        $this->assertEquals(9700, $totalCost);
    }

    public function test3_1(): void
    {
        // путешествия с 15 января следующего года и далее
        // при оплате весь август текущего года и ранее скидка 7%
        $costDetails = new CostDetailsDto();
        $costDetails->baseCost = 10000;
        $costDetails->participantBirthdate = '2000-05-01';
        $costDetails->startDate = '2027-01-15';
        $costDetails->paymentDate = '2026-08-31';

        $totalCost = $this->calculator->calculate($costDetails);

        $this->assertEquals(9300, $totalCost);
    }

    public function test3_2(): void
    {
        // путешествия с 15 января следующего года и далее
        // при оплате весь сентябрь текущего года - 5%
        $costDetails = new CostDetailsDto();
        $costDetails->baseCost = 10000;
        $costDetails->participantBirthdate = '2000-05-01';
        $costDetails->startDate = '2027-01-15';
        $costDetails->paymentDate = '2026-09-30';

        $totalCost = $this->calculator->calculate($costDetails);

        $this->assertEquals(9500, $totalCost);
    }

    public function test3_3(): void
    {
        // путешествия с 15 января следующего года и далее
        // при оплате весь октябрь текущего года - 3%
        $costDetails = new CostDetailsDto();
        $costDetails->baseCost = 10000;
        $costDetails->participantBirthdate = '2000-05-01';
        $costDetails->startDate = '2027-01-15';
        $costDetails->paymentDate = '2026-10-31';

        $totalCost = $this->calculator->calculate($costDetails);

        $this->assertEquals(9700, $totalCost);
    }


}