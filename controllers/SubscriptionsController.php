<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class SubscriptionsController extends Controller
{
	public function index()
	{
		$this->template->blocks[] = new Block(
			'subscriptions/subscriptionList.inc',
			array('subscriptionList'=>$_SESSION['USER']->getSubscriptions())
		);
	}

	public function update()
	{
		$return_url = !empty($_REQUEST['return_url'])
			? $_REQUEST['return_url']
			: null;

		$sub = !empty($_REQUEST['subscription_id'])
			? new Subscription($_REQUEST['subscription_id'])
			: new Subscription();

		if ($sub->permitsEditingBy($_SESSION['USER'])) {
			$sub->setPerson($_SESSION['USER']);

			if (isset($_REQUEST['application_id'])) {
				$sub->setApplication_id($_REQUEST['application_id']);
				if (!$return_url) { $return_url = $sub->getApplication()->getURL(); }
			}

			if (isset($_POST['application_id'])) {
				$sub->handleUpdate($_POST);
				try {
					$sub->save();

					header("Location: $return_url");
					exit();
				}
				catch (Exception $e) {
					$_SESSION['errorMessages'][] = $e;
					header('Location: '.BASE_URL.'/applications');
					exit();
				}
			}

			if (!$return_url) { $return_url = BASE_URL.'/applications'; }

			$this->template->blocks[] = new Block(
				'subscriptions/updateSubscriptionForm.inc',
				array('subscription'=>$sub, 'return_url'=>$return_url)
			);
		}
		else {
			// Not allowed to edit this subscription
			if (!$return_url) { $return_url = BASE_URL.'/applications'; }
			header("Location: $return_url");
			exit();
		}
	}

	public function delete()
	{
		$subscription = new Subscription($_GET['subscription_id']);
		$application = $subscription->getApplication();
		if ($subscription->permitsEditingBy($_SESSION['USER'])) {
			$subscription->delete();
		}

		$return_url = !empty($_GET['return_url']) ? $_GET['return_url'] : $application->getURL();
		header("Location: $return_url");
		exit();
	}
}