<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Application $this->application
 * @param array $this->entries
 */
$url = new URL(BASE_URL.'/applications/viewEntry.php');
$url->application_id = $this->application->getId();
foreach ($this->entries as $log) {
	$url->timestamp = $log['timestamp'];

	echo "
$url
	$log[timestamp] - $log[script]
		$log[type]
	";
}
