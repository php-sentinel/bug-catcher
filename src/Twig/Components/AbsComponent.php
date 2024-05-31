<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 22. 5. 2024
 * Time: 17:33
 */
namespace PhpSentinel\BugCatcher\Twig\Components;

use PhpSentinel\BugCatcher\Entity\Project;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

abstract class AbsComponent {
	#[LiveProp]
	public Project $project;

}