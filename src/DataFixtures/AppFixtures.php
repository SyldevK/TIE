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
            $user->setEmail($faker->unique()->email());
            $user->setPassword($this->passwordHasher->hashPassword($user, 'test1234'));
            $user->setRoles(['ROLE_USER']);
            $user->setDateInscription(new \DateTime());
            $user->setIsVerified(true);
            $manager->persist($user);
            $users[] = $user;
        }
        $manager->flush();
        echo "✅ Utilisateurs enregistrés.\n";

        // Événements et Dates
        $dates = [];
        $eventInfos = [
            [
                'titre' => "L'industrie des voeux / Le cheveu aux poudres",
                'image' => 'affiche2023.png',
                'dates' => ['2023-06-08 20:00', '2023-06-09 15:00']
            ],
            [
                'titre' => "Mon prof est un troll / Le cahier magique",
                'image' => 'affiche2024.png',
                'dates' => ['2024-06-02 20:00', '2024-06-03 15:00']
            ],
            [
                'titre' => "La culotte de Jean ANOUILH / Les enchaînés",
                'image' => 'affiche2025.jpg',
                'dates' => ['2025-06-21 19:30', '2025-06-22 14:30']
            ],
        ];

        foreach ($eventInfos as $info) {
            $event = new Event();
            $event->setTitre($info['titre'])
                ->setDescription("Spectacle")
                ->setLieu("Cintré – Omnisport")
                ->setImageUrl($info['image'])
                ->setIsVisible(true)
                ->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($event);

            foreach ($info['dates'] as $dateStr) {
                $date = new EventDate();
                $date->setDatetime(new \DateTime($dateStr));
                $date->setEvent($event);
                $manager->persist($date);
                $dates[] = $date;
            }
        }
        $manager->flush();

        // Participant + Enrollment (avec lien User correctement fait via ->addEnrollment)
        $participant = new Participant();
        $participant->setPrenom($faker->firstName());
        $participant->setNom($faker->lastName());
        $participant->setDateNaissance(new \DateTime($faker->dateTimeBetween('-15 years', '-7 years')->format('Y-m-d')));

        $enrollment = new Enrollment();
        $enrollment->setGroupe('Ados');
        $enrollment->setIsActive(true);
        $enrollment->setAnneeScolaire('2024-2025');
        $enrollment->setParticipant($participant);

        $randomUser = $users[array_rand($users)];
        $randomUser->addEnrollment($enrollment); // bonne relation

        $manager->persist($participant);
        $manager->persist($enrollment);
        $manager->flush();

        // Réservations
        for ($i = 0; $i < 5; $i++) {
            $reservation = new Reservation();
            $reservation->setNombrePlaces(rand(1, 4));
            $reservation->setDateReservation(new \DateTimeImmutable($faker->dateTimeBetween('-2 weeks', 'now')->format('Y-m-d H:i:s')));

            $user = $users[array_rand($users)];
            $eventDate = $dates[array_rand($dates)];
            $event = $eventDate->getEvent();

            $reservation->setUser($user);
            $reservation->setEvent($event);
            $reservation->setEventDate($eventDate);

            $manager->persist($reservation);
        }
        $manager->flush();

        echo "✅ Utilisateurs, événements, dates, inscriptions et réservations bien enregistrés.\n";
    }
}
