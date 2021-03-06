<?php

require_once(dirname(__FILE__) .'/lib/engine/TrialRunner.php');
require_once(dirname(__FILE__) .'/lib/ProblemDomain/CheckerBoard.php');

mainRun(
	empty($argv[1]) ? 50 : $argv[1], // Population Size per round
	empty($argv[2]) ? 2  : $argv[2]  // Size (on one side), of the square checkerboard
);

function mainRun($pop_size,$checkerboard_size) {
	$board = new \ProblemDomain\CheckerBoard($checkerboard_size);
	$success_sets = $board->generateSuccessSets();
	// exit;

	$runner = new \Engine\TrialRunner($pop_size,$success_sets,false);

	$total_rounds = 0;
	$total_elapsed = 0;
	$trials = [];

	while (1) {
		list($rounds, $winner, $elapsed) = $runner->runTrial();
		$total_rounds += $rounds;
		$total_elapsed += $elapsed;
		$trials[] = [$rounds, $winner, $elapsed];

		echo sprintf(
			"Average: %02.2f balanced-cost, %02.3f sec -- (%d rounds in %.3f sec)\n",
			($total_rounds / count($trials)) * ($pop_size/50),
			($total_elapsed / count($trials)),
			$rounds,
			$elapsed
		);
	}
}
