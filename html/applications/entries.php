<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$application = new Application($_GET['application_id']);

$template = new Template();
$template->blocks[] = new Block('applications/applicationInfo.inc',array('application'=>$application));
$template->blocks[] = new Block('applications/entryList.inc',array('application'=>$application));
echo $template->render();