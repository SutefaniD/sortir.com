<?php

namespace App\DataFixtures;

use App\Entity\Outing;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class OutingFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 25; $i++) {
            $outing = new Outing();
            $outing->setName($faker->name());

            $startingDate = $outing->setStartingDateTime($faker->dateTime());
            $outing->setDuration($faker->randomNumber(3, false));

            $outing->setRegistrationDeadline($faker->dateTimeBetween('-1 year', $startingDate)); // pas sure

            $outing->setMaxParticipant($faker->randomNumber(2, false));
            $outing->setOutingDetails($faker->text(200));

            $manager->persist($outing);

        }

        $manager->flush();
    }
}
