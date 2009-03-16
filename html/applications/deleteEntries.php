<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET application_id
 * @param GET script
 * @param GET timestamp
 * @param GET return_url
 */
verifyUser();

$application = new Application($_GET['application_id']);
$script = isset($_GET['script']) ? $_GET['script'] : null;
$timestamp = isset($_GET['timestamp']) ? $_GET['timestamp'] : null;

$application->deleteEntries($script,$timestamp);

header("Location: $_GET[return_url]");
