<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        DatabasePrimer::prime($kernel);

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    /**
     * @test
     * @group entity
     */
    public function a_user_can_be_inserted(): void
    {
        $user = new User();
        $createdAt = $user->getCreatedAt();
        $user->setUsername('tug');
        $user->setEmail('t@ug.com');
        $user->setPassword('$2y$13$wCu5VgEfjwSCt3sUUADDfOrnkY3u3CE9wZnjnRWFf5ODsGNZFGYtu');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $userRepository = $this->entityManager->getRepository(User::class);
        $userRecord = $userRepository->findOneBy(['username' => 'tug']);

        $this->assertEquals($createdAt, $userRecord->getCreatedAt());
        $this->assertEquals('tug', $userRecord->getUsername());
        $this->assertEquals('t@ug.com', $userRecord->getEmail());
    }
}