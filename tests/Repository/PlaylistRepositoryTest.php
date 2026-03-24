<?php

namespace App\Tests\Repository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Repository\PlaylistRepository;
use App\Entity\Playlist;

/**
 * Tests du repository des playlists
 *
 * @author Karl
 */
class PlaylistRepositoryTest extends KernelTestCase {

    /**
     * Retourne le repository des playlists
     * @return PlaylistRepository
     */
    public function getRepository() {
        self::bootKernel();
        $repository = self::getContainer()->get(PlaylistRepository::class);
        return $repository;
    }

    /**
     * Retourne une nouvelle playlist de test
     * @return Playlist
     */
    public function newPlaylist(): Playlist {
        $playlist = (new Playlist())
                ->setName("titre test")
                ->setDescription("description test");
        return $playlist;
    }

    /**
     * Vérifie que le nombre total de playlists correspond au nombre attendu
     */
    public function testNbPlaylists() {
        $repository = $this->getRepository();
        $nbPlaylists = $repository->count([]);
        $this->assertEquals(26, $nbPlaylists);
    }

    /**
     * Vérifie que l'ajout d'une playlist incrémente bien le compteur
     */
    public function testAddPlaylist() {
        $repository = $this->getRepository();
        $playlist = $this->newPlaylist();
        $nbPlaylists = $repository->count([]);
        $repository->add($playlist);
        $this->assertEquals($nbPlaylists + 1, $repository->count([]),
                "erreur lors de l'ajout : le nombre de playlists n'a pas augmenté de 1");
    }

    /**
     * Vérifie que la suppression d'une playlist décrémente bien le compteur
     */
    public function testRemovePlaylist() {
        $repository = $this->getRepository();
        $playlist = $this->newPlaylist();
        $repository->add($playlist);
        $nbPlaylists = $repository->count([]);
        $repository->remove($playlist);
        $this->assertEquals($nbPlaylists - 1, $repository->count([]),
                "erreur lors de la suppression : le nombre de playlists n'a pas diminué de 1");
    }

    /**
     * Vérifie le tri des playlists par nom en ordre croissant
     */
    public function testFindAllOrderByName() {
        $repository = $this->getRepository();
        $playlists = $repository->findAllOrderByName("ASC");
        $nbPlaylists = count($playlists);
        $this->assertEquals(26, $nbPlaylists);
        $this->assertEquals(13, $playlists[0]->getId());
    }

    /**
     * Vérifie le tri des playlists par nombre de formations en ordre croissant
     */
    public function testFindAllOrderByFormationsCount() {
        $repository = $this->getRepository();
        $playlists = $repository->findAllOrderByFormationsCount("ASC");
        $nbPlaylists = count($playlists);
        $this->assertEquals(26, $nbPlaylists);
        $this->assertEquals(25, $playlists[0]->getId());
    }

    /**
     * Vérifie la recherche de playlists dont le nom contient une valeur
     */
    public function testFindByContainValue() {
        $repository = $this->getRepository();
        $playlists = $repository->findByContainValue("name", "Sujet");
        $nbPlaylists = count($playlists);
        $this->assertEquals(8, $nbPlaylists);
        $this->assertEquals(15, $playlists[0]->getId());
    }
}
