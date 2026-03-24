<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests du contrôleur des formations
 *
 * @author Karl
 */
class FormationsControllerTest extends WebTestCase {

    /**
     * Vérifie que la page des formations est accessible
     */
    public function testAccesPage() {
        $client = static::createClient();
        $client->request("GET","/formations");
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Vérifie le tri des formations par titre en ordre croissant
     */
    public function testTriTitreAsc() {
        $client = static::createClient();
        $crawler = $client->request("GET", "/formations");
        $this->assertResponseIsSuccessful();

        $lien = $crawler->filter("#sortByTitleAsc")->link();
        $crawler = $client->click($lien);
        $this->assertResponseIsSuccessful();

        $premierTitre = $crawler->filter("#formationTitle")->first()->text();
        $this->assertEquals("Android Studio (complément n°1) : Navigation Drawer et Fragment", $premierTitre,
                "la première formation renvoyée n'est pas celle attendue en tri ASC");
    }

    /**
     * Vérifie le tri des formations par titre en ordre décroissant
     */
    public function testTriTitreDesc() {
        $client = static::createClient();
        $crawler = $client->request("GET", "/formations");
        $this->assertResponseIsSuccessful();

        $lien = $crawler->filter("#sortByTitleDesc")->link();
        $crawler = $client->click($lien);
        $this->assertResponseIsSuccessful();

        $premierTitre = $crawler->filter("#formationTitle")->first()->text();
        $this->assertEquals("UML : Diagramme de paquetages", $premierTitre,
                "la première formation renvoyée n'est pas celle attendue en tri DESC");
    }

    /**
     * Vérifie le tri des formations par nom de playlist en ordre croissant
     */
   public function testTriNomPlaylistAsc() {
        $client = static::createClient();
        $crawler = $client->request("GET", "/formations");
        $this->assertResponseIsSuccessful();

        $lien = $crawler->filter("#sortByPlaylistNameAsc")->link();
        $crawler = $client->click($lien);
        $this->assertResponseIsSuccessful();

        $premierTitre = $crawler->filter("#formationTitle")->first()->text();
        $this->assertEquals("Eclipse n°8 : Déploiement", $premierTitre,
                "la première formation renvoyée n'est pas celle attendue en tri ASC");
    }

    /**
     * Vérifie le tri des formations par nom de playlist en ordre décroissant
     */
    public function testTriNomPlaylistDesc() {
        $client = static::createClient();
        $crawler = $client->request("GET", "/formations");
        $this->assertResponseIsSuccessful();

        $lien = $crawler->filter("#sortByPlaylistNameDesc")->link();
        $crawler = $client->click($lien);
        $this->assertResponseIsSuccessful();

        $premierTitre = $crawler->filter("#formationTitle")->first()->text();
        $this->assertEquals("C# : ListBox en couleur", $premierTitre,
                "la première formation renvoyée n'est pas celle attendue en tri DESC");
    }

    /**
     * Vérifie le tri des formations par date en ordre croissant
     */
    public function testTriDateAsc() {
        $client = static::createClient();
        $crawler = $client->request("GET", "/formations");
        $this->assertResponseIsSuccessful();

        $lien = $crawler->filter("#sortByPublishedAtAsc")->link();
        $crawler = $client->click($lien);
        $this->assertResponseIsSuccessful();

        $premierTitre = $crawler->filter("#formationTitle")->first()->text();
        $this->assertEquals("Cours UML (1 à 7 / 33) : introduction et cas d'utilisation", $premierTitre,
                "la première formation renvoyée n'est pas celle attendue en tri ASC");
    }

    /**
     * Vérifie le tri des formations par date en ordre décroissant
     */
    public function testTriDateDesc() {
        $client = static::createClient();
        $crawler = $client->request("GET", "/formations");
        $this->assertResponseIsSuccessful();

        $lien = $crawler->filter("#sortByPublishedAtDesc")->link();
        $crawler = $client->click($lien);
        $this->assertResponseIsSuccessful();

        $premierTitre = $crawler->filter("#formationTitle")->first()->text();
        $this->assertEquals("Cours Informatique embarquée", $premierTitre,
                "la première formation renvoyée n'est pas celle attendue en tri DESC");
    }

    /**
     * Vérifie le filtre des formations par titre
     */
    public function testFiltreParTitre() {
        $client = static::createClient();
        $crawler = $client->request("GET", "/formations");
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter("#btnFilterTitle")->form();
        $form["recherche"]->setValue("python");
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();

        $nbLignes = $crawler->filter('table tbody tr')->count();
        $this->assertEquals(19, $nbLignes,
                "le nombre de formations filtrées ne correspond pas au nombre attendu");

        $premierTitre = $crawler->filter("#formationTitle")->first()->text();
        $this->assertEquals("Python n°18 : Décorateur singleton", $premierTitre,
                "la première formation filtrée ne correspond pas à celle attendue");
    }

    /**
     * Vérifie le filtre des formations par nom de playlist
     */
    public function testFiltreParPlaylist() {
        $client = static::createClient();
        $crawler = $client->request("GET", "/formations");
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter("#btnFilterPlaylistName")->form();
        $form["recherche"]->setValue("Eclipse");
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();

        $nbLignes = $crawler->filter('table tbody tr')->count();
        $this->assertEquals(7, $nbLignes,
                "le nombre de formations filtrées ne correspond pas au nombre attendu");

        $premierTitre = $crawler->filter("#formationTitle")->first()->text();
        $this->assertEquals("Eclipse n°2 : rétroconception avec ObjectAid", $premierTitre,
                "la première formation filtrée ne correspond pas à celle attendue");
    }

    /**
     * Vérifie le filtre des formations par catégorie
     */
    public function testFiltreParCategorie() {
        $client = static::createClient();
        $crawler = $client->request("GET", "/formations");
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter("#selectFilterCategorie")->closest("form")->form();
        $form["recherche"]->setValue("1");
        $crawler = $client->submit($form);
        $this->assertResponseIsSuccessful();

        $nbLignes = $crawler->filter("table tbody tr")->count();
        $this->assertEquals(16, $nbLignes,
                "le nombre de formations filtrées par catégorie ne correspond pas au nombre attendu");

        $premierTitre = $crawler->filter("#formationTitle")->first()->text();
        $this->assertEquals("Cours Informatique embarquée", $premierTitre,
                "la première formation filtrée par catégorie ne correspond pas à celle attendue");
    }

    /**
     * Vérifie que le lien vers le détail d'une formation fonctionne correctement
     */
    public function testLienVersFormation() {
        $client = static::createClient();
        $crawler = $client->request("GET", "/formations");
        $this->assertResponseIsSuccessful();

        $premierTitre = $crawler->filter("#formationTitle")->first()->text();
        $premierePlaylist = $crawler->filter("#formationPlaylistName")->first()->text();
        $premiereDate = $crawler->filter("#formationPublishedAt")->first()->text();

        $lien = $crawler->filter("#btnShowFormation")->first()->link();
        $crawler = $client->click($lien);

        $this->assertResponseIsSuccessful();
        $this->assertEquals(trim($premierTitre), trim($crawler->filter("#formationTitle")->text()),
                "le titre de la formation ne correspond pas");
        $this->assertEquals(trim($premiereDate), trim($crawler->filter("#formationPublishedAt")->text()),
                "la date de la formation ne correspond pas");
        $this->assertEquals(trim($premierePlaylist), trim($crawler->filter("#formationPlaylistName")->text()),
                "la playlist de la formation ne correspond pas");
    }
}
