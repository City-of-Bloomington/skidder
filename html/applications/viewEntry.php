<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET application_id
 * @param GET timestamp
 */
verifyUser();
$application = new Application($_GET['application_id']);

$template = new Template();
$template->blocks[] = new Block('applications/applicationInfo.inc',
								array('application'=>$application));
$template->blocks[] = new Block('applications/entryFullDisplay.inc',
								array('application'=>$application,
									  'timestamp'=>$_GET['timestamp']));
echo $template->render();
