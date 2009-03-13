<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Application $this->application
 */
$scripts = $this->application->getEntryScripts();
if (count($scripts)) {
	echo "
	<table>
	<tr><th>Script</th>
		<th>Error Count</th>
	</tr>
	";
	foreach ($this->application->getEntryScripts() as $entry) {
		$script = View::escape($entry['script']);
		echo "
		<tr><td>$script</td>
			<td>$entry[count]</td>
		</tr>
		";
	}
	echo "</table>";
}
