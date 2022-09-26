<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher )
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $dupont = $this->createAdmin0();

        $manager->persist($dupont);

        $manager->flush();
    }

    private function createAdmin0()
    {
       $admin0 = new User();

       $passwordHashed = $this->hasher->hashPassword($admin0, "azerty1234A#");

       $admin0->setFirstName("Dupont");
       $admin0->setLastName("Jean");
       $admin0->setEmail("joey@gmail.com");
       $admin0->setPassword($passwordHashed);
       $admin0->setRoles(array("ROLE_USER", "ROLE_ADMIN"));
       $admin0->setIsVerified(true);

       return $admin0;
    }
}
