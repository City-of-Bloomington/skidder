<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class IndexController extends Controller
{
	/**
	 * Checks for applications posting a log entry
	 *
	 * If an application is posting a log entry, we handle it
	 * in a RESTful manner.  Otherwise, we just display the
	 * about information for this application
	 */
	public function index()
	{
		if (isset($_POST['application_id'])) {
			try {
				$application = new Application($_POST['application_id']);
				if ($application->getIpAddress() == $_SERVER['REMOTE_ADDR']) {
					$timestamp = $application->log($_POST);
					header('HTTP/1.1 201 Created');
				}
				else {
					throw new Exception('notAllowed');
				}
			}
			catch (Exception $e) {
				switch ($e->getMessage()) {
					case 'missingRequiredFields':
						header('HTTP/1.1 415 Unsupported Media Type');
						break;
					default:
						header('HTTP/1.1 403 Forbidden');
				}
			}
		}
		else {
			$template = new Template();
			$template->blocks[] = new Block('about.inc');
			echo $template->render();
		}
	}
}