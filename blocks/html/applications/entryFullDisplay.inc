<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->entries
 */
foreach ($this->entries as $log) {
	$script = View::escape($log['script']);
	$type = View::escape($log['type']);
	$message = View::escape($log['message']);
	echo "
	<div class=\"interfaceBox\">
		<h1>$log[timestamp] - $script</h1>
		<p>$type</p>
		<div>$message</div>
	</div>
	";
}
