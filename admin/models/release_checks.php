<?php
/**
 * @package    Joomla.CMS
 * @subpackage com_release_checking
 *
 * @copyright  (C) 2020 Open Source Matters, Inc. <http://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;

/**
 * Release_checks Model
 */
class Release_checkingModelRelease_checks extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
        {
			$config['filter_fields'] = array(
				'a.id','id',
				'a.published','published',
				'a.access','access',
				'a.ordering','ordering',
				'a.created_by','created_by',
				'a.modified_by','modified_by',
				'g.name','context',
				'h.name','action',
				'a.outcome','outcome',
				'i.name','joomla_version'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}

		// Check if the form was submitted
		$formSubmited = $app->input->post->get('form_submited');

		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', 0, 'int');
		if ($formSubmited)
		{
			$access = $app->input->post->get('access');
			$this->setState('filter.access', $access);
		}

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$created = $this->getUserStateFromRequest($this->context . '.filter.created', 'filter_created');
		$this->setState('filter.created', $created);

		$sorting = $this->getUserStateFromRequest($this->context . '.filter.sorting', 'filter_sorting', 0, 'int');
		$this->setState('filter.sorting', $sorting);

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$context = $this->getUserStateFromRequest($this->context . '.filter.context', 'filter_context');
		if ($formSubmited)
		{
			$context = $app->input->post->get('context');
			$this->setState('filter.context', $context);
		}

		$action = $this->getUserStateFromRequest($this->context . '.filter.action', 'filter_action');
		if ($formSubmited)
		{
			$action = $app->input->post->get('action');
			$this->setState('filter.action', $action);
		}

		$outcome = $this->getUserStateFromRequest($this->context . '.filter.outcome', 'filter_outcome');
		if ($formSubmited)
		{
			$outcome = $app->input->post->get('outcome');
			$this->setState('filter.outcome', $outcome);
		}

		$joomla_version = $this->getUserStateFromRequest($this->context . '.filter.joomla_version', 'filter_joomla_version');
		if ($formSubmited)
		{
			$joomla_version = $app->input->post->get('joomla_version');
			$this->setState('filter.joomla_version', $joomla_version);
		}

		$created_by = $this->getUserStateFromRequest($this->context . '.filter.created_by', 'filter_created_by');
		if ($formSubmited)
		{
			$created_by = $app->input->post->get('created_by');
			$this->setState('filter.created_by', $created_by);
		}

		// List state information.
		parent::populateState($ordering, $direction);
	}
	
	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		// check in items
		$this->checkInNow();

		// load parent items
		$items = parent::getItems();

		// Set values to display correctly.
		if (Release_checkingHelper::checkArray($items))
		{
			// Get the user object if not set.
			if (!isset($user) || !Release_checkingHelper::checkObject($user))
			{
				$user = JFactory::getUser();
			}
			foreach ($items as $nr => &$item)
			{
				if (!isset($_export) || !$_export)
				{
					$item->outcome_style = ($item->outcome == 1) ? "outcome-success" : (($item->outcome == -1) ? "outcome-failure" : "outcome-undecided");
				}
			}
		}

		// set selection value to a translatable value
		if (Release_checkingHelper::checkArray($items))
		{
			foreach ($items as $nr => &$item)
			{
				// convert outcome
				$item->outcome = $this->selectionTranslation($item->outcome, 'outcome');
			}
		}

        
		// return items
		return $items;
	}

	/**
	 * Method to convert selection values to translatable string.
	 *
	 * @return translatable string
	 */
	public function selectionTranslation($value,$name)
	{
		// Array of outcome language strings
		if ($name === 'outcome')
		{
			$outcomeArray = array(
				2 => 'COM_RELEASE_CHECKING_RELEASE_CHECK_UNDECIDED',
				-1 => 'COM_RELEASE_CHECKING_RELEASE_CHECK_FAILURE',
				1 => 'COM_RELEASE_CHECKING_RELEASE_CHECK_SUCCESSFUL'
			);
			// Now check if value is found in this array
			if (isset($outcomeArray[$value]) && Release_checkingHelper::checkString($outcomeArray[$value]))
			{
				return $outcomeArray[$value];
			}
		}
		return $value;
	}
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// Get the user object.
		$user = JFactory::getUser();
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('a.*');

		// From the release_checking_item table
		$query->from($db->quoteName('#__release_checking_release_check', 'a'));

		// From the release_checking_context table.
		$query->select($db->quoteName('g.name','context_name'));
		$query->join('LEFT', $db->quoteName('#__release_checking_context', 'g') . ' ON (' . $db->quoteName('a.context') . ' = ' . $db->quoteName('g.id') . ')');

		// From the release_checking_action table.
		$query->select($db->quoteName('h.name','action_name'));
		$query->join('LEFT', $db->quoteName('#__release_checking_action', 'h') . ' ON (' . $db->quoteName('a.action') . ' = ' . $db->quoteName('h.id') . ')');

		// From the release_checking_joomla_version table.
		$query->select($db->quoteName('i.name','joomla_version_name'));
		$query->join('LEFT', $db->quoteName('#__release_checking_joomla_version', 'i') . ' ON (' . $db->quoteName('a.joomla_version') . ' = ' . $db->quoteName('i.id') . ')');

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.published = 0 OR a.published = 1)');
		}

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');
		// Filter by access level.
		$_access = $this->getState('filter.access');
		if ($_access && is_numeric($_access))
		{
			$query->where('a.access = ' . (int) $_access);
		}
		elseif (Release_checkingHelper::checkArray($_access))
		{
			// Secure the array for the query
			$_access = ArrayHelper::toInteger($_access);
			// Filter by the Access Array.
			$query->where('a.access IN (' . implode(',', $_access) . ')');
		}
		// Implement View Level Access
		if (!$user->authorise('core.options', 'com_release_checking'))
		{
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN (' . $groups . ')');
		}
		// Filter by search.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search) . '%');
				$query->where('(a.context LIKE '.$search.' OR g.name LIKE '.$search.' OR a.action LIKE '.$search.' OR h.name LIKE '.$search.' OR a.outcome LIKE '.$search.' OR a.joomla_version LIKE '.$search.' OR i.name LIKE '.$search.' OR a.created_by LIKE '.$search.')');
			}
		}

		// Filter by Context.
		$_context = $this->getState('filter.context');
		if (is_numeric($_context))
		{
			if (is_float($_context))
			{
				$query->where('a.context = ' . (float) $_context);
			}
			else
			{
				$query->where('a.context = ' . (int) $_context);
			}
		}
		elseif (Release_checkingHelper::checkString($_context))
		{
			$query->where('a.context = ' . $db->quote($db->escape($_context)));
		}
		elseif (Release_checkingHelper::checkArray($_context))
		{
			// Secure the array for the query
			$_context = array_map( function ($val) use(&$db) {
				if (is_numeric($val))
				{
					if (is_float($val))
					{
						return (float) $val;
					}
					else
					{
						return (int) $val;
					}
				}
				elseif (Release_checkingHelper::checkString($val))
				{
					return $db->quote($db->escape($val));
				}
			}, $_context);
			// Filter by the Context Array.
			$query->where('a.context IN (' . implode(',', $_context) . ')');
		}
		// Filter by Action.
		$_action = $this->getState('filter.action');
		if (is_numeric($_action))
		{
			if (is_float($_action))
			{
				$query->where('a.action = ' . (float) $_action);
			}
			else
			{
				$query->where('a.action = ' . (int) $_action);
			}
		}
		elseif (Release_checkingHelper::checkString($_action))
		{
			$query->where('a.action = ' . $db->quote($db->escape($_action)));
		}
		elseif (Release_checkingHelper::checkArray($_action))
		{
			// Secure the array for the query
			$_action = array_map( function ($val) use(&$db) {
				if (is_numeric($val))
				{
					if (is_float($val))
					{
						return (float) $val;
					}
					else
					{
						return (int) $val;
					}
				}
				elseif (Release_checkingHelper::checkString($val))
				{
					return $db->quote($db->escape($val));
				}
			}, $_action);
			// Filter by the Action Array.
			$query->where('a.action IN (' . implode(',', $_action) . ')');
		}
		// Filter by Outcome.
		$_outcome = $this->getState('filter.outcome');
		if (is_numeric($_outcome))
		{
			if (is_float($_outcome))
			{
				$query->where('a.outcome = ' . (float) $_outcome);
			}
			else
			{
				$query->where('a.outcome = ' . (int) $_outcome);
			}
		}
		elseif (Release_checkingHelper::checkString($_outcome))
		{
			$query->where('a.outcome = ' . $db->quote($db->escape($_outcome)));
		}
		elseif (Release_checkingHelper::checkArray($_outcome))
		{
			// Secure the array for the query
			$_outcome = array_map( function ($val) use(&$db) {
				if (is_numeric($val))
				{
					if (is_float($val))
					{
						return (float) $val;
					}
					else
					{
						return (int) $val;
					}
				}
				elseif (Release_checkingHelper::checkString($val))
				{
					return $db->quote($db->escape($val));
				}
			}, $_outcome);
			// Filter by the Outcome Array.
			$query->where('a.outcome IN (' . implode(',', $_outcome) . ')');
		}
		// Filter by Joomla_version.
		$_joomla_version = $this->getState('filter.joomla_version');
		if (is_numeric($_joomla_version))
		{
			if (is_float($_joomla_version))
			{
				$query->where('a.joomla_version = ' . (float) $_joomla_version);
			}
			else
			{
				$query->where('a.joomla_version = ' . (int) $_joomla_version);
			}
		}
		elseif (Release_checkingHelper::checkString($_joomla_version))
		{
			$query->where('a.joomla_version = ' . $db->quote($db->escape($_joomla_version)));
		}
		elseif (Release_checkingHelper::checkArray($_joomla_version))
		{
			// Secure the array for the query
			$_joomla_version = array_map( function ($val) use(&$db) {
				if (is_numeric($val))
				{
					if (is_float($val))
					{
						return (float) $val;
					}
					else
					{
						return (int) $val;
					}
				}
				elseif (Release_checkingHelper::checkString($val))
				{
					return $db->quote($db->escape($val));
				}
			}, $_joomla_version);
			// Filter by the Joomla_version Array.
			$query->where('a.joomla_version IN (' . implode(',', $_joomla_version) . ')');
		}
		// Filter by Created_by.
		$_created_by = $this->getState('filter.created_by');
		if (is_numeric($_created_by))
		{
			if (is_float($_created_by))
			{
				$query->where('a.created_by = ' . (float) $_created_by);
			}
			else
			{
				$query->where('a.created_by = ' . (int) $_created_by);
			}
		}
		elseif (Release_checkingHelper::checkString($_created_by))
		{
			$query->where('a.created_by = ' . $db->quote($db->escape($_created_by)));
		}
		elseif (Release_checkingHelper::checkArray($_created_by))
		{
			// Secure the array for the query
			$_created_by = array_map( function ($val) use(&$db) {
				if (is_numeric($val))
				{
					if (is_float($val))
					{
						return (float) $val;
					}
					else
					{
						return (int) $val;
					}
				}
				elseif (Release_checkingHelper::checkString($val))
				{
					return $db->quote($db->escape($val));
				}
			}, $_created_by);
			// Filter by the Created_by Array.
			$query->where('a.created_by IN (' . implode(',', $_created_by) . ')');
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.id');
		$orderDirn = $this->state->get('list.direction', 'desc');
		if ($orderCol != '')
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Method to get list export data.
	 *
	 * @param   array  $pks  The ids of the items to get
	 * @param   JUser  $user  The user making the request
	 *
	 * @return mixed  An array of data items on success, false on failure.
	 */
	public function getExportData($pks, $user = null)
	{
		// setup the query
		if (($pks_size = Release_checkingHelper::checkArray($pks)) !== false || 'bulk' === $pks)
		{
			// Set a value to know this is export method. (USE IN CUSTOM CODE TO ALTER OUTCOME)
			$_export = true;
			// Get the user object if not set.
			if (!isset($user) || !Release_checkingHelper::checkObject($user))
			{
				$user = JFactory::getUser();
			}
			// Create a new query object.
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);

			// Select some fields
			$query->select('a.*');

			// From the release_checking_release_check table
			$query->from($db->quoteName('#__release_checking_release_check', 'a'));
			// The bulk export path
			if ('bulk' === $pks)
			{
				$query->where('a.id > 0');
			}
			// A large array of ID's will not work out well
			elseif ($pks_size > 500)
			{
				// Use lowest ID
				$query->where('a.id >= ' . (int) min($pks));
				// Use highest ID
				$query->where('a.id <= ' . (int) max($pks));
			}
			// The normal default path
			else
			{
				$query->where('a.id IN (' . implode(',',$pks) . ')');
			}
			// Get global switch to activate text only export
			$export_text_only = JComponentHelper::getParams('com_release_checking')->get('export_text_only', 0);
			// Add these queries only if text only is required
			if ($export_text_only)
			{

				// From the release_checking_context table.
				$query->select($db->quoteName('g.name','context'));
				$query->join('LEFT', $db->quoteName('#__release_checking_context', 'g') . ' ON (' . $db->quoteName('a.context') . ' = ' . $db->quoteName('g.id') . ')');

				// From the release_checking_action table.
				$query->select($db->quoteName('h.name','action'));
				$query->join('LEFT', $db->quoteName('#__release_checking_action', 'h') . ' ON (' . $db->quoteName('a.action') . ' = ' . $db->quoteName('h.id') . ')');

				// From the release_checking_joomla_version table.
				$query->select($db->quoteName('i.name','joomla_version'));
				$query->join('LEFT', $db->quoteName('#__release_checking_joomla_version', 'i') . ' ON (' . $db->quoteName('a.joomla_version') . ' = ' . $db->quoteName('i.id') . ')');
			}
			// Implement View Level Access
			if (!$user->authorise('core.options', 'com_release_checking'))
			{
				$groups = implode(',', $user->getAuthorisedViewLevels());
				$query->where('a.access IN (' . $groups . ')');
			}

			// Order the results by ordering
			$query->order('a.id desc');

			// Load the items
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows())
			{
				$items = $db->loadObjectList();

				// Set values to display correctly.
				if (Release_checkingHelper::checkArray($items))
				{
					foreach ($items as $nr => &$item)
					{
						if (!isset($_export) || !$_export)
						{
							$item->outcome_style = ($item->outcome == 1) ? "outcome-success" : (($item->outcome == -1) ? "outcome-failure" : "outcome-undecided");
						}
						// unset the values we don't want exported.
						unset($item->asset_id);
						unset($item->checked_out);
						unset($item->checked_out_time);
					}
				}
				// Add headers to items array.
				$headers = $this->getExImPortHeaders();
				if (Release_checkingHelper::checkObject($headers))
				{
					array_unshift($items,$headers);
				}
			// Add these translation only if text only is required
			if ($export_text_only)
			{

					// set selection value to a translatable value
					if (Release_checkingHelper::checkArray($items))
					{
						foreach ($items as $nr => &$item)
						{
							// convert outcome
							$item->outcome = $this->selectionTranslation($item->outcome, 'outcome');
						}
					}

			}
				return $items;
			}
		}
		return false;
	}

	/**
	* Method to get header.
	*
	* @return mixed  An array of data items on success, false on failure.
	*/
	public function getExImPortHeaders()
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		// get the columns
		$columns = $db->getTableColumns("#__release_checking_release_check");
		if (Release_checkingHelper::checkArray($columns))
		{
			// remove the headers you don't import/export.
			unset($columns['asset_id']);
			unset($columns['checked_out']);
			unset($columns['checked_out_time']);
			$headers = new stdClass();
			foreach ($columns as $column => $type)
			{
				$headers->{$column} = $column;
			}
			return $headers;
		}
		return false;
	}
	
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * @return  string  A store id.
	 *
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.id');
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		// Check if the value is an array
		$_access = $this->getState('filter.access');
		if (Release_checkingHelper::checkArray($_access))
		{
			$id .= ':' . implode(':', $_access);
		}
		// Check if this is only an int or string
		elseif (is_numeric($_access)
		 || Release_checkingHelper::checkString($_access))
		{
			$id .= ':' . $_access;
		}
		$id .= ':' . $this->getState('filter.ordering');
		$id .= ':' . $this->getState('filter.modified_by');
		// Check if the value is an array
		$_context = $this->getState('filter.context');
		if (Release_checkingHelper::checkArray($_context))
		{
			$id .= ':' . implode(':', $_context);
		}
		// Check if this is only an int or string
		elseif (is_numeric($_context)
		 || Release_checkingHelper::checkString($_context))
		{
			$id .= ':' . $_context;
		}
		// Check if the value is an array
		$_action = $this->getState('filter.action');
		if (Release_checkingHelper::checkArray($_action))
		{
			$id .= ':' . implode(':', $_action);
		}
		// Check if this is only an int or string
		elseif (is_numeric($_action)
		 || Release_checkingHelper::checkString($_action))
		{
			$id .= ':' . $_action;
		}
		// Check if the value is an array
		$_outcome = $this->getState('filter.outcome');
		if (Release_checkingHelper::checkArray($_outcome))
		{
			$id .= ':' . implode(':', $_outcome);
		}
		// Check if this is only an int or string
		elseif (is_numeric($_outcome)
		 || Release_checkingHelper::checkString($_outcome))
		{
			$id .= ':' . $_outcome;
		}
		// Check if the value is an array
		$_joomla_version = $this->getState('filter.joomla_version');
		if (Release_checkingHelper::checkArray($_joomla_version))
		{
			$id .= ':' . implode(':', $_joomla_version);
		}
		// Check if this is only an int or string
		elseif (is_numeric($_joomla_version)
		 || Release_checkingHelper::checkString($_joomla_version))
		{
			$id .= ':' . $_joomla_version;
		}

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to checkin all items left checked out longer then a set time.
	 *
	 * @return  a bool
	 *
	 */
	protected function checkInNow()
	{
		// Get set check in time
		$time = JComponentHelper::getParams('com_release_checking')->get('check_in');

		if ($time)
		{

			// Get a db connection.
			$db = JFactory::getDbo();
			// reset query
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->quoteName('#__release_checking_release_check'));
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows())
			{
				// Get Yesterdays date
				$date = JFactory::getDate()->modify($time)->toSql();
				// reset query
				$query = $db->getQuery(true);

				// Fields to update.
				$fields = array(
					$db->quoteName('checked_out_time') . '=\'0000-00-00 00:00:00\'',
					$db->quoteName('checked_out') . '=0'
				);

				// Conditions for which records should be updated.
				$conditions = array(
					$db->quoteName('checked_out') . '!=0', 
					$db->quoteName('checked_out_time') . '<\''.$date.'\''
				);

				// Check table
				$query->update($db->quoteName('#__release_checking_release_check'))->set($fields)->where($conditions); 

				$db->setQuery($query);

				$db->execute();
			}
		}

		return false;
	}
}
