<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use App\Repository\PersonneRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\throwException;

class PersonneController extends AbstractController
{
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="personne")
     */
    public function index(PersonneRepository $personneRepository): Response
    {
        return $this->render('personne/index.html.twig', [
            'personnes' => $personneRepository->findBy([], ['nom' => "ASC"]),
        ]);

    }

    /**
     * @Route("/ajout-personne", name="ajout_personne")
     */
    public function add(Request $request): Response
    {
        $personne = New Personne();

        $form = $this->createForm(PersonneType::class, $personne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $today = new DateTime('NOW');

            if ($personne->getDateNaissance()->diff($today)->format('%y') >= 150) {
                throw new \Exception("La personne doit être agée de moins de 150 ans.");
            }

            $this->entityManager->persist($personne);
            $this->entityManager->flush();

            return $this->redirectToRoute('personne');
        }

        return $this->render('personne/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
