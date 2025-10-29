<?php

declare(strict_types=1);

namespace App\Tests\Domain\Score;

use App\Domain\Score\ReactionTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ReactionTimeTest extends TestCase
{
    public function testStoresMilliseconds(): void
    {
        $reactionTime = new ReactionTime(150.5);

        self::assertSame(150.5, $reactionTime->toMilliseconds());
    }

    /**
     * @dataProvider invalidReactionTimeProvider
     */
    public function testInvalidValuesThrowException(float $value, string $expectedMessage): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        new ReactionTime($value);
    }

    /**
     * @return iterable<string, array{float, string}>
     */
    public static function invalidReactionTimeProvider(): iterable
    {
        yield 'not finite positive infinity' => [INF, 'Reaction time must be a finite number.'];
        yield 'not finite negative infinity' => [-INF, 'Reaction time must be a finite number.'];
        yield 'not a number' => [NAN, 'Reaction time must be a finite number.'];
        yield 'too small' => [0.5, 'Reaction time must be at least 1 milliseconds.'];
        yield 'negative' => [-10.0, 'Reaction time must be at least 1 milliseconds.'];
        yield 'too large' => [10001.0, 'Reaction time must not exceed 10000 milliseconds.'];
    }
}
