<?php
/**
 * @copyright Copyright (C) 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$applicationList = new ApplicationList();
$applicationList->find();

$template = new Template();
$template->blocks[] = new Block('applications/applicationList.inc',
								array('applicationList'=>$applicationList));
echo $template->render();