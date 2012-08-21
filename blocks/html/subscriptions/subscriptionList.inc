<?php
/**
 * @copyright 2009-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param SubscriptionList $this->subscriptions
 */
?>
<div class="subscriptionList">
	<h2>Subscriptions</h2>
	<table>
		<thead>
			<tr><th></th>
				<th>Application</th>
				<th>IP Address</th>
				<th>Wait Time</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->subscriptionList as $subscription) {
				$editButton = '';
				if (userIsAllowed('subscriptions')
					&& $subscription->permitsEditingBy($_SESSION['USER'])) {
					$url = BASE_URI.'/subscriptions/update?subscription_id='.$subscription->getId();
					$url.= ';return_url='.$_SERVER['REQUEST_URI'];
					$editButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";

					$url = BASE_URI.'/subscriptions/delete?subscription_id='.$subscription->getId();
					$url.= ';return_url='.$_SERVER['REQUEST_URI'];
					$deleteButton = "<a class=\"delete button\" href=\"$url\">Delete</a>";
				}
				$name = View::escape($subscription->getApplication()->getName());
				$ipAddress = $subscription->getApplication()->getIpAddress();
				echo "
				<tr><td>$editButton</td>
					<td>$name</td>
					<td>$ipAddress</td>
					<td>{$subscription->getWaitTime()}</td>
					<td>$deleteButton</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>
