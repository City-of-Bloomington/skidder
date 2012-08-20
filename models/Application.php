<?php
/**
 * @copyright 2009-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Application extends ActiveRecord
{
	protected $tablename = 'applications';
	public static $reportable_entry_fields = array('script', 'type', 'request_uri');

	/**
	 * Populates the object with data
	 *
	 * Passing in an associative array of data will populate this object without
	 * hitting the database.
	 *
	 * Passing in a scalar will load the data from the database.
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 *
	 * @param int $id
	 */
	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
				$result = $id;
			}
			else {
				$zend_db = Database::getConnection();
				$sql = 'select * from applications where id=?';
				$result = $zend_db->fetchRow($sql, array($id));
			}

			if ($result) {
				$this->data = $result;
			}
			else {
				throw new Exception('applications/unknownApplication');
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
		if (!$this->getName()) {
			throw new Exception('missingName');
		}

		if (!$this->getIpAddress()
			|| !preg_match(
					'/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/',
					$this->getIpAddress()
				)) {
			throw new Exception('applications/invalidIpAddress');
		}
	}

	public function save()
	{
		parent::save();
	}

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()        { return parent::get('id');        }
	public function getName()      { return parent::get('name');      }
	public function getIpAddress() { return parent::get('ipAddress'); }

	public function setName     ($s) { parent::set('name',      $s);                              }
	public function setIpAddress($s) { parent::set('ipAddress', preg_replace('/[^0-9.]/','',$s)); }

	public function handleUpdate($post)
	{
		if (isset($post['name']))      { $this->setName     ($post['name']);      }
		if (isset($post['ipAddress'])) { $this->setIpAddress($post['ipAddress']); }
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
		return BASE_URL.'/applications/view?application_id='.$this->getId();
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

		$type    = trim($post['type']);
		$message = trim($post['message']);
		if (!$script || !$type || !$message) {
			throw new Exception('missingRequiredFields');
		}

		$zend_db = Database::getConnection();
		$zend_db->insert('entries', array(
			'application_id'=>$this->getId(),
			'request_uri'   =>$request_uri,
			'script'        =>$script,
			'type'          =>$type,
			'message'       =>$message
		));

		$sql = 'select unix_timestamp(max(timestamp)) as timestamp from entries where application_id=?';
		$post['timestamp'] = $zend_db->fetchOne($sql, array($this->getId()));


		// Notify all the subscribers of this new error
		$template = new Template('default','txt');
		$template->blocks[] = new Block('applications/applicationInfo.inc', array('application'=>$this));
		$template->blocks[] = new Block('applications/entryFullDisplay.inc',array('application'=>$this,'entries'=>array($post)));
		$message = $template->render();
		foreach ($this->getSubscriptions() as $subscriber) {
			if ($subscriber->wantsNotification($script)) {
				$subscriber->notify($script,$message);
			}
		}

		return $post['timestamp'];
	}

	/**
	 * Returns a count of distinct log entries for the field given
	 *
	 * @param string $field A field in the entries table
	 * @return array
	 */
	public function distinct($field)
	{
		if (in_array($field, self::$reportable_entry_fields)) {
			$zend_db = Database::getConnection();
			$sql = "select distinct $field,count(*) as count from entries
					where application_id=?
					group by $field order by timestamp desc";
			return $zend_db->query($sql, array($this->getId()));
		}
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
		$zend_db = Database::getConnection();
		$select = $zend_db->select()->from('entries');

		$select->where('application_id=?', $this->getId());

		if (is_array($fields)) {
			foreach ($fields as $field=>$value) {
				switch ($field) {
					case 'timestamp':
						$select->where('timestamp=from_unixtime(?)', $value);
						break;
					default:
						$select->where("$field=?", $value);
				}
			}
		}

		$select->order(array('timestamp desc'));
		return $select->query();
	}
	/**
	 * @return int
	 */
	public function getEntryCount()
	{
		$zend_db = Database::getConnection();
		return $zend_db->fetchOne('select count(*) as count from entries where application_id=?', array($this->getId()));
	}

	/**
	 * Returns all the subscriptions for this application
	 * @return SubscriptionList
	 */
	public function getSubscriptions()
	{
		return new SubscriptionList(array('application_id'=>$this->getId()));
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

		$parameters[] = $this->getId();

		if ($script) {
			$notificationSQL.= ' and script=?';
			$entriesSQL     .= ' and script=?';
			$parameters[] = $script;
		}
		if ($timestamp) {
			$notificationSQL.= ' and timestamp=?';
			$entriesSQL     .= ' and timestamp=?';
			$parameters[] = date('Y-m-d H:i:s', $timestamp);
		}

		$zend_db = Database::getConnection();
		$zend_db->query($notificationSQL, $parameters);
		$zend_db->query($entriesSQL,      $parameters);
	}
}
