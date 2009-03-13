<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
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
	 */
	public function log($post)
	{
		$script = trim($post['script']);
		$type = trim($post['type']);
		$message = trim($post['message']);
		if (!$script || !$type || !$message) {
			throw new Exception('missingRequiredFields');
		}

		$pdo = Database::getConnection();
		$query = $pdo->prepare('insert entries values(?,now(),?,?,?)');
		$query->execute(array($this->id,$script,$type,$message));
	}

	/**
	 * Returns all the scripts and count of the log entries for each script
	 *
	 * @return array (script=>,count=>)
	 */
	public function getEntryScripts()
	{
		$pdo = Database::getConnection();
		$sql = "select distinct script,count(*) as count from entries
				where application_id=?
				group by script order by timestamp desc";
		$query = $pdo->prepare($sql);
		$query->execute(array($this->id));
		$result = $query->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	/**
	 * Returns all the log entries for this application matching any fields given
	 * @param array $fields
	 */
	public function getEntries($fields=null)
	{
		$options[] = 'application_id=?';
		$parameters[] = $this->id;

		if (is_array($fields)) {
			if (array_key_exists('script',$fields)) {
				$options[] = 'script=?';
				$parameters[] = $fields['script'];
			}
			if (array_key_exists('timestamp',$fields)) {
				$options[] = 'timestamp=from_unixtime(?)';
				$parameters[] = $fields['timestamp'];
			}
		}
		$options = implode(' and ',$options);

		$sql = "select * from entries where $options order by timestamp desc";
		$pdo = Database::getConnection();
		$query = $pdo->prepare($sql);
		$query->execute($parameters);
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}
}
