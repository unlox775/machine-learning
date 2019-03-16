<?php

require_once(dirname(__FILE__) .'/lib/engine/TrialPopulation.php');

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

function mainRun() {
	$population = new \Engine\TrialPopulation($GLOBALS['argv'][1], $GLOBALS['success']);

	$rounds = 0;
	while(1) {
      $rounds++;
		$population->runTrials();

		$winner = $population->getPerfectNetwork();
		if ( $winner ) {
         echo "\nFinal Winning Neural Net (took $rounds rounds): \n";
			$winner->debugWeights();
			exit;
		}

		$population = $population->generateEvolvedPopulation();

		// sleep(2);
	}
	
}


mainRun();
