<?php
namespace Engine;

class Neuron {
	public static $__id_inc = 1;
	public $verbose = false;
	public $id = false;
	public $downline_neurons = [];
	public $downline_neuron_weights = [];
	private $value = 0.00;
	// public $am_i_biased = 0;

	public function __construct($verbose = false, $set_id = null) {
		$this->verbose = $verbose;
		$this->id = is_null($set_id) ? \Engine\Neuron::$__id_inc++ : $set_id;
	}

	public function attachNeurons($new_downline_neurons,$weights = null) {
		foreach ( $new_downline_neurons as $i => $n ) {
			$this->downline_neurons[$n->id] = $n;
			$this->downline_neuron_weights[$n->id] = is_null($weights) ? \Engine\Neuron::randWeight() : $weights[$i];
		}
	}

	public function attachNeuronsToMe($new_upline_neurons,$weights = null) {
		foreach ( $new_upline_neurons as $i => $n ) {
			$n->downline_neurons[$this->id] = $this;
			$n->downline_neuron_weights[$this->id] = is_null($weights) ? \Engine\Neuron::randWeight() : $weights[$i];
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
