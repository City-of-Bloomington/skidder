<?php
/**
 * @copyright 2007-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$_SESSION['errorMessages'][] = new Exception('noJavascript');
$template = new Template();
echo $template->render();
