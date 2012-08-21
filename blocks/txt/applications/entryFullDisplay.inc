<?php
/**
 * @copyright 2009-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Application $this->application
 * @param array $this->entries
 */
$url = BASE_URL."/applications/viewEntry?application_id={$this->application->getId()}";
foreach ($this->entries as $log) {
	echo "
$url;timestamp=$log[timestamp]
	$log[timestamp] - $log[script]
		$log[type]
	";
}
