<?php
/**
 * @copyright Copyright (C) 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div class="interfaceBox">
	<h1><?php
			if (userHasRole('Administrator')) {
				echo "
				<a class=\"add button\" href=\"".BASE_URL."/applications/addApplication.php\">
					Add
				</a>
				";
			}
		?>
		Applications
	</h1>
	<table>
		<tr><th></th>
			<th>Name</th>
			<th>IP Address</th>
			<th>Errors</th>
		</tr>
		<?php
			foreach ($this->applicationList as $application) {
				$editButton = '';
				if (userHasRole('Administrator')) {
					$url = new URL(BASE_URL.'/applications/updateApplication.php');
					$url->application_id = $application->getId();
					$editButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";
				}

				$name = View::escape($application->getName());
				echo "
				<tr><td>$editButton</td>
					<td><a href=\"{$application->getURL()}\">$name</a></td>
					<td>{$application->getIp_address()}</td>
					<td>{$application->getEntryCount()}</td>
				</tr>
				";
			}
		?>
	</table>
</div>
