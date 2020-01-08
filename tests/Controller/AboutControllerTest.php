<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AboutControllerTest extends WebTestCase
{
    public function testDefaultLanguageRedirect()
    {
        $client = static::createClient();

        $client->request('GET', '/');
        $this->assertTrue(
            $client->getResponse()->isRedirect('/en')
        );
    }

    public function testHome()
    {
        $client = static::createClient();

        $client->request('GET', '/en');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testShowFaq()
    {
        $client = static::createClient();

        $client->request('GET', '/en/about/faq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testShowTermsOfUse()
    {
        $client = static::createClient();

        $client->request('GET', '/en/about/terms_of_use');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testPrivacyPolicy()
    {
        $client = static::createClient();

        $client->request('GET', '/en/about/privacy_policy');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testNonExistentPage()
    {
        $client = static::createClient();

        $client->request('GET', '/en/about/nothing_here');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
