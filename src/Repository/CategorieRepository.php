<?php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Accès aux données des catégories
 *
 * @extends ServiceEntityRepository<Categorie>
 * @author Karl
 */
class CategorieRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

    /**
     * Persiste et enregistre une catégorie en base de données
     * @param Categorie $entity
     */
    public function add(Categorie $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Supprime une catégorie de la base de données
     * @param Categorie $entity
     */
    public function remove(Categorie $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Retourne toutes les catégories triées sur le nom de la catégorie
     * @param type $ordre
     * @return Categorie[]
     */
    public function findAllOrderByName($ordre): array {
        return $this->createQueryBuilder('c')
                        ->orderBy('c.name', $ordre)
                        ->getQuery()
                        ->getResult();
    }

    /**
     * Retourne toutes les catégories triées sur le nombre de formations
     * @param string $ordre
     * @return Categorie[]
     */
    public function findAllOrderByFormationsCount(string $ordre): array {
        return $this->createQueryBuilder('c')
            ->select('c, COUNT(f.id) AS HIDDEN formationCount')
            ->leftjoin('c.formations', 'f')
            ->groupBy('c.id')
            ->orderBy('formationCount', $ordre)
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne la liste des catégories des formations d'une playlist
     * @param type $idPlaylist
     * @return Categorie[]
     */
    public function findAllForOnePlaylist($idPlaylist): array{
        return $this->createQueryBuilder('c')
                ->join('c.formations', 'f')
                ->join('f.playlist', 'p')
                ->where('p.id=:id')
                ->setParameter('id', $idPlaylist)
                ->orderBy('c.name', 'ASC')
                ->getQuery()
                ->getResult();
    }

    /**
     * Enregistrements dont un champ contient une valeur
     * ou tous les enregistrements si la valeur est vide
     * @param type $champ
     * @param type $valeur
     * @return Categorie[]
     */
    public function findByContainValue($champ, $valeur): array {
        if ($valeur == "") {
            return $this->findAllOrderByName('ASC');
        }
        return $this->createQueryBuilder('c')
                        ->where('c.' . $champ . ' LIKE :valeur')
                        ->setParameter('valeur', '%' . $valeur . '%')
                        ->orderBy('c.name', 'ASC')
                        ->getQuery()
                        ->getResult();
    }
}
