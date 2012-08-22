<?php
/**
 * Test Skidder's own error handling
 *
 * If there's a problem with Skidder, we really need to know about it
 * quickly, since all our other applications are using it for their
 * error reporting.
 * The best way is to configure Skidder to EMAIL_ADMIN.  Errors from
 * skidder must be dealt with immediately
 *
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../configuration.inc';

// Let's cause an error
// Try to record a log without the required information
$application = new Application();
$application->log(array());