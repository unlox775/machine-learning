<?php
namespace Engine;

require_once(dirname(__FILE__) .'/Neuron.php');

class NeuralNet {
	public $net = [];

	public function __construct($success_trials, $clone_from_net = null) {
		// $this->net = $this->generateFixedFourFourTwoOneNet($success_trials, $clone_from_net);

		if ( $clone_from_net ) {
			$this->net = $this->cloneNet($clone_from_net);
		}
		else {
			$this->net = $this->generateFlatNet(
				$success_trials /* <-- first row generated from array size in here */,
				[4,2,1]
			);
		}
	}

	public function generateFlatNet($success_trials,$row_sizes) {
		// echo "Not Cloning!  Roll the DICE!\n";

		array_unshift($row_sizes,count($success_trials[0][0]));

		$verbose = false;

		$net = [];
		foreach ( $row_sizes as $row_i => $row_size ) {
			foreach ( range(1,$row_size) as $x ) {
				$neuron = new \Engine\Neuron($verbose);
				$net[ $row_i ][] = $neuron;

				///  If not the first row, connect this neuron to all the neurons of last row
				if ( $row_i > 0 && is_array($net[$row_i - 1]) ) {
					$neuron->attachNeuronsToMe($net[$row_i - 1]);
				}
			}
		}
		return $net;
	}

	public function cloneNet($clone_from_net) {
		// echo "Not Cloning!  Roll the DICE!\n";

		$verbose = false;

		///  First generate the entire net
		$net = [];
		$old_neurons_by_id = [];
		$new_neurons_by_id = [];
		foreach ( $clone_from_net->net as $row_i => $net_row ) {
			foreach ( $net_row as $clone_from_neuron ) {
				$neuron = new \Engine\Neuron($verbose, $clone_from_neuron->id);
				$new_neurons_by_id[ $clone_from_neuron->id ] = $net[ $row_i ][] = $neuron;
				$old_neurons_by_id[ $clone_from_neuron->id ] =                    $clone_from_neuron;
			}
		}

		///  Now, wire up all the neuron connections and weights
		foreach ( $new_neurons_by_id as $id => $link_from_new_neuron ) {
			$link_to_neurons = [];
			$link_to_weights = [];
			foreach ( $old_neurons_by_id[$id]->downline_neurons as $link_to_new_neuron_id => $x ) {
				if ( ! isset($new_neurons_by_id[$link_to_new_neuron_id]) ) {
					$clone_from_net->debugWeights();
					throw new \Exception("Error in cloneNet(): clone from net had a neuron that linked to another neuron that was not in the net, from_id=". $id ." , to_id=". $link_to_new_neuron_id);
				}
				$link_to_neurons[] = $new_neurons_by_id[$link_to_new_neuron_id];
				$link_to_weights[] = $old_neurons_by_id[$id]->downline_neuron_weights[$link_to_new_neuron_id];
			}

			$link_from_new_neuron->attachNeurons($link_to_neurons,$link_to_weights);
		}
		return $net;
	}

	public function generateFixedFourFourTwoOneNet($success_trials, $clone_from_net = null) {

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
			if ($clone_from_net) { $weights = array_values($clone_from_net->net[0][$i]->downline_neuron_weights); }
			else                 { $weights = [\Engine\Neuron::randWeight(),\Engine\Neuron::randWeight(),\Engine\Neuron::randWeight(),\Engine\Neuron::randWeight()]; }
			$n->attachNeurons($second_row, $weights);
		}
		foreach ( $second_row as $i => $n ) {
			$weights = null;
			if ($clone_from_net) { $weights = array_values($clone_from_net->net[1][$i]->downline_neuron_weights); }
			else                 { $weights = [\Engine\Neuron::randWeight(),\Engine\Neuron::randWeight()]; }
			$n->attachNeurons($third_row, $weights);
		}
		foreach ( $third_row  as $i => $n ) {
			$weights = null;
			if ($clone_from_net) { $weights = array_values($clone_from_net->net[2][$i]->downline_neuron_weights); }
			else                 { $weights = [\Engine\Neuron::randWeight()]; }
			$n->attachNeurons([$the_lord_neuron], $weights);
		}

		return [$first_row,$second_row,$third_row,[$the_lord_neuron]];
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
				echo "Neuron[". $n->id ."] ". ($ii + 1) ." [". join(',',array_keys($n->downline_neurons)) ." ==> ". join(', ',$n->downline_neuron_weights) ."]\n";
			}
			echo "\n";
		}
		echo "\n\n";
	}
}

