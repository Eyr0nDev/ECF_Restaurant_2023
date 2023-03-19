<?php

namespace App\Repository;

use App\Entity\OpeningHours;
use App\Entity\Restaurant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OpeningHours>
 *
 * @method OpeningHours|null find($id, $lockMode = null, $lockVersion = null)
 * @method OpeningHours|null findOneBy(array $criteria, array $orderBy = null)
 * @method OpeningHours[]    findAll()
 * @method OpeningHours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OpeningHoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OpeningHours::class);
    }

    public function save(OpeningHours $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(OpeningHours $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findByDayOfWeek(string $dayOfWeek): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.day_of_week = :day_of_week')
            ->setParameter('day_of_week', $dayOfWeek)
            ->getQuery()
            ->getResult();
    }
    public function getOpeningHoursChoices(): array
    {
        $openingHours = $this->findAll();

        $choices = [];

        foreach ($openingHours as $openingHour) {
            $choice = sprintf(
                '%s: %s - %s, %s - %s',
                $openingHour->getDayOfWeek(),
                $openingHour->getLunchOpenTime() ? $openingHour->getLunchOpenTime()->format('H:i') : 'Closed',
                $openingHour->getLunchCloseTime() ? $openingHour->getLunchCloseTime()->format('H:i') : '',
                $openingHour->getDinnerOpenTime() ? $openingHour->getDinnerOpenTime()->format('H:i') : 'Closed',
                $openingHour->getDinnerCloseTime() ? $openingHour->getDinnerCloseTime()->format('H:i') : '',
            );

            $choices[$choice] = $openingHour->getId();
        }

        return $choices;
    }
//    /**
//     * @return OpeningHours[] Returns an array of OpeningHours objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?OpeningHours
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
