<?php

namespace PhpSentinel\BugCatcher\Command;

use PhpSentinel\BugCatcher\Entity\RecordPing;
use PhpSentinel\BugCatcher\Entity\Project;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Uid\Uuid;

#[AsCommand(
	name: 'app:record-optimizer',
	description: 'Add a short description for your command',
)]
class PingRecordOptimizerCommand extends Command {
	public function __construct(
		private readonly EntityManagerInterface $em,
	) {
		parent::__construct();
	}

	protected function configure(): void {
		$this
			->addOption('past', "p", InputOption::VALUE_REQUIRED, 'Days in the past to start from')
			->addOption('precision', "i", InputOption::VALUE_REQUIRED, 'Precision in minutes to group logs to');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$io = new SymfonyStyle($input, $output);

		$past      = $input->getOption('past');
		$precision = $input->getOption('precision');

		$sql       = <<<SQL
select
    project_id, status_code,
    count(*) as cnt ,
    concat(DATE_FORMAT(`date`,'%Y-%c-%d %H:'),TIME_FORMAT(SEC_TO_TIME(((DATE_FORMAT(`date`,'%i') div {$precision})*{$precision})*60),'%i'),':00') as period
from ping_record
where date < DATE_SUB(NOW(), INTERVAL {$past} DAY)
group by `period`, status_code
SQL;
		$deleteSql = <<<SQL
delete from ping_record where date < DATE_SUB(NOW(), INTERVAL {$past} DAY)
SQL;

		$this->em->beginTransaction();
		$total = 0;
		try {
			$rows = $this->em->getConnection()->executeQuery($sql)->fetchAllAssociative();
			$this->em->getConnection()->executeQuery($deleteSql);
			foreach ($rows as $row) {
				$this->em->persist(new RecordPing(
					$this->em->getReference(Project::class, Uuid::fromString($row["project_id"])),
					$row["status_code"],
					new DateTimeImmutable($row["period"]),
				));
				$total += $row["cnt"];
			}
			$this->em->flush();
			$this->em->commit();
		} catch (Exception $e) {
			$this->em->getConnection()->rollBack();
			throw $e;
		}


		$io->success('Optimized ' . $total . ' records.');

		return Command::SUCCESS;
	}
}
