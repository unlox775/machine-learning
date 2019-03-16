<?php
namespace Engine;

require_once(dirname(__FILE__) .'/TrialPopulation.php');

class TrialRunner {
	private $__population_size = null;
	private $__success_sets = null;
	public $verbose = false;

	public function __construct($population_size, $success_sets, $verbose = false) {
		$this->__population_size = empty($population_size) ? 50 : $population_size;
		$this->__success_sets = $success_sets;
		$this->verbose = $verbose;
	}

	public function runTrial() {
		$start = microtime(true);
		$population = new \Engine\TrialPopulation($this->__population_size, $this->__success_sets, false, $this->verbose);

		$rounds = 0;
		$winner = null;
		while(1) {
	      $rounds++;
			$population->runTrials();

			$winner = $population->getPerfectNetwork();
			if ( $winner ) {
				if ( $this->verbose ) echo "\nFinal Winning Neural Net (took $rounds rounds): \n";
				if ( $this->verbose ) $winner->debugWeights();
				break;
			}

			$population = $population->generateEvolvedPopulation();

			// sleep(2);
		}

		return [$rounds,$winner, microtime(true) - $start];
	}

}
