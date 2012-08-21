<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../configuration.inc';

$_SERVER['SERVER_NAME'] = 'localhost';

$application = new Application(1);
$time = $application->log(array(
	'script'=>'/test',
	'type'=>'test',
	'message'=>'This was an error test entry'
));
echo "$time\n";