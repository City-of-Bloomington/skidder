<?php
/**
 * Displays all the entries from the log table
 *
 * Since the data involved can be very large, we don't have enough memory to pass
 * the entire results as a variable.  Instead, the application returns a PDO::query.
 * You must fetch each row, one at a time.  When you're done, call $query->closeCursor()
 * to free up the database for other calls.
 *
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Application $this->application
 * @param array $entry The entry from the logSummary that we're displaying
 */
?>
<table>
	<tr><th></th>
		<th>Time</th>
		<th>Script</th>
		<th>Type</th>
	</tr>
	<?php
		$query = $this->application->getEntries(array('script'=>$entry['script']));
		foreach ($query as $row) {
			$url = new URL(BASE_URL.'/applications/viewEntry.php');
			$url->application_id = $row['application_id'];
			$url->timestamp = strtotime($row['timestamp']);

			$deleteURL = new URL(BASE_URL.'/applications/deleteEntries.php');
			$deleteURL->application_id = $this->application->getId();
			$deleteURL->script = $this->script;
			$deleteURL->timestamp = strtotime($row['timestamp']);
			$deleteURL->return_url = $_SERVER['REQUEST_URI'];

			$script = View::escape($row['script']);
			$type = View::escape($row['type']);
			echo "
			<tr><td><a class=\"delete button\" href=\"$deleteURL\">Delete</a></td>
				<td><a href=\"$url\">$row[timestamp]</a></td>
				<td>$script</td>
				<td>$type</td>
			</tr>
			";
		}
		$query->closeCursor();
	?>
</table>
