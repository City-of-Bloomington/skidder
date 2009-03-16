<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Subscription extends ActiveRecord
{
	private $id;
	private $application_id;
	private $person_id;
	private $waitTime;

	private $application;
	private $person;

	/**
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 *
	 * @param int $id
	 */
	public function __construct($id=null)
	{
		if ($id) {
			$PDO = Database::getConnection();
			$query = $PDO->prepare('select * from subscriptions where id=?');
			$query->execute(array($id));

			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			if (!count($result)) {
				throw new Exception('subscriptions/unknownSubscription');
			}
			foreach ($result[0] as $field=>$value) {
				if ($value) {
					$this->$field = $value;
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		// Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->application_id || !$this->person_id) {
			throw new Exception('missingRequriedFields');
		}
	}

	/**
	 * Saves this record back to the database
	 *
	 * This generates generic SQL that should work right away.
	 * You can replace this $fields code with your own custom SQL
	 * for each property of this class,
	 */
	public function save()
	{
		$this->validate();

		$fields = array();
		$fields['application_id'] = $this->application_id;
		$fields['person_id'] = $this->person_id;
		$fields['waitTime'] = $this->waitTime ? $this->waitTime : 0;

		// Split the fields up into a preparedFields array and a values array.
		// PDO->execute cannot take an associative array for values, so we have
		// to strip out the keys from $fields
		$preparedFields = array();
		foreach ($fields as $key=>$value) {
			$preparedFields[] = "$key=?";
			$values[] = $value;
		}
		$preparedFields = implode(",",$preparedFields);


		if ($this->id) {
			$this->update($values,$preparedFields);
		}
		else {
			$this->insert($values,$preparedFields);
		}
	}

	private function update($values,$preparedFields)
	{
		$PDO = Database::getConnection();

		$sql = "update subscriptions set $preparedFields where id={$this->id}";
		$query = $PDO->prepare($sql);
		$query->execute($values);
	}

	private function insert($values,$preparedFields)
	{
		$PDO = Database::getConnection();

		$sql = "insert subscriptions set $preparedFields";
		$query = $PDO->prepare($sql);
		$query->execute($values);
		$this->id = $PDO->lastInsertID();
	}

	public function delete()
	{
		if ($this->id) {
			$pdo = Database::getConnection();

			$query = $pdo->prepare('delete from notifications where subscription_id=?');
			$query->execute(array($this->id));

			$query = $pdo->prepare('delete from subscriptions where id=?');
			$query->execute(array($this->id));
		}
	}

	//----------------------------------------------------------------
	// Generic Getters
	//----------------------------------------------------------------

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return int
	 */
	public function getApplication_id()
	{
		return $this->application_id;
	}

	/**
	 * @return int
	 */
	public function getPerson_id()
	{
		return $this->person_id;
	}

	/**
	 * Returns the date/time in the desired format
	 * Format can be specified using either the strftime() or the date() syntax
	 *
	 * @param string $format
	 */
	public function getLastNotified($format=null)
	{
		if ($format && $this->lastNotified) {
			if (strpos($format,'%')!==false) {
				return strftime($format,$this->lastNotified);
			}
			else {
				return date($format,$this->lastNotified);
			}
		}
		else {
			return $this->lastNotified;
		}
	}

	/**
	 * @return int
	 */
	public function getWaitTime()
	{
		return $this->waitTime;
	}

	/**
	 * @return Application
	 */
	public function getApplication()
	{
		if ($this->application_id) {
			if (!$this->application) {
				$this->application = new Application($this->application_id);
			}
			return $this->application;
		}
		return null;
	}

	/**
	 * @return Person
	 */
	public function getPerson()
	{
		if ($this->person_id) {
			if (!$this->person) {
				$this->person = new Person($this->person_id);
			}
			return $this->person;
		}
		return null;
	}

	//----------------------------------------------------------------
	// Generic Setters
	//----------------------------------------------------------------

	/**
	 * @param int $int
	 */
	public function setApplication_id($int)
	{
		$this->application = new Application($int);
		$this->application_id = $int;
	}

	/**
	 * @param int $int
	 */
	public function setPerson_id($int)
	{
		$this->person = new Person($int);
		$this->person_id = $int;
	}

	/**
	 * @param int $int
	 */
	public function setWaitTime($int)
	{
		$this->waitTime = preg_replace("/[^0-9]/","",$int);
	}

	/**
	 * @param Application $application
	 */
	public function setApplication($application)
	{
		$this->application_id = $application->getId();
		$this->application = $application;
	}

	/**
	 * @param Person $person
	 */
	public function setPerson($person)
	{
		$this->person_id = $person->getId();
		$this->person = $person;
	}


	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	/**
	 * Determines whether we should send out a notification to this person
	 * People should only receive an email every ($waitTime) seconds for
	 * any given script error.  We don't want to flood their inboxes
	 */
	public function wantsNotification($script)
	{
		$pdo = Database::getConnection();
		$sql = "select unix_timestamp(timestamp) as timestamp
				from notifications where subscription_id=? and script=?";
		$query = $pdo->prepare($sql);
		$query->execute(array($this->id,$script));
		$result = $query->fetchAll(PDO::FETCH_ASSOC);
		if (count($result)) {
			$lastNotified = $result[0]['timestamp'];
			if ($lastNotified + $this->waitTime <= time()) {
				return true;
			}
			return false;
		}
		return true;
	}

	/**
	 * Sends out a notification to the person
	 * @param string $script
	 * @param string $message
	 */
	public function notify($script,$message)
	{
		$subject = "{$this->getApplication()->getName()} error";
		mail($this->getPerson()->getEmail(),
			 $subject,
			 $message,
			 "From: skidder@$_SERVER[SERVER_NAME]");

		$pdo = Database::getConnection();
		$sql = "insert notifications values(?,?,now()) on duplicate key update timestamp=now()";
		$query = $pdo->prepare($sql);
		$query->execute(array($this->id,$script));
	}

	/**
	 * @return boolean
	 */
	public function permitsEditingBy(User $user)
	{
		return ($user->hasRole('Administrator') || $this->person_id==$user->getPerson_id());
	}
}
