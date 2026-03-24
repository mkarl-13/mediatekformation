<?php

namespace App\Tests\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests du contrôleur d'administration des catégories
 *
 * @author Karl
 */
class AdminCategoriesControllerTest extends WebTestCase {

    /**
     * Retourne l'utilisateur admin pour les tests authentifiés
     * @return User
     */
    public function getUser() : User {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(["username" => "admin"]);
        return $user;
    }

    /**
     * Vérifie que la page admin des catégories est accessible
     */
    public function testAccesPage() {
        $client = static::createClient();
        $client->loginUser($this->getUser());

        $client->request("GET","/admin/categories");
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Vérifie le tri des catégories par nom en ordre croissant (admin)
     */
    public function testTriNomAsc() {
        $client = static::createClient();
        $client->loginUser($this->getUser());

        $crawler = $client->request("GET", "/admin/categories");
        $this->assertResponseIsSuccessful();

        $lien = $crawler->filter("#sortByNameAsc")->link();
        $crawler = $client->click($lien);
        $this->assertResponseIsSuccessful();

        $premierTitre = $crawler->filter("#categorieName")->first()->text();
        $this->assertEquals("Android", $premierTitre,
                "la première catégorie renvoyée n'est pas celle attendue en tri ASC");
    }

    /**
     * Vérifie le tri des catégories par nom en ordre décroissant (admin)
     */
    public function testTriNomDesc() {
        $client = static::createClient();
        $client->loginUser($this->getUser());

        $crawler = $client->request("GET", "/admin/categories");
        $this->assertResponseIsSuccessful();

        $lien = $crawler->filter("#sortByNameDesc")->link();
        $crawler = $client->click($lien);
        $this->assertResponseIsSuccessful();

        $premierTitre = $crawler->filter("#categorieName")->first()->text();
        $this->assertEquals("UML", $premierTitre,
                "la première catégorie renvoyée n'est pas celle attendue en tri DESC");
    }

    /**
     * Vérifie le tri des catégories par nombre de formations en ordre croissant (admin)
     */
    public function testTriNombreFormationsAsc() {
        $client = static::createClient();
        $client->loginUser($this->getUser());

        $crawler = $client->request("GET", "/admin/categories");
        $this->assertResponseIsSuccessful();

        $lien = $crawler->filter("#sortByFormationCountAsc")->link();
        $crawler = $client->click($lien);
        $this->assertResponseIsSuccessful();

        $premierTitre = $crawler->filter("#categorieName")->first()->text();
        $this->assertEquals("UML", $premierTitre,
                "la première catégorie renvoyée n'est pas celle attendue en tri ASC");
    }

    /**
     * Vérifie le tri des catégories par nombre de formations en ordre décroissant (admin)
     */
    public function testTriNombreFormationsDesc() {
        $client = static::createClient();
        $client->loginUser($this->getUser());

        $crawler = $client->request("GET", "/admin/categories");
        $this->assertResponseIsSuccessful();

        $lien = $crawler->filter("#sortByFormationCountDesc")->link();
        $crawler = $client->click($lien);
        $this->assertResponseIsSuccessful();

        $premierTitre = $crawler->filter("#categorieName")->first()->text();
        $this->assertEquals("C#", $premierTitre,
                "la première catégorie renvoyée n'est pas celle attendue en tri DESC");
    }

    /**
     * Vérifie le filtre des catégories par nom (admin)
     */
    public function testFiltreParNom() {
        $client = static::createClient();
        $client->loginUser($this->getUser());

        $crawler = $client->request("GET", "/admin/categories");
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter("#btnFilterName")->form();
        $form["recherche"]->setValue("C");
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();

        $nbLignes = $crawler->filter('table tbody tr')->count();
        $this->assertEquals(3, $nbLignes,
                "le nombre de catégories filtrées ne correspond pas au nombre attendu");

        $premierTitre = $crawler->filter("#categorieName")->first()->text();
        $this->assertEquals("C#", $premierTitre,
                "la première catégorie filtrée ne correspond pas à celle attendue");
    }
}
