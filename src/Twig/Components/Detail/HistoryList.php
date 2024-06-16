<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 1. 6. 2024
 * Time: 7:11
 */
namespace PhpSentinel\BugCatcher\Twig\Components\Detail;

use PhpSentinel\BugCatcher\Entity\Record;
use PhpSentinel\BugCatcher\Repository\RecordRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@BugCatcher/components/Detail/HistoryList.html.twig')]
class HistoryList {
	public Record $record;

	public function __construct(private readonly RecordRepository $recordRepo) {}

	/**
	 * @return array<Record>
	 */
	public function getHistory(): array {
		/** @var Record[] $items */
		$items  = $this->recordRepo->findBy([
			"hash"   => $this->record->getHash(),
			"status" => $this->record->getStatus(),
		], ["date" => "DESC"], 50);
		$return = [];
		foreach ($items as $item) {
			$key          = $item->getDate()->getTimestamp();
			$item         = $return[$key]??$item;
			$return[$key] = $item;
			$item->setCount($item->getCount() + 1);
		}

		return array_values($return);
	}
}