<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Subscription $this->subscription
 */
$name = View::escape($this->subscription->getApplication()->getName());
?>
<h1>Update Subscription to <?php echo $name; ?></h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Subscription Info</legend>
		<input name="subscription_id" type="hidden" value="<?php echo $this->subscription->getId(); ?>" />

		<p>Enter the how long to wait before repeating error reports about the same script.</p>
		<table>

			<tr><td><label for="subscription-waitTime">Wait Time (in seconds)</label></td>
				<td><input name="subscription[waitTime]" id="subscription-waitTime" size="4" value="<?php echo $this->subscription->getWaitTime(); ?>" />
				</td>
			</tr>

		</table>

		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='<?php echo BASE_URL; ?>/subscriptions';">
			Cancel
		</button>
	</fieldset>
</form>
