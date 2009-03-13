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
	</tr>
	";
	foreach ($this->application->getEntryScripts() as $entry) {
		$url = $this->application->getURL();
		$url->script = $entry['script'];

		$script = View::escape($entry['script']);
		echo "
		<tr><td><a href=\"$url\">$script</a></td>
			<td>$entry[count]</td>
		</tr>
		";
		if (isset($_GET['script']) && $entry['script']==$_GET['script']) {
			echo "<tr><td colspan=\"2\">";
				$entries = $this->application->getEntries(array('script'=>$entry['script']));
				$logs = new Block('applications/entryList.inc',array('entries'=>$entries));
				echo $logs->render();
			echo "</td></tr>";
		}
	}
	echo "</table>";
}
