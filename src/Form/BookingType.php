<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Restaurant;
use App\Repository\OpeningHoursRepository;
use App\Repository\RestaurantRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    private RestaurantRepository $restaurantRepository;
    private OpeningHoursRepository $openingHoursRepository;

    public function __construct(RestaurantRepository $restaurantRepository, OpeningHoursRepository $openingHoursRepository)
    {
        $this->restaurantRepository = $restaurantRepository;
        $this->openingHoursRepository = $openingHoursRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $initialTimeChoices = [];
        if (count($options['restaurants']) > 0) {
            $initialRestaurant = $options['restaurants'][0];
            $initialDate = new \DateTime();
            $initialTimeChoices = $this->generateTimeChoices($options['opening_hours_repository'], $initialRestaurant, $initialDate);
        }

        $builder
            ->add('restaurant', EntityType::class, [
                'class' => Restaurant::class,
                'choices' => $options['restaurants'],
                'choice_label' => 'name',
                'label' => 'Restaurant',
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date',
            ])
            ->add('time', ChoiceType::class, [
                'label' => 'Time',
                'choices' => $initialTimeChoices,
                'attr' => [
                    'class' => 'time-selector',
                ],
            ])
            ->add('numGuests', IntegerType::class, [
                'label' => 'Number of Guests',
            ])
            ->add('allergies', TextType::class, [
                'label' => 'Allergies (optional)',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
            'restaurants' => [],
            'opening_hours_repository' => null,
        ]);
    }

    private function generateTimeChoices(OpeningHoursRepository $openingHoursRepository, Restaurant $restaurant, \DateTimeInterface $date): array
    {
        $dayOfWeek = $date->format('l');
        $openingHours = $openingHoursRepository->findOneBy([
            'restaurant' => $restaurant,
            'day_of_week' => $dayOfWeek,
        ]);

        $timeChoices = [];
        if ($openingHours) {
            $interval = new \DateInterval('PT30M');
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
                        $timeChoices[$time->format('H:i')] = $time;
                        $time->add($interval);
                    }
                }
            }
        }

        return $timeChoices;
    }
}

