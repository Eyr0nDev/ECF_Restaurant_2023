<?php

namespace App\Controller;

use App\Repository\OpeningHoursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OpeningHoursController extends AbstractController
{
    #[Route('/api/opening_hours', name: 'api_opening_hours')]
    public function getOpeningHoursByDayOfWeek(Request $request, OpeningHoursRepository $openingHoursRepository): JsonResponse
    {
        $dayOfWeek = $request->query->get('day_of_week');

        $openingHours = $openingHoursRepository->findByDayOfWeek($dayOfWeek);

        // Format the response
        $response = [];
        foreach ($openingHours as $openingHour) {
            // Add lunch hours
            if ($openingHour->getLunchOpenTime() && $openingHour->getLunchCloseTime()) {
                $current = $openingHour->getLunchOpenTime();
                while ($current <= $openingHour->getLunchCloseTime()) {
                    $response[] = $current->format('H:i');
                    $current->modify('+15 minutes');
                }
            }

            // Add dinner hours
            if ($openingHour->getDinnerOpenTime() && $openingHour->getDinnerCloseTime()) {
                $current = $openingHour->getDinnerOpenTime();
                while ($current <= $openingHour->getDinnerCloseTime()) {
                    $response[] = $current->format('H:i');
                    $current->modify('+15 minutes');
                }
            }
        }

        return new JsonResponse($response);
    }
}