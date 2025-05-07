<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\EventDate;
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

        // Utilisateurs
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

        // === Événements et Dates ===
        $events = [];

        $event1 = new Event();
        $event1->setTitre("L'industrie des voeux / Le cheveu aux poudres")
            ->setDescription("Deux spectacles proposés par les enfants de l'atelier théâtre.\nEncadré par Juliette Douzet.\nEntrée libre et gratuite.")
            ->setLieu("Cintré – Salle théâtre, étage de l’Omnisport")
            ->setImageUrl('/uploads/images/affiche2023.png')
            ->setIsVisible(true)
            ->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($event1);
        $events[] = $event1;

        $d1a = new EventDate();
        $d1a->setDatetime(new \DateTime('2023-06-08 20:00'))->setEvent($event1);
        $manager->persist($d1a);

        $d1b = new EventDate();
        $d1b->setDatetime(new \DateTime('2023-06-09 15:00'))->setEvent($event1);
        $manager->persist($d1b);

        $event2 = new Event();
        $event2->setTitre('Mon prof est un troll / Le cahier magique')
            ->setDescription("Deux spectacles proposés par les enfants de l'atelier théâtre.\nDes textes de Juliette Douzet.\nEntrée libre et gratuite.")
            ->setLieu("Cintré – Salle théâtre, étage de l’Omnisport")
            ->setImageUrl('/uploads/images/affiche2024.png')
            ->setIsVisible(true)
            ->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($event2);
        $events[] = $event2;

        $d2a = new EventDate();
        $d2a->setDatetime(new \DateTime('2024-06-02 20:00'))->setEvent($event2);
        $manager->persist($d2a);

        $d2b = new EventDate();
        $d2b->setDatetime(new \DateTime('2024-06-03 15:00'))->setEvent($event2);
        $manager->persist($d2b);

        $event3 = new Event();
        $event3->setTitre("La culotte de Jean ANOUILH / Les enchaînés (de Philippe DORIN)")
            ->setDescription("Deux spectacles proposés par les enfants, ados et adultes de l’atelier théâtre.\nEncadré par MEVENA PIEL.\nEntrée libre et gratuite.")
            ->setLieu("Cintré – Salle théâtre, étage de l’Omnisport")
            ->setImageUrl('/uploads/images/affiche2025.jpg')
            ->setIsVisible(true)
            ->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($event3);
        $events[] = $event3;

        $d3a = new EventDate();
        $d3a->setDatetime(new \DateTime('2025-06-21 19:30'))->setEvent($event3);
        $manager->persist($d3a);

        $d3b = new EventDate();
        $d3b->setDatetime(new \DateTime('2025-06-22 14:30'))->setEvent($event3);
        $manager->persist($d3b);

        // Participants et inscriptions
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

        // Réservations
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
