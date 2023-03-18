<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Restaurant;
use App\Repository\OpeningHoursRepository;
use App\Repository\RestaurantRepository;
use DateInterval;
use DateTimeInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
        $restaurant = $options['restaurant'];
        $date = $options['date'];

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
                'choices' => [],
                'placeholder' => 'Select a time',
                'required' => true,
                'label' => 'Time',
                'mapped' => true,
                'multiple' => false,
                'expanded' => false,
                'constraints' => [],
                'choice_value' => function (?string $choice) {
                    return $choice;
                },
                'choice_label' => function (?string $choice) {
                    return $choice;
                },
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
            'restaurants' => $this->restaurantRepository->findAll(),
            'restaurant' => null,
            'date' => null,
            'constraints' => [
                new Callback([$this, 'validateTime']),
            ],
        ]);
        $resolver->setAllowedTypes('restaurant', [Restaurant::class, 'null']);
        $resolver->setAllowedTypes('date', [DateTimeInterface::class, 'null']);
    }

    public function validateTime(Booking $booking, ExecutionContextInterface $context)
    {
        $time = $booking->getTime();
        if (!$time) {
            $context->buildViolation('The selected choice is invalid.')
                ->atPath('time')
                ->addViolation();
            return;
        }
        $formattedTime = $time->format('H:i');
        $availableTimes = $this->generateTimeChoices($booking->getRestaurant(), $booking->getDate());

        if (!in_array($formattedTime, $availableTimes)) {
            $context->buildViolation('The selected choice is invalid.')
                ->atPath('time')
                ->addViolation();
        }
    }


    private function generateTimeChoices(?Restaurant $restaurant, ?DateTimeInterface $date): array
    {
        if ($restaurant === null || $date === null) {
            return [];
        }
        $dayOfWeek = $date->format('l');
        $openingHours = $this->openingHoursRepository->findOneBy([
            'restaurant' => $restaurant->getId(),
            'day_of_week' => $dayOfWeek,
        ]);
        $timeChoices = [];
        if ($openingHours) {
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
                        $timeChoices[] = $time->format('H:i');
                        $time->add($interval);
                    }
                }
            }
        }
        return $timeChoices;
    }
}
