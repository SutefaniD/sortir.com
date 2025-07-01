<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ParticipantFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $participant = new Participant();
        $participant->setLastName('Smith');
        $participant->setFirstName('John');
        $participant->setPhone("0102030405");
        $participant->setEmail("j.smith@mail.fr");
        $participant->setAdministrator(0);
        $participant->setActive(1);

        $participant2 = new Participant();
        $participant2->setLastName('Doe');
        $participant2->setFirstName('Jane');
        $participant2->setPhone("0502030401");
        $participant2->setEmail("jane.doe@mail.fr");
        $participant2->setAdministrator(0);
        $participant2->setActive(1);

        $participant3 = new Participant();
        $participant3->setLastName('Smith');
        $participant3->setFirstName('Will');
        $participant3->setPhone("0234567865");
        $participant3->setEmail("wsmith@mail.fr");
        $participant3->setAdministrator(1);
        $participant3->setActive(1);


        $manager->persist($participant);
        $manager->persist($participant2);
        $manager->persist($participant3);

        $manager->flush();
    }
}
