<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Application extends ActiveRecord
{
	private $id;
	private $name;
	private $ip_address;

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
			$query = $PDO->prepare('select * from applications where id=?');
			$query->execute(array($id));

			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			if (!count($result)) {
				throw new Exception('applications/unknownApplication');
			}
			foreach ($result[0] as $field=>$value) {
				if ($value) $this->$field = $value;
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
		if (!$this->name) {
			throw new Exception('missingName');
		}

		if (!$this->ip_address) {
			throw new Exception('applications/invalidIpAddress');
		}

		if (!preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/',$this->ip_address)) {
			throw new Exception('applications/invalidIPAddress');
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
		$fields['name'] = $this->name;
		$fields['ip_address'] = $this->ip_address;

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

		$sql = "update applications set $preparedFields where id={$this->id}";
		$query = $PDO->prepare($sql);
		$query->execute($values);
	}

	private function insert($values,$preparedFields)
	{
		$PDO = Database::getConnection();

		$sql = "insert applications set $preparedFields";
		$query = $PDO->prepare($sql);
		$query->execute($values);
		$this->id = $PDO->lastInsertID();
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
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getIp_address()
	{
		return $this->ip_address;
	}

	//----------------------------------------------------------------
	// Generic Setters
	//----------------------------------------------------------------

	/**
	 * @param string $string
	 */
	public function setName($string)
	{
		$this->name = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setIp_address($string)
	{
		$this->ip_address = preg_replace('/[^0-9.]/','',$string);
	}


	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	/**
	 * @return string
	 */
	public function getURL()
	{
		$url = new URL(BASE_URL.'/applications/viewApplication.php');
		$url->application_id = $this->id;
		return $url;
	}
	/**
	 * Adds a new log entry to the database
	 * @param array $post An associative array containg all the information needed
	 * 		$post['script']
	 * 		$post['type']
	 * 		$post['message']
	 * 	@return string The MySQL-formatted timestamp for this log entry
	 */
	public function log($post)
	{
		$request_uri = trim($post['script']);
		$t = explode('?',$request_uri);
		$script = $t[0];

		$type = trim($post['type']);
		$message = trim($post['message']);
		if (!$script || !$type || !$message) {
			throw new Exception('missingRequiredFields');
		}

		$pdo = Database::getConnection();

		// These are split out this way to make it easier to add in
		// anything we might need to do to support large $messages
		// If MySQL max_allowed_packet is too small, I'm still not sure
		// what we can do.
		$sql = "insert entries
				(application_id, timestamp, request_uri, script, type, message)
				values(?, now(), ?, ?, ?, ?)";
		$query = $pdo->prepare($sql);
		$query->bindParam(1, $this->id);
		$query->bindParam(2, $request_uri);
		$query->bindParam(3, $script);
		$query->bindParam(4, $type);
		$query->bindParam(5, $message);
		$query->execute();

		$query = $pdo->prepare('select max(timestamp) as timestamp from entries where application_id=?');
		$query->execute(array($this->id));
		$result = $query->fetchAll(PDO::FETCH_ASSOC);
		return $result[0]['timestamp'];
	}

	/**
	 * Returns a count distinct log entries for the field given
	 *
	 * @param string $field A field in the entries table
	 * @return array
	 */
	public function distinct($field)
	{
		$field = str_replace('`','``',$field);
		$pdo = Database::getConnection();
		$sql = "select distinct $field,count(*) as count from entries
				where application_id=?
				group by $field order by timestamp desc";
		$query = $pdo->prepare($sql);
		$query->execute(array($this->id));
		$result = $query->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	/**
	 * Returns a query containing the log entries for this application matching any fields given
	 *
	 * You must fetch each row from the returned query one at a time.  Since these queries
	 * are unbuffered you cannot do any other database calls until you call $query->closeCursor()
	 *
	 * We are returning a query, instead of a result set, since the data returned can be very
	 * large.  There is usually not enough memory in PHP to handle the entire result at once.
	 * It is up to you to go through the query, one row at a time, and do something with each
	 * row.  Remember to close the query when you're done, or you won't be able to do any other
	 * database work.
	 *
	 * @param array $fields
	 * @return PDOStatement
	 */
	public function getEntries($fields=null)
	{
		$options[] = 'application_id=?';
		$parameters[] = $this->id;

		if (is_array($fields)) {
			foreach ($fields as $field=>$value) {
				switch ($field) {
					case 'timestamp':
						$options[] = 'timestamp=from_unixtime(?)';
						$parameters[] = $value;
						break;
					default:
						$options[] = "$field=?";
						$parameters[] = $value;
				}
			}
		}
		$options = implode(' and ',$options);

		$sql = "select * from entries where $options order by timestamp desc";
		$pdo = Database::getConnection();
		$query = $pdo->prepare($sql);
		$query->execute($parameters);
		return $query;
	}
	/**
	 * @return int
	 */
	public function getEntryCount()
	{
		$pdo = Database::getConnection();
		$query = $pdo->prepare('select count(*) as count from entries where application_id=?');
		$query->execute(array($this->id));
		$result = $query->fetchAll(PDO::FETCH_ASSOC);
		return $result[0]['count'];
	}

	/**
	 * Returns all the subscriptions for this application
	 * @return SubscriptionList
	 */
	public function getSubscriptions()
	{
		return new SubscriptionList(array('application_id'=>$this->id));
	}

	/**
	 * Purges the logs from the system
	 *
	 * @param string $script
	 * @param int $timestamp A Unix Timestamp
	 */
	public function deleteEntries($script=null,$timestamp=null)
	{
		$notificationSQL = "delete from notifications
							where subscription_id in
							(select id from subscriptions where application_id=?)";
		$entriesSQL = "delete from entries where application_id=?";

		$parameters[] = $this->id;

		if ($script) {
			$notificationSQL.= ' and script=?';
			$entriesSQL.= ' and script=?';
			$parameters[] = $script;
		}
		if ($timestamp) {
			$notificationSQL.= ' and timestamp=?';
			$entriesSQL.= ' and timestamp=?';
			$parameters[] = date('Y-m-d H:i:s',$timestamp);
		}

		$pdo = Database::getConnection();

		$query = $pdo->prepare($notificationSQL);
		$query->execute($parameters);

		$query = $pdo->prepare($entriesSQL);
		$query->execute($parameters);
	}
}
