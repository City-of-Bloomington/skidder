<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param SubscriptionList
 */
?>
<div class="interfaceBox">
	<h1>Subscriptions</h1>
	<table>
		<tr><th></th>
			<th>Application</th>
			<th>IP Address</th>
			<th>Wait Time</th>
		</tr>
		<?php
			foreach ($this->subscriptionList as $subscription) {
				$editButton = '';
				if ($subscription->permitsEditingBy($_SESSION['USER'])) {
					$url = new URL(BASE_URL.'/subscriptions/updateSubscription.php');
					$url->subscription_id = $subscription->getId();
					$editButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";

					$url = new URL(BASE_URL.'/subscriptions/deleteSubscription.php');
					$url->subscription_id = $subscription->getId();
					$url->return_url = $_SERVER['REQUEST_URI'];
					$deleteButton = "<a class=\"delete button\" href\"$url\">Delete</a>";
				}
				$name = View::escape($subscription->getApplication()->getName());
				$ip_address = $subscription->getApplication()->getIp_address();
				echo "
				<tr><td>$editButton</td>
					<td>$name</td>
					<td>$ip_address</td>
					<td>{$subscription->getWaitTime()}</td>
				</tr>
				";
			}
		?>
	</table>
</div>
