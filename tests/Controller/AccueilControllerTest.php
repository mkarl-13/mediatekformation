<?php

namespace mediatekformation\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of AccueilControllerTest
 *
 * @author Karl
 */
class AccueilControllerTest extends WebTestCase {
    public function testAccesPage() {
        $client = static::createClient();
        $client->request("GET","/");
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
