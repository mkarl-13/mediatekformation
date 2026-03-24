<?php

namespace App\Tests;
use PHPUnit\Framework\TestCase;
use App\Entity\Formation;
use DateTime;

/**
 * Tests unitaires de l'entité Formation
 *
 * @author Karl
 */
class FormationTest extends TestCase {

    /**
     * Vérifie que la date de publication est bien formatée en j/m/Y
     */
    public function testGetPublishedAtString() {
        $formation = new Formation();
        $formation->setPublishedAt(new DateTime("2026-01-04 17:00:12"));
        $this->assertEquals("04/01/2026", $formation->getPublishedAtString());
    }
}
