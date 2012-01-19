<?php
/**
 * @copyright 2009-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Application $this->application
 * @param GET field
 */
echo "<h2>Display By:</h2><ul>";
$url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
$list = array('script','type','request_uri');
foreach ($list as $f) {
	$url->field = $f;
	echo "<li><a href=\"$url\">$f</a></li>";
}
echo "</ul>";

if (!empty($_GET['field'])) {
	$field = $_GET['field'];
	$entries = $this->application->distinct($field);
	if (count($entries)) {
		$title = View::escape(ucfirst($field));
		echo "
		<table>
		<tr><th>$title</th>
			<th>Error Count</th>
			<th></th>
		</tr>
		";
		foreach ($entries as $entry) {
			$url = new URL(BASE_URL.'/applications/entries.php');
			$url->application_id = $this->application->getId();
			$url->$field = $entry[$field];

			$deleteURL = new URL(BASE_URL.'/applications/deleteEntries.php');
			$deleteURL->application_id = $this->application->getId();
			$deleteURL->$field = $entry[$field];
			$deleteURL->return_url = $_SERVER['REQUEST_URI'];

			$value = View::escape($entry[$field]);
			echo "
			<tr><td><a href=\"$url\">$value</a></td>
				<td>$entry[count]</td>
				<td><a class=\"delete button\" href=\"$deleteURL\">Delete</a></td>
			</tr>
			";
		}
		echo "</table>";
	}
}
