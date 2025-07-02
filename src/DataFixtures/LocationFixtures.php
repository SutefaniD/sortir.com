<?php

namespace App\DataFixtures;

use App\Entity\Location;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LocationFixtures extends Fixture
{
    public const LOCATION_COUNT = 15;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::LOCATION_COUNT; $i++) {
            $location = new Location();
            $location->setName($faker->company);
            $location->setStreet($faker->streetAddress);

            $cityReference = $this->getReference('city_' . rand(0, CityFixtures::CITY_COUNT - 1), CityFixtures::class);
            $location->setCity($cityReference);

            $manager->persist($location);
            $this->addReference('location_' . $i, $location);
        }

        $manager->flush();
    }
}
