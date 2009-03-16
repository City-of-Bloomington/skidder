<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
verifyUser();
$subscriptionList = new SubscriptionList(array('person_id'=>$_SESSION['USER']->getPerson_id()));

$template = new Template();
$template->blocks[] = new Block('subscriptions/subscriptionList.inc',
								array('subscriptionList'=>$subscriptionList));
echo $template->render();
