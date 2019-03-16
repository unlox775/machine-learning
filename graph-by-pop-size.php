<?php

require_once(dirname(__FILE__) .'/lib/engine/TrialRunner.php');

$successful_checkerboards = array(
	[1,0,
	 0,1],

	[0,1,
	 1,0]
	);

$GLOBALS['success'] = [
   [[0,0,0,0],[0]],
   [[0,0,0,1],[0]],
   [[0,0,1,0],[0]],
   [[0,1,0,0],[0]],
   [[1,0,0,0],[0]],
   [[0,0,1,1],[0]],
   [[0,1,0,1],[0]],
   [[1,0,0,1],[1]],
   [[0,1,1,0],[1]],
   [[1,0,1,0],[0]],
   [[1,1,0,0],[0]],
   [[1,1,1,0],[0]],
   [[1,1,0,1],[0]],
   [[1,0,1,1],[0]],
   [[0,1,1,1],[0]],
   [[1,1,1,1],[0]], #all possible combinations
   
   [[1,0,0,1],[1]], # repeating the number of combinations that return [1] until the ratio of [1] returning and [0] returning are the same
   [[0,1,1,0],[1]],
   [[1,0,0,1],[1]],
   [[0,1,1,0],[1]],
   [[1,0,0,1],[1]],
   [[0,1,1,0],[1]],
   [[1,0,0,1],[1]],
   [[0,1,1,0],[1]],
   [[1,0,0,1],[1]],
   [[0,1,1,0],[1]],
   [[1,0,0,1],[1]],
   [[0,1,1,0],[1]],
   ];

function mainRun($success, $popsize_increment = 5, $seconds_per_popsize = 120) {
	$pop_size = 5;

	if ( empty($popsize_increment) ) { $popsize_increment = 5; }
	if ( empty($seconds_per_popsize) ) { $seconds_per_popsize = 120; }

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


mainRun($GLOBALS['success'], $argv[1], $argv[2]);
