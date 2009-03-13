<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (isset($_POST['application_id'])) {
	try {
		$application = new Application($_POST['application_id']);
		if ($application->getIp_address() == $_SERVER['REMOTE_ADDR']) {
			$application->log($_POST);
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
	if (isset($_SESSION['USER'])) {
		include APPLICATION_HOME.'/html/applications/home.php';
	}
	else {
		$template = new Template();
		echo $template->render();
	}
}
