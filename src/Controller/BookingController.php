<?php


namespace App\Controller;


use App\Entity\Booking;
use App\Form\BookingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class BookingController extends AbstractController
{


    #[Route('/booking', name: 'booking')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setUser($this->getUser());
            $entityManager->persist($booking);
            $entityManager->flush();


            return $this->redirectToRoute('booking_success', ['booking_name' => $booking->getName()]);
        }


        return $this->render('booking/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/booking/success/{booking_name}', name: 'booking_success')]
    public function success(string $booking_name): Response
    {
        return $this->render('booking/success.html.twig', [
            'booking_name' => $booking_name,
        ]);
    }
}

