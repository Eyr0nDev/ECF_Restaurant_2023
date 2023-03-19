<?php

namespace App\Form;

use App\DataTransformer\TimeStringToDateTimeTransformer;
use App\Entity\Booking;
use App\Entity\OpeningHours;
use App\Entity\Restaurant;
use DateInterval;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $timeTransformer = new TimeStringToDateTimeTransformer();

        $builder
            ->add('numGuests', IntegerType::class)
            ->add('allergies', TextType::class, [
                'required' => false,
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['id' => 'booking_date'],
            ])
            ->add('restaurant', EntityType::class, [
                'class' => Restaurant::class,
                'choice_label' => 'name',
            ])
            ->add('time', ChoiceType::class, [
                'expanded' => true,
                'choices' => $options['opening_hours_choices'],
                'choice_label' => function ($choice, $key, $value) {
                    return $choice;
                },
                'attr' => [
                    'id' => 'booking_time',
                ],
            ])
            ->get('time')->addModelTransformer($timeTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $openingHoursChoices = $this->generateTimeIntervals('00:00', '24:00', 15);

        $resolver->setDefaults([
            'data_class' => Booking::class,
            'opening_hours_choices' => $openingHoursChoices,
        ]);
    }
    function generateTimeIntervals($start, $end, $interval): array
    {
        $startTime = new DateTime($start);
        $endTime = new DateTime($end);
        $interval = new DateInterval("PT{$interval}M");
        $times = [];

        while ($startTime <= $endTime) {
            $times[] = $startTime->format('H:i');
            $startTime->add($interval);
        }

        return $times;
    }
}