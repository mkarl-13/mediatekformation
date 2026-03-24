<?php

namespace App\Tests\Repository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Repository\FormationRepository;
use App\Entity\Formation;
use DateTime;

/**
 * Tests du repository des formations
 *
 * @author Karl
 */
class FormationRepositoryTest extends KernelTestCase {

    /**
     * Retourne le repository des formations
     * @return FormationRepository
     */
    public function getRepository() {
        self::bootKernel();
        $repository = self::getContainer()->get(FormationRepository::class);
        return $repository;
    }

    /**
     * Retourne une nouvelle formation de test
     * @return Formation
     */
    public function newFormation(): Formation {
        $formation = (new Formation())
                ->setTitle("titre test")
                ->setDescription("description test")
                ->setVideoId("videoId test")
                ->setPublishedAt(new DateTime("2026-01-04 17:00:12"));
        return $formation;
    }

    /**
     * Vérifie que le nombre total de formations correspond au nombre attendu
     */
    public function testNbFormations() {
        $repository = $this->getRepository();
        $nbFormations = $repository->count([]);
        $this->assertEquals(237, $nbFormations);
    }

    /**
     * Vérifie que l'ajout d'une formation incrémente bien le compteur
     */
    public function testAddFormation() {
        $repository = $this->getRepository();
        $formation = $this->newFormation();
        $nbFormations = $repository->count([]);
        $repository->add($formation);
        $this->assertEquals($nbFormations + 1, $repository->count([]),
                "erreur lors de l'ajout : le nombre de formations n'a pas augmenté de 1");
    }

    /**
     * Vérifie que la suppression d'une formation décrémente bien le compteur
     */
    public function testRemoveFormation() {
        $repository = $this->getRepository();
        $formation = $this->newFormation();
        $repository->add($formation);
        $nbFormations = $repository->count([]);
        $repository->remove($formation);
        $this->assertEquals($nbFormations - 1, $repository->count([]),
                "erreur lors de la suppression : le nombre de formations n'a pas diminué de 1");
    }

    /**
     * Vérifie le tri des formations par titre en ordre croissant
     */
    public function testFindAllOrderBy() {
        $repository = $this->getRepository();
        $formations = $repository->findAllOrderBy("title", "ASC");
        $nbFormations = count($formations);
        $this->assertEquals(237, $nbFormations,
                "le nombre de formations renvoyé ne correspond pas au nombre attendu");
        $this->assertEquals(89, $formations[0]->getId(),
                "la première formation renvoyée n'est pas celle attendue en tri ASC");
    }

    /**
     * Vérifie la recherche de formations dont le titre contient une valeur
     */
    public function testFindByContainValue() {
        $repository = $this->getRepository();
        $formations = $repository->findByContainValue("title", "python");
        $nbFormations = count($formations);
        $this->assertEquals(19, $nbFormations,
                "le nombre de formations contenant 'python' ne correspond pas au nombre attendu");
        $this->assertEquals(25, $formations[0]->getId(),
                "la première formation renvoyée ne correspond pas à celle attendue");
    }

    /**
     * Vérifie que les n formations les plus récentes sont bien renvoyées
     */
    public function testFindAllLasted() {
        $repository = $this->getRepository();
        $formations = $repository->findAllLasted(3);
        $nbFormations = count($formations);
        $this->assertEquals(3, $nbFormations,
                "le nombre de formations renvoyé ne correspond pas au nombre demandé");
        $this->assertEquals(234, $formations[0]->getId(),
                "la formation la plus récente renvoyée n'est pas celle attendue");
    }

    /**
     * Vérifie que les formations d'une playlist sont bien renvoyées
     */
    public function testFindAllForOnePlaylist() {
        $repository = $this->getRepository();
        $formations = $repository->findAllForOnePlaylist(1);
        $nbFormations = count($formations);
        $this->assertEquals(7, $nbFormations,
                "le nombre de formations pour la playlist 1 ne correspond pas au nombre attendu");
        $this->assertEquals(8, $formations[0]->getId(),
                "la première formation renvoyée pour la playlist 1 n'est pas celle attendue");
    }
}
