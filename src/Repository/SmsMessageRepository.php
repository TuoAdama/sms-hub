<?php

namespace App\Repository;

use App\Entity\SmsMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SmsMessage>
 *
 * @method SmsMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method SmsMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method SmsMessage[]    findAll()
 * @method SmsMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SmsMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SmsMessage::class);
    }


    public function getAllUnsentSmsMessages(): array
    {

        $results =  $this->createQueryBuilder('s')
            ->andWhere('s.sent = :sent')
            ->setParameter('sent', false)
            ->getQuery()
            ->getResult();

        $this->updateAllUnsentSmsMessages();

        return $results;
    }

    private function updateAllUnsentSmsMessages(): void
    {
        $this->createQueryBuilder('s')
            ->update("App\Entity\SmsMessage", 's')
            ->set('s.sent', ':sent')
            ->where('s.sent = false')
            ->setParameter('sent', true)
            ->getQuery()
            ->execute();
    }

    public function paginate(): Query
    {
        return $this->createQueryBuilder('s')
            ->getQuery();
    }
}
