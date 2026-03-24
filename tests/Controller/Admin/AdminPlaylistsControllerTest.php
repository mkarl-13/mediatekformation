<?php

namespace mediatekformation\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of AdminPlaylistsControllerTest
 *
 * @author Karl
 */
class AdminPlaylistsControllerTest extends WebTestCase {
    public function getUser() : User {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(["username" => "admin"]);
        return $user;
    }
    
    public function testAccesPage() {
        $client = static::createClient();
        $client->loginUser($this->getUser());
        
        $client->request("GET","/admin/playlists");
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
    
    public function testTriNomAsc() {
        $client = static::createClient();
        $client->loginUser($this->getUser());
        
        $crawler = $client->request("GET", "/admin/playlists");
        $this->assertResponseIsSuccessful();

        $lien = $crawler->filter("#sortByNameAsc")->link();
        $crawler = $client->click($lien);
        $this->assertResponseIsSuccessful();

        $premierTitre = $crawler->filter("#playlistName")->first()->text();
        $this->assertEquals("Bases de la programmation (C#)", $premierTitre,
                "la première playlist renvoyée n'est pas celle attendue en tri ASC");
    }
    
    public function testTriNomDesc() {
        $client = static::createClient();
        $client->loginUser($this->getUser());
        
        $crawler = $client->request("GET", "/admin/playlists");
        $this->assertResponseIsSuccessful();

        
        $lien = $crawler->filter("#sortByNameDesc")->link();
        $crawler = $client->click($lien);
        $this->assertResponseIsSuccessful();

        $premierTitre = $crawler->filter("#playlistName")->first()->text();
        $this->assertEquals("Visual Studio 2019 et C#", $premierTitre,
                "la première playlist renvoyée n'est pas celle attendue en tri DESC");
    }
    
    public function testTriNombreFormationsAsc() {
        $client = static::createClient();
        $client->loginUser($this->getUser());
        
        $crawler = $client->request("GET", "/admin/playlists");
        $this->assertResponseIsSuccessful();

        $lien = $crawler->filter("#sortByFormationCountAsc")->link();
        $crawler = $client->click($lien);
        $this->assertResponseIsSuccessful();

        $premierTitre = $crawler->filter("#playlistName")->first()->text();
        $this->assertEquals("Cours Merise/2", $premierTitre,
                "la première playlist renvoyée n'est pas celle attendue en tri ASC");
    }
    
    public function testTriNombreFormationsDesc() {
        $client = static::createClient();
        $client->loginUser($this->getUser());
        
        $crawler = $client->request("GET", "/admin/playlists");
        $this->assertResponseIsSuccessful();

        
        $lien = $crawler->filter("#sortByFormationCountDesc")->link();
        $crawler = $client->click($lien);
        $this->assertResponseIsSuccessful();

        $premierTitre = $crawler->filter("#playlistName")->first()->text();
        $this->assertEquals("Bases de la programmation (C#)", $premierTitre,
                "la première playlist renvoyée n'est pas celle attendue en tri DESC");
    }
    
    public function testFiltreParNom() {
        $client = static::createClient();
        $client->loginUser($this->getUser());
        
        $crawler = $client->request("GET", "/admin/playlists");
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
    
    public function testFiltreParCategorie() {
        $client = static::createClient();
        $client->loginUser($this->getUser());
        
        $crawler = $client->request("GET", "/admin/playlists");
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
    
    public function testLienVersPlaylist() {
        $client = static::createClient();
        $client->loginUser($this->getUser());
        
        $crawler = $client->request("GET", "/admin/playlists");
        $this->assertResponseIsSuccessful();
        
        $premierNom = $crawler->filter("#playlistName")->first()->text();
        $premierNombreFormations = $crawler->filter("#playlistFormationCount")->first()->text();

        $lien = $crawler->filter("#btnShowPlaylist")->first()->link();
        $crawler = $client->click($lien);

        $this->assertResponseIsSuccessful();
        $this->assertEquals(trim($premierNom), trim($crawler->filter("#playlistName")->attr("value")),
                "le nom de la playlist ne correspond pas");
        $this->assertEquals(trim($premierNombreFormations), trim($crawler->filter("#playlistFormationCount")->text()),
                "le nombre de formations de la playlist ne correspond pas");
    }
    
    public function testLienCreerPlaylist() {
        $client = static::createClient();
        $client->loginUser($this->getUser());

        $crawler = $client->request("GET", "/admin/playlists");
        $this->assertResponseIsSuccessful();

        $lien = $crawler->filter("#btnNewPlaylist")->link();
        $crawler = $client->click($lien);
        $this->assertResponseIsSuccessful();

        $this->assertEquals("", trim($crawler->filter("#playlistName")->attr("value")),
                "le champ nom devrait être vide");
        $this->assertEquals("", trim($crawler->filter("#playlistFormationCount")->text()),
                "aucune formation ne devrait être affichée");
    }
}
