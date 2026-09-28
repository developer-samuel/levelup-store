<?php

declare(strict_types=1);

namespace Tests\Support\Provides;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Test\TestContainer;

/** @method static TestContainer getContainer() */
trait Persistence
{
    private function getEntityManager(): EntityManagerInterface
    {
        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);

        return $em;
    }
}
