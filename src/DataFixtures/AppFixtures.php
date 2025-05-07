<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Event;
use App\Entity\Participant;
use App\Entity\Enrollment;
use App\Entity\Reservation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $users = [];
        // Création de 5 utilisateurs fictifs
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setNom($faker->lastName());
            $user->setPrenom($faker->firstName());
            $user->setEmail($faker->email());
            $user->setPassword($this->passwordHasher->hashPassword($user, 'test1234'));
            $user->setRoles(['ROLE_USER']);
            $user->setDateInscription(new \DateTime($faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s')));
            $user->setIsVerified(true);
            $user->setVerificationToken(null);

            $manager->persist($user);
            $users[] = $user;
        }
        // -------- Création des événements --------
        $events = [];
        for ($i = 0; $i < 3; $i++) {
            $event = new Event();
            $event->setTitre($faker->sentence(4));
            $event->setDescription($faker->paragraph());
            $event->setDateEvent($faker->dateTimeBetween('+1 week', '+2 months'));
            $event->setLieu($faker->city());
            $event->setImageUrl('/uploads/images/affiche2024.png');
            $event->setCreatedAt(new \DateTimeImmutable());
            $event->setIsVisible(true);

            $manager->persist($event);
            $events[] = $event;
        }

        // -------- Participants + Enrollments --------
        for ($i = 0; $i < 5; $i++) {
            $participant = new Participant();
            $participant->setPrenom($faker->firstName());
            $participant->setNom($faker->lastName());
            $participant->setDateNaissance(new \DateTime($faker->dateTimeBetween('-15 years', '-7 years')->format('Y-m-d')));

            $enrollment = new Enrollment();
            $enrollment->setGroupe('Ados');
            $enrollment->setIsActive(true);
            $enrollment->setAnneeScolaire('2024-2025');
            $randomUser = $users[array_rand($users)];
            $enrollment->setUser($randomUser);
            $enrollment->setParticipant($participant);
            $participant->setEnrollment($enrollment);

            $manager->persist($participant);
            $manager->persist($enrollment);
        }

        // -------- Création des réservations --------
        for ($i = 0; $i < 5; $i++) {
            $reservation = new Reservation();
            $reservation->setNombrePlaces(rand(1, 4));
            $reservation->setDateReservation(new \DateTimeImmutable($faker->dateTimeBetween('-2 weeks', 'now')->format('Y-m-d H:i:s')));

            $user = $users[array_rand($users)];
            $event = $events[array_rand($events)];

            $reservation->setUser($user);
            $reservation->setEvent($event);

            $manager->persist($reservation);
        }

        $manager->flush();
    }
}
