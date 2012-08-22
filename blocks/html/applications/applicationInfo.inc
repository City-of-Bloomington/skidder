<?php
/**
 * @copyright 2009-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Application $this->application
 */
$name = View::escape($this->application->getName());
$subscriptionButton = '';
if (isset($_SESSION['USER'])) {
	$subscription = $_SESSION['USER']->getSubscription($this->application);

	if ($subscription) {
		$url = new URL(BASE_URL.'/subscriptions/delete');
		$url->subscription_id = $subscription->getId();
		$url->return_url = $_SERVER['REQUEST_URI'];
		$subscriptionButton = "<a class=\"unsubscribe button\" href=\"$url\">Unsubscribe</a>";
	}
	else {
		$url = new URL(BASE_URL.'/subscriptions/update');
		$url->application_id = $this->application->getId();
		$url->return_url = $_SERVER['REQUEST_URI'];
		$subscriptionButton = "<a class=\"subscribe button\" href=\"$url\">Subscribe</a>";
	}
}
echo "
<h2><a href=\"{$this->application->getURL()}\">$name</a>
	({$this->application->getIpAddress()})
	$subscriptionButton
</h2>
";
