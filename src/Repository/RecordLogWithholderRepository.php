<?php

namespace PhpSentinel\BugCatcher\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PhpSentinel\BugCatcher\Entity\Record;
use PhpSentinel\BugCatcher\Entity\RecordLog;
use PhpSentinel\BugCatcher\Entity\RecordLogWithholder;
use PhpSentinel\BugCatcher\Entity\RecordStatus;

/**
 * @extends ServiceEntityRepository<RecordLogWithholder>
 *
 * @method RecordLogWithholder|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecordLogWithholder|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecordLogWithholder[]    findAll()
 * @method RecordLogWithholder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecordLogWithholderRepository extends RecordRepository {
	public function __construct(
		ManagerRegistry $registry,
	) {
		parent::__construct($registry, RecordLogWithholder::class);
	}

}