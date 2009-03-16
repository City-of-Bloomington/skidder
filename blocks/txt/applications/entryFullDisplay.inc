<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->entries
 */
foreach ($this->entries as $log) {
	echo "
	$log[timestamp] - $log[script]
		$log[type]
	";
}
