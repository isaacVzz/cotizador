<?php

namespace App\DataFixtures;

use App\Entity\Carrier;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CarrierFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $now = new \DateTimeImmutable();

        $carrier1 = new Carrier();
        $carrier1->setName('Carrier Success');
        $carrier1->setPrice(120.50);
        $carrier1->setDescription('Proveedor que simula respuesta exitosa');
        $carrier1->setActive(true);
        $carrier1->setEndpoint("https://webhook.site/6afc6de3-fdc4-48ae-a924-93e0761d7972");
        $carrier1->setSuccess(true);
        $carrier1->setCreatedAt($now);
        $carrier1->setUpdatedAt($now);

        $manager->persist($carrier1);

        $carrier2 = new Carrier();
        $carrier2->setName('Carrier Fail');
        $carrier2->setPrice(150.00);
        $carrier2->setDescription('Proveedor que simula respuesta con error');
        $carrier2->setActive(true);
        $carrier2->setEndpoint("https://webhook.site/f45cb83d-71d8-4b8b-b22e-1a6e0b75dcea");
        $carrier2->setSuccess(false);
        $carrier2->setCreatedAt($now);
        $carrier2->setUpdatedAt($now);

        $manager->persist($carrier2);

        $manager->flush();
    }
}
