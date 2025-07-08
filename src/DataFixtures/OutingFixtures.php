<?php

namespace App\DataFixtures;

use App\Entity\Location;
use App\Entity\Participant;
use App\Entity\Outing;
use App\Entity\Site;
use App\Entity\Status;
use App\Enum\StatusName;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class OutingFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $now = new DateTime();
        $faker = Factory::create('fr_FR');

        $statuses = [];
        foreach(StatusName::cases() as $statusName) {
            $statuses[$statusName->value] = $this->getReference('status_' .$statusName->value, Status::class);
        }

        for ($i = 0; $i < 50; $i++) {
            $outing = new Outing();

            $outing->setName($faker->sentence(3));
            $startDate = (new \DateTime())->modify(rand(-10, 10) . ' days');
            $outing->setStartingDateTime($startDate);
            $outing->setDuration($faker->numberBetween(30, 1440)); // durée en minutes
            $outing->setRegistrationDeadline((clone $startDate)->modify('-1 days'));
            $outing->setMaxParticipants(rand(3, 100));
            $outing->setOutingDetails($faker->text(100));

            // Choix du status
            // Si date future : à 70% OPENED, sinon 20% CREATED, 5% CLOSED, 5% CANCELLED
            // Si date passée : à 80% PAST, sinon 20% CANCELLED
            if ($startDate == $now) {
                // Cas exact : maintenant
                $outing->setStatus($statuses[StatusName::ONGOING->value]);

            } elseif ($startDate > $now) {
                $rand = $faker->randomFloat(2, 0, 1);
                if ($rand <= 0.7) {
                    $outing->setStatus($statuses[StatusName::OPENED->value]);
                } else if ($rand <= 0.9) {
                    $outing->setStatus($statuses[StatusName::CREATED->value]);
                } elseif ($rand <= 0.95) {
                    $outing->setStatus($statuses[StatusName::CLOSED->value]);
                } else {
                    $outing->setStatus($statuses[StatusName::CANCELLED->value]);
                    $outing->setCancelReason($faker->sentence());
                }
            } else {
                $rand = $faker->randomFloat(2, 0, 1);
                if ($rand <= 0.8) {
                    $outing->setStatus($statuses[StatusName::PAST->value]);
                } else {
                    $outing->setStatus($statuses[StatusName::CANCELLED->value]);
                    $outing->setCancelReason($faker->sentence());
                }
            }

            $organizerIndex = rand(0,19);
            $organizer = $this->getReference('participant_' . $organizerIndex, Participant::class);
            $outing->setOrganizer($organizer);

            $possibleParticipants = range(0, 19);
            unset($possibleParticipants[$organizerIndex]); // enlève l'organisateur de la liste
            $possibleParticipants = array_values($possibleParticipants); // Réindexe le tableau pour éviter des trous dans les clés

            $participantsRegistered = rand(2, 10);
            // attention, array_rand renvoie un int si 1 élément, un tableau si >= 2
            $participantsIndex = (array)array_rand($possibleParticipants, $participantsRegistered);

            foreach ($participantsIndex as $index) {
                $participant = $this->getReference('participant_' . $possibleParticipants[$index], Participant::class);
                $outing->addParticipant($participant);
            }

            $locationReference = $this->getReference('location_' . rand(0, LocationFixtures::LOCATION_COUNT - 1), Location::class);
            $outing->setLocation($locationReference);

            $siteReference = $this->getReference('site_' . rand(0, count(SiteFixtures::SITE_NAMES) - 1), Site::class);
            $outing->setSite($siteReference);

            $manager->persist($outing);
            $this->addReference('outing_' . $i, $outing);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ParticipantFixtures::class,
            LocationFixtures::class,
            SiteFixtures::class,
            StatusFixtures::class
        ];
    }
}
