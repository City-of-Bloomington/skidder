<?php
/**
 * @copyright Copyright (C) 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<h1>Add Application</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Application Info</legend>
		<table>

			<tr><td><label for="application-name" class="required">Name</label></td>
				<td><input name="application[name]" id="application-name" />
				</td>
			</tr>

			<tr><td><label for="application-ip_address" class="required">IP Address</label></td>
				<td><input name="application[ip_address]" id="application-ip_address" />
				</td>
			</tr>

		</table>

		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='<?php echo BASE_URL; ?>/applications';">
			Cancel
		</button>
	</fieldset>
</form>
