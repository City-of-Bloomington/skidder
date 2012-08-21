<?php
/**
 * @copyright 2009-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Subscription extends ActiveRecord
{
	protected $tablename = 'subscriptions';

	protected $application;
	protected $person;

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
				$sql = 'select * from subscriptions where id=?';
				$result = $zend_db->fetchRow($sql, array($id));
			}

			if ($result) {
				$this->data = $result;
			}
			else {
				throw new Exception('subscriptions/unknownSubscription');
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
		if (!$this->getApplication_id() || !$this->getPerson_id()) {
			throw new Exception('missingRequriedFields');
		}
	}

	public function save()
	{
		parent::save();
	}

	public function delete()
	{
		if ($this->getId()) {
			$zend_db = Database::getConnection();
			$zend_db->delete('notifications', 'subscription_id='.$this->getId());

			parent::delete();
		}
	}

	//----------------------------------------------------------------
	// Generic Getters
	//----------------------------------------------------------------
	public function getId()             { return parent::get('id');             }
	public function getWaitTime()       { return parent::get('waitTime');       }
	public function getApplication_id() { return parent::get('application_id'); }
	public function getPerson_id()      { return parent::get('person_id');      }
	public function getApplication()    { return parent::getForeignKeyObject('Application', 'application_id'); }
	public function getPerson()         { return parent::getForeignKeyObject('Person',      'person_id');      }

	public function setWaitTime($s) { parent::set('waitTime', (int)$s); }
	public function setApplication_id($s) { parent::setForeignKeyField('Application', 'application_id', $s); }
	public function setPerson_id     ($s) { parent:setForeignKeyField ('Person',      'person_id',      $s); }
	public function setApplication($o)   { parent::setForeignKeyObject('Application', 'application_id', $o); }
	public function setPerson     ($o)   { parent::setForeignKeyObject('Person',      'person_id',      $o); }

	public function handleUpdate($post)
	{
		if (isset($post['waitTime']))       { $this->setWaitTime      ($post['waitTime']);       }
		if (isset($post['application_id'])) { $this->setApplication_id($post['application_id']); }
		if (!$this->getPerson_id())         { $this->setPerson($_SESSION['USER']); }
	}

	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	/**
	 * Determines whether we should send out a notification to this person
	 *
	 * People should only receive an email every ($waitTime) seconds for
	 * any given script error.  We don't want to flood their inboxes
	 *
	 * @param string $script
	 * @return boolean
	 */
	public function wantsNotification($script)
	{
		$zend_db = Database::getConnection();
		$sql = "select max(unix_timestamp(timestamp)) as timestamp
				from notifications where subscription_id=? and script=?";
		$result = $zend_db->fetchOne($sql, array($this->getId(), $script));
		if ($result) {
			if ($result + $this->getWaitTime('U') <= time()) {
				return true;
			}
			return false;
		}
		return true;
	}

	/**
	 * Sends out a notification to the person
	 *
	 * @param string $script
	 * @param string $message
	 */
	public function notify($script, $message)
	{
		$subject = "{$this->getApplication()->getName()} error";
		mail($this->getPerson()->getEmail(),
			 $subject,
			 $message,
			 "From: skidder@$_SERVER[SERVER_NAME]");

		$sql = "insert notifications values(?,?,now()) on duplicate key update timestamp=now()";
		$zend_db = Database::getConnection();
		$zend_db->query($sql, array($this->getId(), $script));
	}

	/**
	 * Subscriptions should only be editable by the person or an admin
	 *
	 * Administrators should be able to edit anyone's subscription
	 * Any user should be able to create new subscriptions
	 * Users should only be able to edit their own subscriptions
	 *
	 * @return boolean
	 */
	public function permitsEditingBy(Person $user)
	{
		return $user->getRole() == 'Administrator'
				|| !$this->getPerson_id()
				|| $this->getPerson_id()==$user->getPerson_id();
	}
}
