<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use DateTimeImmutable;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
        //Create a new pin
        $pin = new Pin;
        //Calls PinType form
        $form = $this->createForm(PinType::class, $pin);
    
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
     * @Route("/pin/editer/{id<[0-9]+>}", name="app_pin_update", methods={"GET", "PUT"})
     */
    public function update(Pin $pin, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PinType::class, $pin, [
            //Changes default method from POST to PUT
            'method' => 'PUT',
        ]);

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

    /**
     * @Route("/pin/effacer/{id<[0-9]+>}", name="app_pin_delete", methods={"POST"})
     */
    public function delete(Pin $pin, EntityManagerInterface $em): Response
    {
       $em->remove($pin);
       $em->flush();
       return $this->redirectToRoute('app_home');
    }
}
