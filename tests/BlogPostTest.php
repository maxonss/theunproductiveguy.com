<?php

namespace App\Tests;

use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BlogPostTest extends KernelTestCase
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
    public function a_post_can_be_inserted(): void
    {
        $userRecord = new User();
        $userRecord->setUsername('tug');
        $userRecord->setEmail('t@ug.com');
        $userRecord->setRoles(["ROLE_USER", "ROLE_ADMIN"]);
        $userRecord->setPassword('$2y$13$wCu5VgEfjwSCt3sUUADDfOrnkY3u3CE9wZnjnRWFf5ODsGNZFGYtu');

        $this->entityManager->persist($userRecord);
        $this->entityManager->flush();

        $post = new Post();
        $post->setTitle($title = 'Stop using your Dock right now');
        $post->setSlug($slug = 'stop-the-dock');
        $post->setSummary($summary = 'Curabitur in nulla massa. Mauris at scelerisque leo. Curabitur suscipit risus quis neque pharetra, ac mattis eros cursus. Donec in leo ut nibh ultricies');
        $post->setContent($content = 'Contenu de l\'article');
        $post->setAuthor($userRecord);
        $post->setPublishedAt($post->getCreatedAt());
        $post->setUpdatedAt(null);
        $post->setUpdatedBy(null);

        $this->entityManager->persist($post);

        $tag = new Tag();
        $createdAt = $tag->getCreatedAt();
        $tag->setName('Tip');
        $tag->setDescription('Quick tip about anything');
        $this->entityManager->persist($tag);

        $post->addTag($tag);

        $this->entityManager->flush();

        $postRepository = $this->entityManager->getRepository(Post::class);
        $postRecord = $postRepository->findOneBy(['slug' => $slug]);

        $tagsArray = new ArrayCollection();
        $tagsArray[] = $tag;

        $this->assertEquals($title, $postRecord->getTitle());
        $this->assertEquals($slug, $postRecord->getSlug());
        $this->assertEquals($summary, $postRecord->getSummary());
        $this->assertEquals($content, $postRecord->getContent());
        $this->assertEquals($userRecord, $postRecord->getAuthor());
        $this->assertNull($postRecord->getUpdatedAt());
        $this->assertEquals($tagsArray[0], $postRecord->getTags()[0]);
        $this->assertEquals(null, $postRecord->getUpdatedBy());
    }
}
