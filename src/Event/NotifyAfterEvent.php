<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 26. 7. 2024
 * Time: 16:17
 */
namespace BugCatcher\Event;

use BugCatcher\Entity\Notifier;
use Symfony\Contracts\EventDispatcher\Event;

class NotifyAfterEvent extends Event {


	public function __construct(
		public readonly Notifier $notifier,
	) {}
}