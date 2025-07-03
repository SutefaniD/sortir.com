<?php

namespace App\DataFixtures;

use App\Entity\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SiteFixtures extends Fixture
{
    public const SITE_NAMES = ['Paris', 'Quimper', 'Saint Herblain', 'Chartres de Bretagne', 'La Roche sur Yon'];

    public function load(ObjectManager $manager): void
    {
        foreach (self::SITE_NAMES as $key => $name) {
            $site = new Site();
            $site->setName($name);

            $manager->persist($site);
            $this->addReference('site_' . $key, $site);
        }

        $manager->flush();
    }
}
