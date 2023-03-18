<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\OpeningHoursRepository;
use App\Repository\RestaurantRepository;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use IntlDateFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    #[Route('/booking', name: 'booking')]
    public function index(Request $request, EntityManagerInterface $em, RestaurantRepository $restaurantRepository, OpeningHoursRepository $openingHoursRepo): Response
    {
        $booking = new Booking();
        $form = $this->createBookingForm($booking, $restaurantRepository, $openingHoursRepo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setUser($this->getUser());
            $booking->setCreatedAt(new DateTimeImmutable());

            $openingHours = $openingHoursRepo->findValidOpeningHours(
                $booking->getRestaurant(),
                $booking->getDate()->format('l'),
                $booking->getTime()
            );

            if ($openingHours) {
                $booking->setOpeningHours($openingHours);
                $em->persist($booking);
                $em->flush();

                $this->addFlash('success', 'Booking successfully created!');
                return $this->redirectToRoute('booking');
            } else {
                $this->addFlash('error', 'Selected time is not within the valid opening hours.');
            }
        }

        return $this->render('booking/index.html.twig', [
            'bookingForm' => $form->createView(),
        ]);
    }

    private function createBookingForm(Booking $booking, RestaurantRepository $restaurantRepository, OpeningHoursRepository $openingHoursRepository): FormInterface
    {
        $restaurant = $booking->getRestaurant();
        $date = $booking->getDate();

        return $this->createForm(BookingType::class, $booking, [
            'action' => $this->generateUrl('booking'),
            'method' => 'POST',
            'restaurant' => $restaurant,
            'restaurants' => $restaurantRepository->findAll(),
            'date' => $date,
        ]);
    }

    #[Route('/restaurant/{restaurantId}/available-times', name: 'restaurant_available_times')]
    public function getAvailableTimes(int $restaurantId, Request $request, OpeningHoursRepository $openingHoursRepository): JsonResponse
    {
        try {
            $dateStr = $request->query->get('date');
            $date = DateTimeImmutable::createFromFormat('Y-m-d', $dateStr);

            $dateFormatter = new IntlDateFormatter(
                'fr_FR',
                IntlDateFormatter::FULL,
                IntlDateFormatter::NONE,
                null,
                null,
                'EEEE'
            );

            $dayOfWeek = $dateFormatter->format($date);

            $openingHours = $openingHoursRepository->findOneBy([
                'restaurant' => $restaurantId,
                'day_of_week' => $dayOfWeek,
            ]);

            $availableTimes = [];


            if ($openingHours && !$openingHours->isIsClosed()) {
                $interval = new DateInterval('PT30M');
                $timeRanges = [
                    [$openingHours->getLunchOpenTime(), $openingHours->getLunchCloseTime()],
                    [$openingHours->getDinnerOpenTime(), $openingHours->getDinnerCloseTime()],
                ];

                foreach ($timeRanges as $range) {
                    $start = $range[0];
                    $end = $range[1];

                    if ($start && $end) {
                        $time = clone $start;
                        while ($time <= $end) {
                            $availableTimes[$time->format('H:i')] = $time->format('H:i');
                            $time->add($interval);
                        }
                    }
                }
            }

            // Debugging information
            return new JsonResponse([
                'restaurantId' => $restaurantId,
                'date' => $dateStr,
                'dayOfWeek' => $dayOfWeek,
                'openingHours' => $openingHours,
                'availableTimes' => $availableTimes,
            ]);

        } catch (\Throwable $exception) {
            return new JsonResponse([
                'error' => true,
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ], 500);
        }
    }

}
