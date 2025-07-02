<?php

namespace App\DataFixtures;

use App\Entity\Location;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LocationFixtures extends Fixture implements DependentFixtureInterface
{
    public const LOCATION_COUNT = 15;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 15; $i++) {
            $location = new Location();

            $cityReference = $this->getReference('city_' . rand(0, CityFixtures::CITY_COUNT - 1), CityFixtures::class);
            $location->setCity($cityReference);

            $location->setName($faker->company);
            $location->setStreet($faker->streetAddress);

            $manager->persist($location);
            $this->addReference('location_' . $i, $location);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CityFixtures::class,
        ];
    }
}
