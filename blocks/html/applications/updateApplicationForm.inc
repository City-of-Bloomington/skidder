<?php
/**
 * @copyright Copyright (C) 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Application $this->application
 */
?>
<h1>Update Application</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Application Info</legend>
		<input name="application_id" type="hidden" value="<?php echo $this->application->getId(); ?>" />
		<table>

			<tr><td><label for="application-name" class="required">Name</label></td>
				<td><input name="application[name]" id="application-name" value="<?php echo View::escape($this->application->getName()); ?>" />
				</td>
			</tr>

			<tr><td><label for="application-ip_address" class="required">IP Address</label></td>
				<td><input name="application[ip_address]" id="application-ip_address" value="<?php echo $this->application->getIp_address(); ?>" />
				</td>
			</tr>

		</table>

		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='<?php echo BASE_URL; ?>/applications';">
			Cancel
		</button>
	</fieldset>
</form>
