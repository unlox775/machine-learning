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

	$runner = new \Engine\TrialRunner($pop_size,$success_sets,true);

	list($rounds, $winner, $elapsed) = $runner->runTrial();

	echo "Total Elapsed: $elapsed sec\n";
}
