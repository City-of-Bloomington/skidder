<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET application_id
 * @param GET timestamp
 */
verifyUser();
$application = new Application($_GET['application_id']);
$entries = $application->getEntries(array('timestamp'=>$_GET['timestamp']));

$template = new Template();
$template->blocks[] = new Block('applications/applicationInfo.inc',
								array('application'=>$application));
$template->blocks[] = new Block('applications/entryFullDisplay.inc',
								array('entries'=>$entries));
echo $template->render();
