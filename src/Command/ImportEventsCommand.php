<?php

namespace App\Command;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'import:events')]
class ImportEventsCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $eventsData = [
            [
                'titre' => 'Mon prof est un troll / Le cahier magique',
                'description' => "Deux spectacles proposés par les enfants de l'atelier théâtre.\nEncadré par Juliette Dauzet.\nEntrée libre et gratuite.",
                'date' => new \DateTime('2024-06-02 20:00'),
                'lieu' => 'Cintré – Salle théâtre, étage de l’Omnisport',
                'isVisible' => true,
            ],
            [
                'titre' => 'Mon prof est un troll / Le cahier magique',
                'description' => "Deux spectacles proposés par les enfants de l'atelier théâtre.\nEncadré par Juliette Dauzet.\nEntrée libre et gratuite.",
                'date' => new \DateTime('2024-06-03 15:00'),
                'lieu' => 'Cintré – Salle théâtre, étage de l’Omnisport',
                'isVisible' => true,
            ],
            [
                'titre' => 'L’industrie des vœux / Le cheveu aux poudres',
                'description' => "Deux spectacles proposés par les pré-ados et ados de l'atelier théâtre.\nEncadré par Juliette Dauzet.\nEntrée libre et gratuite.",
                'date' => new \DateTime('2023-06-08 20:00'),
                'lieu' => 'Cintré – Salle théâtre, étage de l’Omnisport',
                'isVisible' => true,
            ],
            [
                'titre' => 'L’industrie des vœux / Le cheveu aux poudres',
                'description' => "Deux spectacles proposés par les pré-ados et ados de l'atelier théâtre.\nEncadré par Juliette Dauzet.\nEntrée libre et gratuite.",
                'date' => new \DateTime('2023-06-09 15:00'),
                'lieu' => 'Cintré – Salle théâtre, étage de l’Omnisport',
                'isVisible' => true,
            ],
            [
                'titre' => 'La culotte de Jean ANOUILH / Les enchaînés(de Philippe DORIN)',
                'description' => "Deux spectacles proposés par les enfants, ados et adultes de l'atelier théâtre.\nEncadré par MEVENA PIEL.\nEntrée libre et gratuite.",
                'date' => new \DateTime('2025-06-21 19:30'),
                'lieu' => 'Cintré – Salle théâtre, étage de l’Omnisport',
                'isVisible' => true,
            ],
            [
                'titre' => 'La culotte de Jean ANOUILH / Les enchaînés(de Philippe DORIN)',
                'description' => "Deux spectacles proposés par les enfants, ados et adultes de l'atelier théâtre.\nEncadré par MEVENA PIEL.\nEntrée libre et gratuite.",
                'date' => new \DateTime('2025-06-22 14:30'),
                'lieu' => 'Cintré – Salle théâtre, étage de l’Omnisport',
                'isVisible' => true,
            ],
        ];

        foreach ($eventsData as $data) {
            $event = new Event();
            $event->setTitre($data['titre']);
            $event->setDescription($data['description']);
            $event->setLieu($data['lieu']);
            $event->setIsVisible($data['isVisible']);
            $event->setCreatedAt(new \DateTimeImmutable());

            $this->em->persist($event);
        }

        $this->em->flush();
        $output->writeln('<info>✅ Événements importés avec succès !</info>');

        return Command::SUCCESS;
    }
}
