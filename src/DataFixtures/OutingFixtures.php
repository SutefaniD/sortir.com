<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Location;
use App\Entity\Outing;
use App\Entity\Status;
use App\Enum\StatusName;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class OutingFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $statusValues = [
            StatusName::CREATED,
            StatusName::ONGOING,
            StatusName::PAST,
            StatusName::CANCELLED,
        ];

        $statuses = [];
        foreach ($statusValues as $statusName) {
            $status = new Status();
            $status->setLabel($statusName);
            $manager->persist($status);
            $statuses[] = $status;
        }

        $cities = [];
        for ($i = 0; $i < 5; $i++) {
            $city = new City();
            $city->setName($faker->city());
            $city->setZipCode($faker->postcode());
            $manager->persist($city);
            $cities[] = $city;
        }

        $locations = [];
        for ($i = 0; $i < 10; $i++) {
            $location = new Location();
            $location->setName($faker->text(30));
            $location->setStreet($faker->streetName());
            $location->setCity($cities[array_rand($cities)]);
            $manager->persist($location);
            $locations[] = $location;
        }

        $sites = [];
        for ($i = 0; $i < 3; $i++) {
            $site = new Site();
            $site->setName($faker->city());
            $manager->persist($site);
            $sites[] = $site;
        }

        for ($i = 0; $i < 25; $i++) {
            $outing = new Outing();
            $outing->setName($faker->sentence());
            $startingDate = $faker->dateTimeBetween('-6 months', '+6 months');
            $outing->setStartingDateTime($startingDate);
            $outing->setDuration($faker->randomNumber(3, false));
            $outing->setRegistrationDeadline($faker->dateTimeBetween('-1 year', $startingDate)); // pas sure
            $outing->setMaxParticipants($faker->randomNumber(2, false));
            $outing->setOutingDetails($faker->text(200));

            $site = $this->getReference(('site_' . rand(0, 9)));
            $outing->setSite($site);

            $outing->setStatus($statuses[array_rand($statuses)]);
            $outing->setLocation($locations[array_rand($locations)]);

            $manager->persist($outing);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            SiteFixtures::class,
        ];
    }
}
