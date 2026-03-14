<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TaskControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $taskRepository;
    private string $path = '/task/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->taskRepository = $this->manager->getRepository(Task::class);

        foreach ($this->taskRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Task index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
        ]);

        self::assertResponseRedirects('/task');

        self::assertSame(1, $this->taskRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }

    public function testShow(): void
    {
        $fixture = new Task();

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Task');

        // Use assertions to check that the properties are properly displayed.
        $this->markTestIncomplete('This test was generated');
    }

    public function testEdit(): void
    {
        $fixture = new Task();

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
        ]);

        self::assertResponseRedirects('/task');

        $fixture = $this->taskRepository->findAll();

        $this->markTestIncomplete('This test was generated');
    }

    public function testRemove(): void
    {
        $fixture = new Task();

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/task');
        self::assertSame(0, $this->taskRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }
}
