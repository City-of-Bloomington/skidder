<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Application $this->application
 * @param GET script
 */
$scripts = $this->application->getEntryScripts();
if (count($scripts)) {
	echo "
	<table>
	<tr><th>Script</th>
		<th>Error Count</th>
		<th></th>
	</tr>
	";
	foreach ($this->application->getEntryScripts() as $entry) {
		$url = $this->application->getURL();
		$url->script = $entry['script'];

		$deleteURL = new URL(BASE_URL.'/applications/deleteEntries.php');
		$deleteURL->application_id = $this->application->getId();
		$deleteURL->script = $entry['script'];
		$deleteURL->return_url = $_SERVER['REQUEST_URI'];

		$script = View::escape($entry['script']);
		echo "
		<tr><td><a href=\"$url\">$script</a></td>
			<td>$entry[count]</td>
			<td><a class=\"delete button\" href=\"$deleteURL\">Delete</a></td>
		</tr>
		";
		if (isset($_GET['script']) && $entry['script']==$_GET['script']) {
			echo "<tr><td colspan=\"2\">";
			include APPLICATION_HOME.'/blocks/html/applications/entryList.inc';
			echo "</td></tr>";
		}
	}
	echo "</table>";
}
