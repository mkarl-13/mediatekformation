<?php

namespace App\Tests\Validations;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Formation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use DateTime;

/**
 * Tests de validation des contraintes de l'entité Formation
 *
 * @author Karl
 */
class FormationValidationsTest extends KernelTestCase {

    /**
     * Retourne une nouvelle instance de Formation vide
     * @return Formation
     */
    public function getFormation(): Formation{
        return new Formation();
    }

    /**
     * Vérifie que le nombre d'erreurs de validation correspond au nombre attendu
     * @param Formation $formation
     * @param int $nbErreursAttendues
     */
    public function assertErrors(Formation $formation, int $nbErreursAttendues) {
        self::bootKernel();
        $validator = self::getContainer()->get(ValidatorInterface::class);
        $error = $validator->validate($formation);
        $this->assertCount($nbErreursAttendues, $error);
    }

    /**
     * Vérifie qu'une date passée est valide
     */
    public function testValidPublishedAtFormation() {
        $formation = $this->getFormation()->setPublishedAt(new DateTime("2026-01-04 17:00:12"));
        $this->assertErrors($formation, 0);
    }

    /**
     * Vérifie que la date du jour est acceptée (valeur limite)
     */
    public function testValidPublishedAtFormationLimite() {
        $formation = $this->getFormation()->setPublishedAt(new DateTime("now"));
        $this->assertErrors($formation, 0);
    }

    /**
     * Vérifie qu'une date future est rejetée
     */
    public function testInvalidPublishedAtFormation() {
        $formation = $this->getFormation()->setPublishedAt(new DateTime("tomorrow"));
        $this->assertErrors($formation, 1);
    }
}
