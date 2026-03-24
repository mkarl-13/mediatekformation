<?php

namespace mediatekformation\tests\Validations;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Formation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use DateTime;

/**
 * Description of FormationValidationsTest
 *
 * @author Karl
 */
class FormationValidationsTest extends KernelTestCase {
    public function getFormation(): Formation{
        return new Formation();
    }
    public function assertErrors(Formation $formation, int $nbErreursAttendues) {
        self::bootKernel();
        $validator = self::getContainer()->get(ValidatorInterface::class);
        $error = $validator->validate($formation);
        $this->assertCount($nbErreursAttendues, $error);
    }
    public function testValidPublishedAtFormation() {
        $formation = $this->getFormation()->setPublishedAt(new DateTime("2026-01-04 17:00:12"));
        $this->assertErrors($formation, 0);
    }
    public function testValidPublishedAtFormationLimite() {
        $formation = $this->getFormation()->setPublishedAt(new DateTime("now"));
        $this->assertErrors($formation, 0);
    }
    public function testInvalidPublishedAtFormation() {
        $formation = $this->getFormation()->setPublishedAt(new DateTime("tomorrow"));
        $this->assertErrors($formation, 1);
    }
}

