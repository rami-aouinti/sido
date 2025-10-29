<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Infrastructure\Score\InMemoryScoreRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ScoreControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
        $repository = static::getContainer()->get(InMemoryScoreRepository::class);
        if ($repository instanceof InMemoryScoreRepository) {
            $repository->clear();
        }
    }

    public function testSubmitScoreReturnsCreatedResponse(): void
    {
        $this->client->jsonRequest('POST', '/api/scores', ['name' => 'Alice', 'reactionTime' => 123]);

        self::assertResponseStatusCodeSame(201);
        $data = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('Alice', $data['name']);
        self::assertSame(123, $data['reactionTime']);
        self::assertArrayHasKey('recordedAt', $data);
    }

    public function testSubmitScoreWithInvalidPayloadReturnsBadRequest(): void
    {
        $this->client->jsonRequest('POST', '/api/scores', ['name' => '', 'reactionTime' => -1]);

        self::assertResponseStatusCodeSame(400);
        $data = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertEqualsCanonicalizing([
            ['name' => 'name', 'message' => 'Name must not be empty.'],
            ['name' => 'reactionTime', 'message' => 'Reaction time must be greater than zero.'],
        ], $data['errors']);
    }

    public function testSubmitScoreWithNonStringNameReturnsFieldError(): void
    {
        $this->client->jsonRequest('POST', '/api/scores', ['name' => ['Alice'], 'reactionTime' => 150]);

        self::assertResponseStatusCodeSame(400);
        $data = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertEqualsCanonicalizing([
            ['name' => 'name', 'message' => 'Name must be a string.'],
        ], $data['errors']);
    }

    public function testSubmitScoreWithNonIntegerReactionTimeReturnsFieldError(): void
    {
        $this->client->jsonRequest('POST', '/api/scores', ['name' => 'Alice', 'reactionTime' => 'fast']);

        self::assertResponseStatusCodeSame(400);
        $data = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertEqualsCanonicalizing([
            ['name' => 'reactionTime', 'message' => 'Reaction time must be an integer.'],
        ], $data['errors']);
    }

    public function testLeaderboardReturnsTopScores(): void
    {
        $this->client->jsonRequest('POST', '/api/scores', ['name' => 'Alice', 'reactionTime' => 150]);
        $this->client->jsonRequest('POST', '/api/scores', ['name' => 'Bob', 'reactionTime' => 125]);
        $this->client->jsonRequest('POST', '/api/scores', ['name' => 'Charlie', 'reactionTime' => 175]);

        $this->client->request('GET', '/api/scores');
        self::assertResponseIsSuccessful();

        $data = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('scores', $data);
        self::assertCount(3, $data['scores']);
        self::assertSame('Bob', $data['scores'][0]['name']);
        self::assertSame('Alice', $data['scores'][1]['name']);
        self::assertSame('Charlie', $data['scores'][2]['name']);
    }
}
