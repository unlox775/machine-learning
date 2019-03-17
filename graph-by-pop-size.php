<?php

require_once(dirname(__FILE__) .'/lib/engine/TrialRunner.php');
require_once(dirname(__FILE__) .'/lib/ProblemDomain/CheckerBoard.php');

$board = new \ProblemDomain\CheckerBoard(2); // Size (on one side), of the square checkerboard
$success_sets = $board->generateSuccessSets();

mainRun( // Starts with a population size of 5
	$success_sets,                   // The "Goal", all the right and wrong answers to guide the search
	empty($argv[1]) ? 5 : $argv[1],  // How much to increase the population size by, each time
	empty($argv[2]) ? 500 : $argv[2] // How many seconds to run each popuation size, to gain a steady average
	);

function mainRun($success, $popsize_increment, $seconds_per_popsize) {
	$pop_size = 5;

	while(1) {
		$runner = new \Engine\TrialRunner($pop_size,$success,false);

		$total_rounds = 0;
		$total_elapsed = 0;
		$trials = [];

		$start = time();
		while (1) {
			list($rounds, $winner, $elapsed) = $runner->runTrial();
			$total_rounds += $rounds;
			$total_elapsed += $elapsed;
			$trials[] = [$rounds, $winner, $elapsed];

			// echo sprintf(
			// 	"Average: %02.2f balanced-cost, %02.3f sec -- (%d rounds in %.3f sec)\n",
			// 	($total_rounds / count($trials)) * ($pop_size/50),
			// 	($total_elapsed / count($trials)),
			// 	$rounds,
			// 	$elapsed
			// );

			if ( (time() - $start) > $seconds_per_popsize ) { break; }
		}

		fputcsv(STDIN,[
			$pop_size,
			sprintf("%.2f",($total_rounds / count($trials)) * ($pop_size/50)),
			sprintf("%.3f",($total_elapsed / count($trials)))
		]);
		$pop_size += $popsize_increment;
	}

}


