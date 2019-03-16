<?php
namespace Engine;

require_once(dirname(__FILE__) .'/Neuron.php');

class NeuralNet {
	public $net = [];

	public function __construct($success_trials, $clone_from_net = null) {

		// if ( ! empty($clone_from_net) ) { echo "Cloning!  NO RANDOM!\n"; }
		// else { echo "Not Cloning!  Roll the DICE!\n"; }

		$verbose = false;

		// $frow_weights = array(
		// 	[-1,1,0,0],
		// 	[-1,1,0,0],
		// 	[0,0,-1,1],
		// 	[0,0,-1,1],
		// );
		$n_frow_1 = new \Engine\Neuron($verbose);
		$n_frow_2 = new \Engine\Neuron($verbose);
		$n_frow_3 = new \Engine\Neuron($verbose);
		$n_frow_4 = new \Engine\Neuron($verbose);
		$first_row = [$n_frow_1,$n_frow_2,$n_frow_3,$n_frow_4];

		// $srow_weights = array(
		// 	[1,-1],
		// 	[1,-1],
		// 	[1,-1],
		// 	[1,-1],
		// );
		$n_srow_1 = new \Engine\Neuron($verbose);
		$n_srow_2 = new \Engine\Neuron($verbose);
		$n_srow_3 = new \Engine\Neuron($verbose);
		$n_srow_4 = new \Engine\Neuron($verbose);
		$second_row = [$n_srow_1,$n_srow_2,$n_srow_3,$n_srow_4];

		$n_trow_1 = new \Engine\Neuron($verbose);
		$n_trow_2 = new \Engine\Neuron($verbose);
		$third_row = [$n_trow_1,$n_trow_2];

		$the_lord_neuron = new \Engine\Neuron($verbose);

		foreach ( $first_row  as $i => $n ) {
			$weights = null;
			if ($clone_from_net) { $weights = $clone_from_net->net[0][$i]->downline_neuron_weights; }
			else                 { $weights = [\Engine\Neuron::randWeight(),\Engine\Neuron::randWeight(),\Engine\Neuron::randWeight(),\Engine\Neuron::randWeight()]; }
			$n->attachNeurons($second_row, $weights);
		}
		foreach ( $second_row as $i => $n ) {
			$weights = null;
			if ($clone_from_net) { $weights = $clone_from_net->net[1][$i]->downline_neuron_weights; }
			else                 { $weights = [\Engine\Neuron::randWeight(),\Engine\Neuron::randWeight()]; }
			$n->attachNeurons($third_row, $weights);
		}
		foreach ( $third_row  as $i => $n ) {
			$weights = null;
			if ($clone_from_net) { $weights = $clone_from_net->net[2][$i]->downline_neuron_weights; }
			else                 { $weights = [\Engine\Neuron::randWeight()]; }
			$n->attachNeurons([$the_lord_neuron], $weights);
		}

		$this->net = [$first_row,$second_row,$third_row,[$the_lord_neuron]];
	}

	function runTrial($starting) {

		foreach ( $this->net as $i => $net_row ) {
			///  Only run the initialize on the first row
			if ( $i == 0 ) {
				foreach ($net_row as $ii => $n ) {
					// if ( $n->verbose ) echo "(". $starting[$ii] .")";
					$n->incrementValue($starting[$ii]);
				}
			}
			///  Call Downline on all the rows
			foreach ($net_row as $n ) {
				$n->pushForward();
			}

			if ( $net_row[0]->verbose) echo " | ";
		}

		return end(end($this->net))->pushForward();
	}

	public function resetNeuronValues() {
		foreach ( $this->net as $i => $net_row ) {
			foreach ($net_row as $n ) {
				$n->resetValue();
			}
		}
	}

	public function mutate($mutate_percentage) {
		foreach ( $this->net as $i => $net_row ) {
			foreach ($net_row as $n ) {
				$n->mutate($mutate_percentage);
			}
		}
	}

	public function debugWeights() {
		foreach ( $this->net as $i => $net_row ) {
			echo "Row ". ($i + 1) ."\n";
			foreach ($net_row as $ii => $n ) {
				echo "Neuron ". ($ii + 1) ." [". join(', ',$n->downline_neuron_weights) ."]\n";
			}
			echo "\n";
		}
		echo "\n\n";
	}
}

