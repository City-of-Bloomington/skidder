<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Application $this->application
 */
$name = View::escape($this->application->getName());
$subscriptionButton = '';
if (isset($_SESSION['USER'])) {
	if ($_SESSION['USER']->hasSubscription($this->application)) {
		$url = new URL(BASE_URL.'/subscriptions/deleteSubscription.php');
		$url->subscription_id = $_SESSION['USER']->getSubscription($this->application)->getId();
		$url->return_url = $_SERVER['REQUEST_URI'];
		$subscriptionButton = "<a class=\"unsubscribe button\" href=\"$url\">Unsubscribe</a>";
	}
	else {
		$url = new URL(BASE_URL.'/subscriptions/addSubscription.php');
		$url->application_id = $this->application->getId();
		$url->return_url = $_SERVER['REQUEST_URI'];
		$subscriptionButton = "<a class=\"subscribe button\" href=\"$url\">Subscribe</a>";
	}
}
echo "
<h1>$name $subscriptionButton</h1>
<h2>{$this->application->getIp_address()}</h2>
";
