<?php
/**
 * @copyright Copyright (C) 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
verifyUser('Administrator');

if (isset($_POST['application'])) {
	$application = new Application();
	foreach ($_POST['application'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$application->$set($value);
	}

	try {
		$application->save();
		header('Location: '.BASE_URL.'/applications');
		exit();
	}
	catch(Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->blocks[] = new Block('applications/addApplicationForm.inc');
echo $template->render();
