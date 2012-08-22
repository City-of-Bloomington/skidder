<?php
/**
 * @copyright 2009-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param ApplicationList $this->applicationList
 */
?>
<div class="interfaceBox">
	<h2><?php
			if (userIsAllowed('applications')) {
				echo "<a class=\"add button\" href=\"".BASE_URI."/applications/update\">Add</a>";
			}
		?>
		Applications
	</h2>
	<table>
		<tr><th></th>
			<th>Name</th>
			<th>IP Address</th>
			<th>Errors</th>
		</tr>
		<?php
			foreach ($this->applicationList as $application) {
				$editButton = '';
				if (userIsAllowed('applications')) {
					$url = BASE_URI.'/applications/update?application_id='.$application->getId();
					$editButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";
				}

				$name = View::escape($application->getName());
				echo "
				<tr><td>$editButton</td>
					<td><a href=\"{$application->getURL()}\">$name</a></td>
					<td>{$application->getIpAddress()}</td>
					<td>{$application->getEntryCount()}</td>
				</tr>
				";
			}
		?>
	</table>
</div>
