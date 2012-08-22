<?php
/**
 * @copyright 2009-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Application $this->application
 */
$fields = array('id', 'name', 'ipAddress');
foreach ($fields as $f) {
	$get = 'get'.ucfirst($f);
	$$f = View::escape($this->application->$get());
}
$title = $id ? 'Edit' : 'Add';
?>
<h2><?php echo $title; ?> Application</h2>
<form method="post" action="<?php echo BASE_URI; ?>/applications/update">
	<fieldset><legend>Application Info</legend>
		<input name="application_id" type="hidden" value="<?php echo $id; ?>" />
		<table>

			<tr><td><label for="name" class="required">Name</label></td>
				<td><input name="name" id="name" value="<?php echo $name; ?>" />
				</td>
			</tr>

			<tr><td><label for="ipAddress" class="required">IP Address</label></td>
				<td><input name="ipAddress" id="ipAddress" value="<?php echo $ipAddress; ?>" />
				</td>
			</tr>

		</table>

		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='<?php echo BASE_URL; ?>/applications';">
			Cancel
		</button>
	</fieldset>
</form>
