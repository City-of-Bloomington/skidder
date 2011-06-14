<?php
/**
 * @copyright Copyright (C) 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST application_id
 */
verifyUser('Administrator');

$application = new Application($_REQUEST['application_id']);
if (isset($_POST['application'])) {
	foreach ($_POST['application'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$application->$set($value);
	}

	try {
		$application->save();
		header('Location: '.BASE_URL.'/applications');
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->blocks[] = new Block('applications/updateApplicationForm.inc',
								array('application'=>$application));
echo $template->render();
