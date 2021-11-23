<?php

namespace App\Controller;

use App\Entity\Pin;
use DateTimeImmutable;
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

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
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
    public function create(Request $request): Response
    {
        //Create a new pin
        $pin = new Pin;
        $form = $this->createFormBuilder($pin)
                        ->add('title', TextType::class)
                        ->add('description', TextareaType::class)
                        ->getForm()
        ;
    
        $form->handleRequest($request);
        //If the form is submitted and is valid then:
        if ($form->isSubmitted() && $form->isValid()){           
            //Add createdAt            
            $pin->setCreatedAt(new DateTimeImmutable);
            //Add to DB
            $em->persist($pin);
            $em->flush();
            //Redirect to homepage
            return $this->redirectToRoute('app_home');
        }

       return $this->render('pins/create.html.twig', [
           'form' => $form->createView(),
       ]);
    }

    /**
     * @Route("/pin/{id<[0-9]+>}", name="app_pin_read", methods={"GET"})
     */
    public function read(Pin $pin): Response
    {
       return $this->render('pins/read.html.twig', compact('pin'));
    }

    /**
     * @Route("/pin/editer/{id<[0-9]+>}", name="app_pin_edit", methods={"GET", "POST"})
     */
    public function edit(Pin $pin, Request $request): Response
    {
        $form = $this->createFormBuilder($pin)
                    ->add('title', TextType::class)
                    ->add('description', TextareaType::class)
                    ->getForm()
        ;

        $form->handleRequest($request); 

        if ($form->isSubmitted() && $form->isValid()){  
            $pin->setUpdatedAt(new DateTimeImmutable);
            $em->flush();
            return $this->redirectToRoute('app_home');
        }
       return $this->render('pins/edit.html.twig', [
           'pin' => $pin,
           'form' => $form->createView(),
       ]);
    }
}
