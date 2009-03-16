<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST subscription_id
 */
verifyUser();

$subscription = new Subscription($_REQUEST['subscription_id']);
if (!$subscription->permitsEditingBy($_SESSION['USER'])) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL.'/subscriptions');
	exit();
}

if (isset($_POST['subscription'])) {
	$subscription->setWaitTime($_POST['subscription']['waitTime']);
	try {
		$subscription->save();
		header('Location: '.BASE_URL.'/subscriptions');
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->blocks[] = new Block('subscriptions/updateSubscriptionForm.inc',
								array('subscription'=>$subscription));
echo $template->render();
