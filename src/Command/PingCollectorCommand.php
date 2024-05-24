<?php

namespace App\Command;

use App\Entity\PingRecord;
use App\Repository\PingRecordRepository;
use App\Repository\ProjectRepository;
use App\Service\PingCollector\PingCollectorInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

#[AsCommand(
	name: 'ping-collector',
)]
class PingCollectorCommand extends Command implements ServiceSubscriberInterface {
	public function __construct(
		private readonly array                $collectors,
		private readonly ProjectRepository    $projectRepo,
		private readonly PingRecordRepository $pingRecordRepo
	) {
		parent::__construct();
	}

	protected function configure(): void {}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$projects = $this->projectRepo->findAll();
		foreach ($projects as $project) {
			/** @var PingCollectorInterface $collector */
			$collector = $this->collectors[$project->getPingCollector()]??null;
			if (!$collector) {
				continue;
			}
			try {
				$status = $collector->ping($project);
			} catch (\Throwable $e) {
				$status = Response::HTTP_INTERNAL_SERVER_ERROR;
			}
			$this->pingRecordRepo->save(new PingRecord($project, $status), true);
		}

		return Command::SUCCESS;
	}

	public static function getSubscribedServices(): array {
		return [];
	}
}