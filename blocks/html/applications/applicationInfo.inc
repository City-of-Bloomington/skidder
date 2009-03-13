<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Application $this->application
 */
$name = View::escape($this->application->getName());
echo "
<h1>$name</h1>
<h2>{$this->application->getIp_address()}</h2>
";
