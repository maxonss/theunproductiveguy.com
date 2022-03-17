<?php

namespace App\Tests;

use App\Entity\User;
use App\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TagTest extends KernelTestCase
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
     * @return void
     * @group entity
     */
    public function a_tag_record_can_be_inserted(): void
    {
        $tag = new Tag();
        $createdAt = $tag->getCreatedAt();
        $tag->setName('Tip');
        $tag->setDescription('Quick tip about anything');

        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        $tagRepository = $this->entityManager->getRepository(Tag::class);
        $tagRecord = $tagRepository->findOneBy(['id' => 1]);

        $this->assertEquals('Tip', $tagRecord->getName());
        $this->assertEquals('Quick tip about anything', $tagRecord->getDescription());
        $this->assertEquals($createdAt, $tagRecord->getCreatedAt());
    }

}