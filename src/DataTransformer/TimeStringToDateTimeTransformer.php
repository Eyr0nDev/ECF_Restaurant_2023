<?php

namespace App\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TimeStringToDateTimeTransformer implements DataTransformerInterface
{
    public function transform($dateTime): string
    {
        if (null === $dateTime) {
            return '';
        }

        return $dateTime->format('H:i');
    }

    public function reverseTransform($timeString): ?\DateTime
    {
        if (!$timeString) {
            return null;
        }

        $dateTime = \DateTime::createFromFormat('H:i', $timeString);

        if (!$dateTime) {
            throw new TransformationFailedException(sprintf('The time string "%s" is not valid.', $timeString));
        }

        return $dateTime;
    }
}
