<?php

namespace App\DataFixtures;

use App\Entity\Outing;
use App\Entity\Status;
use App\Enum\StatusName;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class OutingFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $now = new DateTime();
        $statusOngoing = (new Status())->setLabel(StatusName::ONGOING);
        $statusPast = (new Status())->setLabel(StatusName::PAST);
        $manager->persist($statusOngoing);
        $manager->persist($statusPast);

        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 20; $i++) {
            $outing = new Outing();

            $outing->setName($faker->sentence(3));
            $startDate = (new \DateTime())->modify(rand(-10, 10) . ' days');
            $outing->setStartingDateTime($startDate);
            $outing->setDuration($faker->numberBetween(1, 8)); // durée en heures
            $outing->setRegistrationDeadline((clone $startDate)->modify('-1 days'));
            $outing->setMaxParticipants(rand(3, 100));
            $outing->setOutingDetails($faker->text(100));

            // pour le moment que 2 statuts
            if ($startDate > $now) {
                $outing->setStatus($statusOngoing);
            } else {
                $outing->setStatus($statusPast);
            }

            $organizerIndex = rand(0,19);
            $organizer = $this->getReference('participant_' . $organizerIndex);
            $outing->setOrganizer($organizer);

            $possibleParticipants = range(0, 19);
            unset($possibleParticipants[$organizerIndex]); // enlève l'organisateur de la liste
            $possibleParticipants = array_values($possibleParticipants); // Réindexe le tableau pour éviter des trous dans les clés

            $participantsRegistered = rand(2, 10);
            // attention, array_rand renvoie un int si 1 élément, un tableau si >= 2
            $participantsIndex = (array)array_rand($possibleParticipants, $participantsRegistered);

            foreach ($participantsIndex as $index) {
                $participant = $this->getReference('participant_' . $possibleParticipants[$index]);
                $outing->addParticipant($participant);
            }

            $siteReference = $this->getReference('site_' . rand(0, count(SiteFixtures::SITE_NAMES) - 1));
            $outing->setSite($siteReference);

            $locationReference = $this->getReference('location_' . rand(0, LocationFixtures::LOCATION_COUNT - 1));
            $outing->setLocation($locationReference);


            $manager->persist($outing);
            $this->addReference('outing_' . $i, $outing);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [SiteFixtures::class, LocationFixtures::class, ParticipantFixtures::class];
    }

}
