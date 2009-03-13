<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->entries
 */
?>
<table>
	<tr><th>Time</th>
		<th>Script</th>
		<th>Type</th>
	</tr>
	<?php
		foreach ($this->entries as $log) {
			$url = new URL(BASE_URL.'/applications/viewEntry.php');
			$url->application_id = $log['application_id'];
			$url->timestamp = strtotime($log['timestamp']);

			$script = View::escape($log['script']);
			$type = View::escape($log['type']);
			echo "
			<tr><td><a href=\"$url\">$log[timestamp]</a></td>
				<td>$script</td>
				<td>$type</td>
			</tr>
			";
		}
	?>
</table>
