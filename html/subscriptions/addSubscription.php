<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST application_id
 * @param REQUEST return_url
 */
verifyUser();
$application = new Application($_REQUEST['application_id']);
if (isset($_POST['subscription'])) {
	$subscription = new Subscription();
	$subscription->setApplication($application);
	$subscription->setPerson_id($_SESSION['USER']->getPerson_id());
	$subscription->setWaitTime($_POST['subscription']['waitTime']);

	try {
		$subscription->save();
		header('Location: '.$_POST['return_url']);
		exit();
	}
	catch(Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->blocks[] = new Block('subscriptions/addSubscriptionForm.inc',
								array('application'=>$application,
									  'return_url'=>$_REQUEST['return_url']));
echo $template->render();
