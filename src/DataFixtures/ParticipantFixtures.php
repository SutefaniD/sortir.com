<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ParticipantFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 20; $i++) {
            $participant = new Participant();
            $participant->setFirstName($faker->firstName());
            $participant->setLastName($faker->lastName());
            $participant->setPhone($faker->phoneNumber());
            $participant->setEmail($faker->unique()->safeEmail());
            $password = password_hash('password', PASSWORD_BCRYPT);
            $participant->setPassword($password);
            $participant->setAdministrator($faker->boolean(10)); // 10% d'admin
            $participant->setActive($faker->boolean(80)); // 80% actifs

            $siteReference = $this->getReference('site_' . rand(0, count(SiteFixtures::SITE_NAMES) - 1), SiteFixtures::class);
            $participant->setSite($siteReference);

            $manager->persist($participant);

            $this->addReference('participant_' . $i, $participant);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SiteFixtures::class
        ];
    }
}
