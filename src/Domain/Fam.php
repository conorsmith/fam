<?php
declare(strict_types=1);

namespace ConorSmith\Fam\Domain;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

final class Fam
{
    /** @var Uuid */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $speciesId;

    /** @var array */
    private $feedTimes;

    public function __construct(
        Uuid $id,
        string $name,
        string $speciesId,
        array $feedTimes
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->speciesId = $speciesId;
        $this->feedTimes = $feedTimes;
    }

    public function feed(DateTimeImmutable $feedTime): void
    {
        $this->feedTimes[] = $feedTime;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSpeciesId(): string
    {
        return $this->speciesId;
    }

    public function getFeedTimes(): array
    {
        return $this->feedTimes;
    }

    public function isAlive(DateTimeImmutable $now): bool
    {
        return $this->getCalories($now) > 0
            && $this->getCalories($now) < 40 * 500;
    }

    public function getDistress(DateTimeImmutable $now): float
    {
        if (!$this->isAlive($now)) {
            return 0;
        }

        if ($this->getCalories($now) >= 3 * 500) {
            return 0;
        }

        return ((3 * 500) - $this->getCalories($now)) / (3 * 500);
    }

    public function isHappy(DateTimeImmutable $now): bool
    {
        if (!$this->isAlive($now)) {
            return false;
        }

        if ($this->isSick($now)) {
            return false;
        }

        return $this->wasJustFed($now);
    }

    public function isSick(DateTimeImmutable $now): bool
    {
        if (!$this->isAlive($now)) {
            return false;
        }

        return $this->wasJustFed($now)
            && $this->getCalories($now) > 6 * 500;
    }

    private function getCalories(DateTimeImmutable $now): float
    {
        $calories = 0;

        /** @var DateTimeImmutable $feedTime */
        foreach ($this->feedTimes as $feedTime) {
            $secondsSinceLastFeed = $now->getTimestamp() - $feedTime->getTimestamp();
            $secondsRemainingForFeed = FEED_TTL - $secondsSinceLastFeed;
            if ($secondsRemainingForFeed > 0) {
                $calories += 500 * $secondsRemainingForFeed / FEED_TTL;
            }
        }

        return $calories;
    }

    private function wasJustFed(DateTimeImmutable $now): bool
    {
        $latestFeedTime = $this->feedTimes[count($this->feedTimes) - 1];

        $secondsSinceLastFeed = $now->getTimestamp() - $latestFeedTime->getTimestamp();

        return $secondsSinceLastFeed <= 1;
    }
}
