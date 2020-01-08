<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthControllerTest extends WebTestCase
{
    public function testLoginInvite()
    {
        $client = static::createClient();
        $client->request('GET', '/en/auth/login');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testLoginSubmit()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/auth/login');
        $buttonCrawlerNode = $crawler->selectButton('Log in');
        $form = $buttonCrawlerNode->form([
            'identity'    => 'admin@example.com',
            'password' => 'pass',
        ]);

        $client->submit($form);
        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
