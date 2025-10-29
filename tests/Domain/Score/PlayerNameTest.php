<?php

declare(strict_types=1);

namespace App\Tests\Domain\Score;

use App\Domain\Score\PlayerName;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PlayerNameTest extends TestCase
{
    public function testTrimsValueOnCreation(): void
    {
        $name = new PlayerName('  Alice  ');

        self::assertSame('Alice', $name->value());
    }

    /**
     * @dataProvider invalidNameProvider
     */
    public function testInvalidNamesThrowException(string $value, string $expectedMessage): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        new PlayerName($value);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function invalidNameProvider(): iterable
    {
        yield 'empty string' => ['', 'Player name cannot be empty.'];
        yield 'too short' => ['Al', 'Player name must be at least 3 characters long.'];
        yield 'too long' => [str_repeat('a', 31), 'Player name must be at most 30 characters long.'];
        yield 'invalid characters' => ['Alice!', 'Player name contains invalid characters.'];
    }
}
