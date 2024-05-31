<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 31. 5. 2024
 * Time: 15:53
 */
namespace PhpSentinel\BugCatcher\Repository;

use Doctrine\ORM\QueryBuilder;
use PhpSentinel\BugCatcher\Entity\Record;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PhpSentinel\BugCatcher\Entity\RecordStatus;

/**
 * @method Record|null find($id, $lockMode = null, $lockVersion = null)
 * @method Record|null findOneBy(array $criteria, array $orderBy = null)
 * @method Record[] findAll()
 * @method Record[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecordRepository extends ServiceEntityRepository implements RecordRepositoryInterface{
	public function __construct(ManagerRegistry $registry, $class = Record::class) {
		parent::__construct($registry, $class);
	}

	public function setStatusOlderThan(\DateTimeInterface $lastDate, $newStatus, $previousStatus = RecordStatus::NEW): void {
		$qb = $this->getUpdateStatusQB($newStatus, $lastDate, $previousStatus);

		$qb
			->getQuery()
			->execute();
	}

	public function setStatus(Record $log, \DateTimeInterface $lastDate, $newStatus, $previousStatus = RecordStatus::NEW): void {
		$qb = $this->getUpdateStatusQB($newStatus, $lastDate, $previousStatus);
		$qb
			->andWhere('l.message = :message')
			->setParameter('message', $log->getMessage())
			->getQuery()
			->execute();
	}

	protected function getUpdateStatusQB($newStatus, \DateTimeInterface $lastDate, mixed $previousStatus): QueryBuilder {
		$qb = $this->createQueryBuilder('l');
		$qb = $qb->update()
			->set('l.status', "'{$newStatus->value}'")
			->andWhere('l.date <= :date')
			->andWhere('l.status = :status')
			->setParameter('date', $lastDate)
			->setParameter('status', $previousStatus);

		return $qb;
	}
}