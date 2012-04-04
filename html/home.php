<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (isset($_POST['application_id'])) {
	try {
		$application = new Application($_POST['application_id']);
		if ($application->getIp_address() == $_SERVER['REMOTE_ADDR']) {
			$timestamp = $application->log($_POST);
			header('HTTP/1.1 201 Created');

			$_POST['timestamp'] = $timestamp;
			$template = new Template('default','txt');
			$template->blocks[] = new Block('applications/applicationInfo.inc',
											array('application'=>$application));
			$template->blocks[] = new Block('applications/entryFullDisplay.inc',
											array('entries'=>array($_POST)));
			$message = $template->render();
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
