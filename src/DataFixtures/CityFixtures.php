<?php

namespace App\DataFixtures;

use App\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CityFixtures extends Fixture
{
    public const CITY_COUNT = 10;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::CITY_COUNT; $i++) {
            $city = new City();
            $city->setName($faker->city);
            $city->setZipCode($faker->postcode);

            $manager->persist($city);

            $this->addReference('city_' . $i, $city);
        }

        $manager->flush();
    }
}
