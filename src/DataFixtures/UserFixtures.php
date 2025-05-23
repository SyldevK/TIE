<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('admin@example.fr');
        $user->setNom('Admin');
        $user->setPrenom('Super');
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $user->setIsVerified(true);
        $user->setDateInscription(new \DateTime());

        // Mot de passe hashé
        $password = $this->hasher->hashPassword($user, 'admin1234');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();
    }

    /**
     * Ajoute le groupe "users" à cette fixture.
     */
    public static function getGroups(): array
    {
        return ['users'];
    }
}
