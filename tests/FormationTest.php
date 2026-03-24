<?php

namespace mediatekformation\tests;
use PHPUnit\Framework\TestCase;
use App\Entity\Formation;
use DateTime;

/**
 * Description of FormationTest
 *
 * @author Karl
 */
class FormationTest extends TestCase {
    public function testGetPublishedAtString() {
        $formation = new Formation();
        $formation->setPublishedAt(new DateTime("2026-01-04 17:00:12"));
        $this->assertEquals("04/01/2026", $formation->getPublishedAtString());
    }
}
