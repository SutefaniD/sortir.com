<?php

namespace App\DataFixtures;

use App\Entity\Site;
use App\Entity\Participant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ParticipantFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $user1 = new Participant();
        $user1->setFirstName("Jane");
        $user1->setLastName("Doe");
        $user1->setUsername("Super Nana");
        $user1->setPhone("06 12 13 14 15");
        $user1->setEmail("jane.doe@mail.com");
        $user1->setPassword(password_hash("password", PASSWORD_BCRYPT));
        $user1->setAdministrator(false);
        $user1->setActive(true);
        $user1->setSite($this->getReference('site_1', Site::class));
        $manager->persist($user1);
        $this->addReference('participant_user', $user1);

        $admin = new Participant();
        $admin->setFirstName("Admin");
        $admin->setLastName("Istrator");
        $admin->setUsername("admin");
        $admin->setPhone("0102030405");
        $admin->setEmail("admin@mail.fr");
        $admin->setPassword(password_hash("password", PASSWORD_BCRYPT));
        $admin->setAdministrator(true);
        $admin->setActive(true);
        $admin->setSite($this->getReference('site_1', Site::class));
        $manager->persist($admin);
        $this->addReference('participant_admin', $admin);


        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 20; $i++) {
            $participant = new Participant();
            $firstName = $faker->firstName();
            $participant->setFirstName($firstName);
            $participant->setLastName($faker->lastName());
            $participant->setUsername($firstName . $i);
            $participant->setPhone($faker->phoneNumber());
            $participant->setEmail($faker->unique()->safeEmail());
            $password = password_hash('password', PASSWORD_BCRYPT);
            $participant->setPassword($password);
            $participant->setAdministrator($faker->boolean(10)); // 10% d'admin
            $participant->setActive($faker->boolean(95)); // 95% actifs

            $siteReference = $this->getReference('site_' . rand(0, count(SiteFixtures::SITE_NAMES) - 1), Site::class);
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
