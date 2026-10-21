<?php
namespace Tests;

use BusinessDays\Calculator;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    private Calculator $_sut;

    public function setUp(): void
    {
        $this->_sut = new Calculator();
    }

    public function dataProvider(): array
    {
        return include __DIR__ . '/fixtures/business_days.php';
    }

    /**
     * @dataProvider dataProvider
     *
     * @param int[]       $nonBusinessDays
     * @param \DateTime[] $freeDays
     * @param \DateTime[] $holidays
     */
    public function testReturnsExpected(
        string $message,
        \DateTime $startDate,
        int $howManyDays,
        \DateTime $expected,
        array $nonBusinessDays = [],
        array $freeDays = [],
        array $holidays = []
    ): void {
        $this->_sut->setFreeDays($freeDays);
        $this->_sut->setHolidays($holidays);
        $this->_sut->setFreeWeekDays($nonBusinessDays);
        $this->_sut->setStartDate($startDate);
        $this->_sut->addBusinessDays($howManyDays);

        $response = $this->_sut->getDate();

        $this->assertEquals($expected, $response, $message);
    }

    public function testTooManyBusinessDaysException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $nonBusinessDays = [
            Calculator::MONDAY,
            Calculator::TUESDAY,
            Calculator::WEDNESDAY,
            Calculator::THURSDAY,
            Calculator::FRIDAY,
            Calculator::SATURDAY,
            Calculator::SUNDAY,
        ];

        $this->_sut->setFreeWeekDays($nonBusinessDays);
        $this->_sut->addBusinessDays(1);
        $this->_sut->getDate();
    }

    public function testThatPassedParameterIsNotChangedByReferenceInSut(): void
    {
        $date = new \DateTime('2000-01-01');

        $this->_sut->setStartDate($date);
        $this->_sut->addBusinessDays(1);
        $responseDate = $this->_sut->getDate();

        $this->assertNotEquals($date, $responseDate);
    }
}
