<?php

namespace App\Tests\Repository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Repository\CategorieRepository;
use App\Entity\Categorie;

/**
 * Tests du repository des catégories
 *
 * @author Karl
 */
class CategorieRepositoryTest extends KernelTestCase {

    /**
     * Retourne le repository des catégories
     * @return CategorieRepository
     */
    public function getRepository() {
        self::bootKernel();
        $repository = self::getContainer()->get(CategorieRepository::class);
        return $repository;
    }

    /**
     * Retourne une nouvelle catégorie de test
     * @return Categorie
     */
    public function newCategorie(): Categorie {
        $categorie = (new Categorie())
                ->setName("nom test");
        return $categorie;
    }

    /**
     * Vérifie que le nombre total de catégories correspond au nombre attendu
     */
    public function testNbCategories() {
        $repository = $this->getRepository();
        $nbCategories = $repository->count([]);
        $this->assertEquals(9, $nbCategories);
    }

    /**
     * Vérifie que l'ajout d'une catégorie incrémente bien le compteur
     */
    public function testAddCategorie() {
        $repository = $this->getRepository();
        $categorie = $this->newCategorie();
        $nbCategories = $repository->count([]);
        $repository->add($categorie);
        $this->assertEquals($nbCategories + 1, $repository->count([]),
                "erreur lors de l'ajout : le nombre de catégories n'a pas augmenté de 1");
    }

    /**
     * Vérifie que la suppression d'une catégorie décrémente bien le compteur
     */
    public function testRemoveCategorie() {
        $repository = $this->getRepository();
        $categorie = $this->newCategorie();
        $repository->add($categorie);
        $nbCategories = $repository->count([]);
        $repository->remove($categorie);
        $this->assertEquals($nbCategories - 1, $repository->count([]),
                "erreur lors de la suppression : le nombre de catégories n'a pas diminué de 1");
    }

    /**
     * Vérifie le tri des catégories par nom en ordre croissant
     */
    public function testFindAllOrderByName() {
        $repository = $this->getRepository();
        $categories = $repository->findAllOrderByName("ASC");
        $nbCategories = count($categories);
        $this->assertEquals(9, $nbCategories);
        $this->assertEquals(6, $categories[0]->getId());
    }

    /**
     * Vérifie le tri des catégories par nombre de formations en ordre croissant
     */
    public function testFindAllOrderByFormationsCount() {
        $repository = $this->getRepository();
        $categories = $repository->findAllOrderByFormationsCount("ASC");
        $nbCategories = count($categories);
        $this->assertEquals(9, $nbCategories);
        $this->assertEquals(2, $categories[0]->getId());
    }

    /**
     * Vérifie que les catégories d'une playlist sont bien renvoyées
     */
    public function testFindAllForOnePlaylist() {
        $repository = $this->getRepository();
        $categories = $repository->findAllForOnePlaylist(3);
        $nbCategories = count($categories);
        $this->assertEquals(2, $nbCategories);
        $this->assertEquals(7, $categories[0]->getId());
    }

    /**
     * Vérifie la recherche de catégories dont le nom contient une valeur
     */
    public function testFindByContainValue() {
        $repository = $this->getRepository();
        $categories = $repository->findByContainValue("name", "C");
        $nbCategories = count($categories);
        $this->assertEquals(3, $nbCategories);
        $this->assertEquals(3, $categories[0]->getId());
    }
}
