<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests du contrôleur de la page d'accueil
 *
 * @author Karl
 */
class AccueilControllerTest extends WebTestCase {

    /**
     * Vérifie que la page d'accueil est accessible
     */
    public function testAccesPage() {
        $client = static::createClient();
        $client->request("GET","/");
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
