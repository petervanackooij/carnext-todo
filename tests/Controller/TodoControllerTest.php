<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class TodoControllerTest.
 */
class TodoControllerTest extends WebTestCase
{
    public function testTodoList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreateTask(): void
    {
        $description = $this->generateRandomString();

        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $link = $crawler->selectLink('Create your first task here')->link();
        $client->click($link);
        $client->submitForm('Save', [
            'task[description]' => $description,
        ]);

        self::assertEquals(302, $client->getResponse()->getStatusCode());
        self::assertEquals('/', $client->getResponse()->headers->get('location'));

        $client->request('GET', '/');

        self::assertContains(sprintf('Task &quot;%s&quot; is created', $description), $client->getResponse()->getContent());
        self::assertContains($description, $client->getResponse()->getContent());
    }

    public function testCompleteTask(): void
    {
        $client = static::createClient();
        $client->request('GET', '/create');
        $client->submitForm('Save', [
            'task[description]' => 'task description',
        ]);

        $crawler = $client->request('GET', '/');

        self::assertContains('<td>task description</td>', $client->getResponse()->getContent());

        $link = $crawler->selectLink('Mark as completed')->link();
        $client->click($link);

        self::assertEquals(302, $client->getResponse()->getStatusCode());
        self::assertEquals('/', $client->getResponse()->headers->get('location'));

        $client->request('GET', '/');

        self::assertContains('Task &quot;task description&quot; is marked as completed', $client->getResponse()->getContent());
        self::assertNotContains('<td>task description</td>', $client->getResponse()->getContent());
    }

    /**
     * @param int $length
     *
     * @return string
     */
    private function generateRandomString($length = 25): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
