<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PinsController extends AbstractController
{
    /**
     * @Route("/", name="app_home", methods={"GET"})
     */
    public function index(PinRepository $pinRepository): Response
    {
        //Browse all pins in the repository by createdAt date
        $allPins = $pinRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('pins/index.html.twig', compact('allPins'));
    }

    /**
     * @Route("/pin/creer", name="app_pin_create", methods={"GET", "POST"})
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
       $form = $this->createFormBuilder(new Pin)
                    ->add('title', TextType::class)
                    ->add('description', TextareaType::class)
                    ->getForm()
        ;
    
        $form->handleRequest($request);
        //If the form is submitted and is valid then:
        if ($form->isSubmitted() && $form->isValid()){
            //Add submitted data to $data
            $data = $form->getData();
            //Create a new pin
            $pin = new Pin();
            //Add title and description
            $pin->setTitle($data['title']);
            $pin->setDescription($data['description']);
            //Add to DB
            $em->persist($pin);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

       return $this->render('pins/create.html.twig', [
           'form' => $form->createView(),
       ]);
    }

    /**
     * @Route("/pin/{id<[0-9]+>}", name="app_pin_show", methods="GET")
     */
    public function show(Pin $pin): Response
    {
       return $this->render('pins/show.html.twig', compact('pin'));
    }
}
