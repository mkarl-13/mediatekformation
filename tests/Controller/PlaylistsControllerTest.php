<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests du contrôleur des playlists
 *
 * @author Karl
 */
class PlaylistsControllerTest extends WebTestCase {

    /**
     * Vérifie que la page des playlists est accessible
     */
    public function testAccesPage() {
        $client = static::createClient();
        $client->request("GET","/playlists");
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Vérifie le tri des playlists par nom en ordre croissant
     */
    public function testTriNomAsc() {
        $client = static::createClient();
        $crawler = $client->request("GET", "/playlists");
        $this->assertResponseIsSuccessful();

        $lien = $crawler->filter("#sortByNameAsc")->link();
        $crawler = $client->click($lien);
        $this->assertResponseIsSuccessful();

        $premierTitre = $crawler->filter("#playlistName")->first()->text();
        $this->assertEquals("Bases de la programmation (C#)", $premierTitre,
                "la première playlist renvoyée n'est pas celle attendue en tri ASC");
    }

    /**
     * Vérifie le tri des playlists par nom en ordre décroissant
     */
    public function testTriNomDesc() {
        $client = static::createClient();
        $crawler = $client->request("GET", "/playlists");
        $this->assertResponseIsSuccessful();

        $lien = $crawler->filter("#sortByNameDesc")->link();
        $crawler = $client->click($lien);
        $this->assertResponseIsSuccessful();

        $premierTitre = $crawler->filter("#playlistName")->first()->text();
        $this->assertEquals("Visual Studio 2019 et C#", $premierTitre,
                "la première playlist renvoyée n'est pas celle attendue en tri DESC");
    }

    /**
     * Vérifie le tri des playlists par nombre de formations en ordre croissant
     */
    public function testTriNombreFormationsAsc() {
        $client = static::createClient();
        $crawler = $client->request("GET", "/playlists");
        $this->assertResponseIsSuccessful();

        $lien = $crawler->filter("#sortByFormationCountAsc")->link();
        $crawler = $client->click($lien);
        $this->assertResponseIsSuccessful();

        $premierTitre = $crawler->filter("#playlistName")->first()->text();
        $this->assertEquals("Cours Merise/2", $premierTitre,
                "la première playlist renvoyée n'est pas celle attendue en tri ASC");
    }

    /**
     * Vérifie le tri des playlists par nombre de formations en ordre décroissant
     */
    public function testTriNombreFormationsDesc() {
        $client = static::createClient();
        $crawler = $client->request("GET", "/playlists");
        $this->assertResponseIsSuccessful();

        $lien = $crawler->filter("#sortByFormationCountDesc")->link();
        $crawler = $client->click($lien);
        $this->assertResponseIsSuccessful();

        $premierTitre = $crawler->filter("#playlistName")->first()->text();
        $this->assertEquals("Bases de la programmation (C#)", $premierTitre,
                "la première playlist renvoyée n'est pas celle attendue en tri DESC");
    }

    /**
     * Vérifie le filtre des playlists par nom
     */
    public function testFiltreParNom() {
        $client = static::createClient();
        $crawler = $client->request("GET", "/playlists");
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter("#btnFilterName")->form();
        $form["recherche"]->setValue("Sujet");
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();

        $nbLignes = $crawler->filter('table tbody tr')->count();
        $this->assertEquals(8, $nbLignes,
                "le nombre de playlists filtrées ne correspond pas au nombre attendu");

        $premierTitre = $crawler->filter("#playlistName")->first()->text();
        $this->assertEquals("Exercices objet (sujets EDC BTS SIO)", $premierTitre,
                "la première playlist filtrée ne correspond pas à celle attendue");
    }

    /**
     * Vérifie le filtre des playlists par catégorie
     */
    public function testFiltreParCategorie() {
        $client = static::createClient();
        $crawler = $client->request("GET", "/playlists");
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter("#selectFilterCategorie")->closest("form")->form();
        $form["recherche"]->setValue("1");
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();

        $nbLignes = $crawler->filter("table tbody tr")->count();
        $this->assertEquals(5, $nbLignes,
                "le nombre de playlists filtrées par catégorie ne correspond pas au nombre attendu");

        $premierTitre = $crawler->filter("#playlistName")->first()->text();
        $this->assertEquals("Bases de la programmation (C#)", $premierTitre,
                "la première playlist filtrée par catégorie ne correspond pas à celle attendue");
    }

    /**
     * Vérifie que le lien vers le détail d'une playlist fonctionne correctement
     */
    public function testLienVersPlaylist() {
        $client = static::createClient();
        $crawler = $client->request("GET", "/playlists");
        $this->assertResponseIsSuccessful();

        $premierNom = $crawler->filter("#playlistName")->first()->text();
        $premierNombreFormations = $crawler->filter("#playlistFormationCount")->first()->text();

        $lien = $crawler->filter("#btnShowPlaylist")->first()->link();
        $crawler = $client->click($lien);

        $this->assertResponseIsSuccessful();
        $this->assertEquals(trim($premierNom), trim($crawler->filter("#playlistName")->text()),
                "le nom de la playlist ne correspond pas");
        $this->assertEquals(trim($premierNombreFormations), trim($crawler->filter("#playlistFormationCount")->text()),
                "le nombre de formations de la playlist ne correspond pas");
    }
}
