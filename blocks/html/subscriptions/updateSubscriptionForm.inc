<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Subscription $this->subscription
 * @param string $this->return_url
 */
$fields = array('id', 'application_id', 'waitTime');
foreach ($fields as $f) {
	$get = 'get'.ucfirst($f);
	$$f = $this->subscription->$get();
}

$name = View::escape($this->subscription->getApplication()->getName());
$title = $id ? 'Edit subscription to' : 'Subscribe to';
?>
<h2><?php echo "$title $name"; ?></h2>
<form method="post" action="<?php echo BASE_URI; ?>/subscriptions/update">
	<fieldset><legend>Subscription Info</legend>
		<input name="subscription_id" type="hidden" value="<?php echo $id ?>" />
		<input name="application_id" type="hidden" value="<?php echo $application_id; ?>" />
		<input name="return_url" type="hidden" value="<?php echo View::escape($this->return_url); ?>" />

		<p>Enter the how long to wait before repeating error reports about the same script.</p>
		<table>

			<tr><td><label for="waitTime">Wait Time (in seconds)</label></td>
				<td><input name="waitTime" id="waitTime" size="4" value="<?php echo $waitTime; ?>" />
				</td>
			</tr>

		</table>

		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='<?php echo $this->return_url; ?>';">
			Cancel
		</button>
	</fieldset>
</form>
