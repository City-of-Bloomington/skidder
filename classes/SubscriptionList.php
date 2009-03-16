<?php
/**
 * A collection class for Subscription objects
 *
 * This class creates a select statement, only selecting the ID from each row
 * PDOResultIterator handles iterating and paginating those results.
 * As the results are iterated over, PDOResultIterator will pass each desired
 * ID back to this class's loadResult() which will be responsible for hydrating
 * each Subscription object
 *
 * Beyond the basic $fields handled, you will need to write your own handling
 * of whatever extra $fields you need
 *
 * The PDOResultIterator uses prepared queries; it is recommended to use bound
 * parameters for each of the options you handle
 *
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class SubscriptionList extends PDOResultIterator
{
	/**
	 * Creates a basic select statement for the collection.
	 * Populates the collection if you pass in $fields
	 *
	 * @param array $fields
	 */
	public function __construct($fields=null)
	{
		$this->select = 'select subscriptions.id as id from subscriptions';
		if (is_array($fields)) {
			$this->find($fields);
		}
	}

	/**
	 * Populates the collection from the database based on the $fields you handle
	 *
	 * @param array $fields
	 * @param string $sort
	 * @param int $limit
	 * @param string $groupBy
	 */
	public function find($fields=null,$sort='id',$limit=null,$groupBy=null)
	{
		$this->sort = $sort;
		$this->limit = $limit;
		$this->groupBy = $groupBy;
		$this->joins = '';

		$options = array();
		$parameters = array();

		if (isset($fields['id'])) {
			$options[] = 'id=:id';
			$parameters[':id'] = $fields['id'];
		}

		if (isset($fields['application_id'])) {
			$options[] = 'application_id=:application_id';
			$parameters[':application_id'] = $fields['application_id'];
		}

		if (isset($fields['person_id'])) {
			$options[] = 'person_id=:person_id';
			$parameters[':person_id'] = $fields['person_id'];
		}

		if (isset($fields['waitTime'])) {
			$options[] = 'waitTime=:waitTime';
			$parameters[':waitTime'] = $fields['waitTime'];
		}


		// Finding on fields from other tables required joining those tables.
		// You can add fields from other tables to $options by adding the join SQL
		// to $this->joins here

		$this->populateList($options,$parameters);
	}

	/**
	 * Loads a single Subscription object for the key returned from PDOResultIterator
	 * @param int $key
	 */
	protected function loadResult($key)
	{
		return new Subscription($this->list[$key]);
	}
}
