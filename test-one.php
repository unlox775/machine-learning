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

function mainRun($pop_size,$success) {
	$runner = new \Engine\TrialRunner($pop_size,$success,false);

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


mainRun($argv[1],$GLOBALS['success']);
