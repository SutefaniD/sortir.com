<?php

namespace App\DataFixtures;

use App\Entity\Status;
use App\Enum\StatusName;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class StatusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach(StatusName::cases() as $statusName) {
            $status = new Status();
            $status->setLabel($statusName);
            $manager->persist($status);

            $this->addReference('status_' . $statusName->value, $status);
        }

        $manager->flush();
    }
}
