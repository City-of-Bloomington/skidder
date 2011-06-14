<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Application $this->application
 * @param string $this->return_url
 */
$name = View::escape($this->application->getName());
?>
<h1>Subscribe to <?php echo $name; ?></h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Subscription Info</legend>
		<input name="application_id" type="hidden" value="<?php echo $this->application->getId(); ?>" />
		<input name="return_url" type="hidden" value="<?php echo View::escape($this->return_url); ?>" />

		<p>Enter the how long to wait before repeating error reports about the same script.</p>
		<table>

			<tr><td><label for="subscription-waitTime">Wait Time (in seconds)</label></td>
				<td><input name="subscription[waitTime]" id="subscription-waitTime" size="4" />
				</td>
			</tr>

		</table>

		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='<?php echo $this->return_url; ?>';">
			Cancel
		</button>
	</fieldset>
</form>
