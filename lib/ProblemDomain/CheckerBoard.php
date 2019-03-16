<?php
namespace ProblemDomain;

class CheckerBoard {
	private $__size = null; // always a square checkerboard

	public function __construct($size = 2) {
		if ( empty($size) ) { throw new \Exception("No zero-size checkerboards, Please!!"); }
		$this->__size = $size;
	}

	public function generateSuccessSets() {

		function t($v) { return ($v == 0) ? 1 : 0; }

		///  Generate the successful ones first
		$good_sets = [];
		foreach ([0 /* starting tile = black */,1 /* starting tile = white */] as $start_tile) {
			$set = [];
			$tile_color = $start_tile;
			$last_row_start_tile = $start_tile;
			foreach (range(1,$this->__size) as $x) { // row
				$tile_color = $last_row_start_tile = t($last_row_start_tile);
				foreach (range(1,$this->__size) as $xx) { // column
					$set[] = $tile_color;
					$tile_color = t($tile_color);
				}
			}
			$good_sets[join($set)] = [$set,[1]]; // Success row
		}
		$goods = array_values($good_sets);

		// Now generate ALL possible boards
		$bad_sets = [];
		foreach ( range(0,(($this->__size*$this->__size)**2) - 1) as $n ) {
			$binary = sprintf( "%0". ($this->__size*$this->__size) ."d", decbin($n) );
			if ( isset($good_sets[$binary]) ) { continue; }

			$bad_sets[$binary] = [str_split($binary),[0]]; // Fail row
		}

		$final_sets = array_values($bad_sets);
		// Add good sets until # of good is about the same as # of bad
		while( count($final_sets) < count($bad_sets)*2 ) {
			$final_sets = array_merge($final_sets,$goods);
		}

		// print_r($final_sets); exit;
		return array_values($final_sets);
	}
}
