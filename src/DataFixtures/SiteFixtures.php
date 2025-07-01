<?php

namespace App\DataFixtures;

use App\Entity\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SiteFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $site = new Site();
            $site->setName($faker->city());
            $manager->persist($site);
            $this->addReference('site_' . $i, $site);
        }

        $manager->flush();
    }
}
