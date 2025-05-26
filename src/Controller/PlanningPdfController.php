<?php

namespace App\Controller;

use App\Repository\PlanningCoursRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class PlanningPdfController extends AbstractController
{
    #[Route('/admin/generer-planning-pdf', name: 'generate_planning_pdf')]
    public function generatePdf(PlanningCoursRepository $repository, Environment $twig): Response
    {
        $cours = $repository->findAll();
        $html = $twig->render('pdf/planning.html.twig', [
            'cours' => $cours,
        ]);


        $options = new Options();
        $options->set('defaultFont', 'Poppins');
        $options->setIsRemoteEnabled(true);
        $options->set('chroot', $this->getParameter('kernel.project_dir') . '/public');


        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Sauvegarde dans /public/pdf/planning.pdf
        file_put_contents(
            $this->getParameter('kernel.project_dir') . '/public/pdf/planning.pdf',
            $dompdf->output()
        );

        $this->addFlash('success', 'Le planning a été généré avec succès.');

        return $this->redirectToRoute('admin');
    }
}
