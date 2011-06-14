<?php
/**
 * Displays the full record of entries
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
 * @param int $this->timestamp
 */
$query = $this->application->getEntries(array('timestamp'=>$this->timestamp));
foreach ($query as $log) {
	$script = View::escape($log['script']);
	$type = View::escape($log['type']);
	$message = View::escape($log['message']);
	echo "
	<div class=\"interfaceBox\">
		<h1>$log[timestamp] - $script</h1>
		<p>$type</p>
		<code>
<pre>
$message
</pre>
		</code>
	</div>
	";
}
$query->closeCursor();
