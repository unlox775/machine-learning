<?php

require_once(dirname(__FILE__) .'/NeuralNet.php');

class TrialPopulation {
	private $__size = null;
	private $__population = [];
	private $__success_trials = null;
	private $__trials_run = false;
	public $winners_percentage = 5;
	public $mutate_percentage = 10;

	public function __construct($pop_size, $success_trials, $seed_babies = false) {
		$this->__size = $pop_size;
		$this->__success_trials = $success_trials;

		$this->__population = ( $seed_babies === false
			? $this->__generateBasePopulation()
			: $this->__generateMutatedBabyPopulation($seed_babies)
		);
	}

	private function __generateBasePopulation() {
		$new_population = [];
		foreach ( range(0,$this->__size - 1) as $i ) {
			$new_population[] = (object) [ 'id' => $i+1, 'net' => new NeuralNet($this->__success_trials), 'yay_count' => 0 ];
		}
		return $new_population;
	}

	private function __generateMutatedBabyPopulation($seed_babies = []) {
		// echo "Best Baby:\n";
		// $seed_babies[0]->debugWeights();

		$new_population = [];
		foreach ( range(0,$this->__size - 1) as $i ) {
			$clone_from = $seed_babies[ $i % count($seed_babies) ];
			// echo "SEED CHOSEN ". var_export([
			// 	$i % count($seed_babies),
			// 	count($seed_babies),
			// 	array_key_exists($i % count($seed_babies),$seed_babies),
			// 	is_null($seed_babies[ $i % count($seed_babies) ])
			// ]) ."\n";
			// $clone_from->debugWeights();
			$new_net = new NeuralNet($this->__success_trials,$clone_from);

			///  Mutate babies, but leave the first original Seed Baby set, un-mutated
			if ( $i >= count($seed_babies) ) {
				$new_net->mutate($this->mutate_percentage);
			}
			// else { echo "Preserving Original winner...\n"; }

			$new_population[] = (object) [ 'id' => $i+1, 'net' => $new_net, 'yay_count' => 0 ];
		}

		if ( count($new_population) != $this->__size ) { throw new \Exception("Error in __generateMutatedBabyPopulation(), result size was wrong: ". count($new_population) ." (should have been ". $this->__size .")"); }
		return $new_population;
	}

	public function runTrials() {
		$this->__trials_run = false;
		foreach ($this->__population as $net) {
			// echo "\n\n\n\n\n==================";
			$yay_count = 0;
			foreach ( $this->__success_trials as $i => $trial ) {
				$net->net->resetNeuronValues();
				list($start_values, $expected ) = $trial;
				$result = $net->net->runTrial($start_values);
				if ( $result == $expected[0] ) { $net->yay_count++; }
				// echo "\nTrial: [". join(',',$start_values) ."] ". $result ." (should be: ". $expected[0] .") --> Success = ". ($result == $expected[0] ? "Yay!" : "Bleh") ."  ";
			}
			// usleep(100000);
		}

		usort( $this->__population,
			function ($a,$b) { return $a->yay_count > $b->yay_count ? -1
								   : ($a->yay_count < $b->yay_count ? 1 : 0 );
			}
		);
		$this->__trials_run = true;

		// echo "\n\nWINNERS:\n";
		// foreach (array_reverse(array_slice($this->__population,0,5)) as $net) {
		// 	echo $net->id ." - ". $net->yay_count ."\n";
		// }
	}

	public function getPerfectNetwork() {
		if ( ! $this->__trials_run ) { throw new \Exception("Error calling ". __FUNCTION__ ." without first running trials"); }
		if ( $this->__population[0]->yay_count == count($this->__success_trials) ) {
			return $this->__population[0]->net;
		}
	}

	public function generateEvolvedPopulation() {
		if ( ! $this->__trials_run ) { throw new \Exception("Error calling ". __FUNCTION__ ." without first running trials"); }

		$newpop = [];
		$winner_max_i = min(
			floor($this->__size * ($this->winners_percentage/100)) + 1,
			count($this->__population)-1
		);
		$seed_babies = [];
		foreach ( range(0,$winner_max_i) as $i ) {
			if ( empty($this->__population[$i]->net) ) {
				throw new \Exception("Error in generateEvolvedPopulation(), found a null row: $i");
			}
			// echo "Winner";
			// $this->__population[$i]->net->debugWeights();
			$seed_babies[] = $this->__population[$i]->net;
		}

		return new TrialPopulation($this->__size, $this->__success_trials, $seed_babies);
	}
}
