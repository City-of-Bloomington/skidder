<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET subscription_id
 * @param GET return_url
 */
verifyUser();
$subscription = new Subscription($_GET['subscription_id']);
if ($subscription->permitsEditingBy($_SESSION['USER'])) {
	$subscription->delete();
}
header('Location: '.$_GET['return_url']);
