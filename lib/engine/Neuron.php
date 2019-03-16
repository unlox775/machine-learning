<?php
namespace Engine;

class Neuron {
	public $verbose = false;
	public $downline_neurons = [];
	public $downline_neuron_weights = [];
	private $value = 0.00;
	// public $am_i_biased = 0;

	public function __construct($verbose = false) {
		$this->verbose = $verbose;
	}

	public function attachNeurons($new_downline_neurons,$weights) {
		foreach ( $new_downline_neurons as $i => $n ) {
			$this->downline_neurons[] = $n;
			$this->downline_neuron_weights[] = $weights[$i];
		}
	}

	public function resetValue() { $this->value = 0; }
	public function incrementValue($value) {
		if ( $this->verbose) echo ".";
		$this->value += $value;
	}
	public function pushForward() {

		$passValue =                      $this->value > 0 ?  1  : 0;
		if ( $this->verbose) echo         $this->value > 0 ? "%" : "X";

		foreach ( $this->downline_neurons as $i => $n ) {
			if ( $this->verbose) echo "[";
			$n->incrementValue($passValue * $this->downline_neuron_weights[$i]);
			if ( $this->verbose) echo "]";
		}

		return $passValue;
	}

	public function mutate($mutate_percentage) {
		// echo "MUTATE\n"; 

		foreach ( $this->downline_neuron_weights as $i => $x ) {
			if ( (rand(0,1000000) / 1000000) > ($mutate_percentage/100) ) continue;

			// Mutate!
			$this->downline_neuron_weights[$i] = \Engine\Neuron::randWeight();
		}
	}

	public static function randWeight() {
		// echo "RAND\n";
		return rand(-99999999,99999999) / 100000000;
	}
}
