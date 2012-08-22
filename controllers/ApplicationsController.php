<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class ApplicationsController extends Controller
{
	public function index()
	{
		$list = new ApplicationList();
		$list->find();

		$this->template->blocks[] = new Block(
			'applications/applicationList.inc', array('applicationList'=>$list)
		);
	}

	public function view()
	{
		try {
			$a = new Application($_GET['application_id']);
		}
		catch (Exception $e) {
			$_SESSION['errorMessages'][] = $e;
			header('Location: '.BASE_URL.'/applications');
			exit();
		}

		$this->template->blocks[] = new Block('applications/applicationInfo.inc',array('application'=>$a));
		$this->template->blocks[] = new Block('applications/logSummary.inc',     array('application'=>$a));
	}

	public function update()
	{
		$application = isset($_REQUEST['application_id'])
			? new Application($_REQUEST['application_id'])
			: new Application();
		if (isset($_POST['name'])) {
			$application->handleUpdate($_POST);
			try {
				$application->save();
				header('Location: '.BASE_URL.'/applications');
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}
		$this->template->blocks[] = new Block(
			'applications/updateApplicationForm.inc', array('application'=>$application)
		);
	}

	public function entries()
	{
		$application = new Application($_GET['application_id']);
		$this->template->blocks[] = new Block(
			'applications/applicationInfo.inc', array('application'=>$application)
		);
		$this->template->blocks[] = new Block(
			'applications/entryList.inc',      array('application'=>$application)
		);
	}

	public function viewEntry()
	{
		$application = new Application($_GET['application_id']);
		$this->template->blocks[] = new Block(
			'applications/applicationInfo.inc', array('application'=>$application)
		);
		$this->template->blocks[] = new Block(
			'applications/entryFullDisplay.inc',
			array('application'=>$application, 'timestamp'=>$_GET['timestamp'])
		);
	}

	public function deleteEntries()
	{
		$application = new Application($_GET['application_id']);
		$script = isset($_GET['script']) ? $_GET['script'] : null;
		$timestamp = isset($_GET['timestamp']) ? $_GET['timestamp'] : null;

		$application->deleteEntries($script,$timestamp);

		header("Location: $_GET[return_url]");
	}
}