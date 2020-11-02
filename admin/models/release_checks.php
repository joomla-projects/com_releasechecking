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
				'a.ordering','ordering',
				'a.created_by','created_by',
				'a.modified_by','modified_by',
				'g.name',
				'h.name',
				'a.outcome','outcome',
				'i.name'
			);
		}

		parent::__construct($config);
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}
		$context = $this->getUserStateFromRequest($this->context . '.filter.context', 'filter_context');
		$this->setState('filter.context', $context);

		$action = $this->getUserStateFromRequest($this->context . '.filter.action', 'filter_action');
		$this->setState('filter.action', $action);

		$outcome = $this->getUserStateFromRequest($this->context . '.filter.outcome', 'filter_outcome');
		$this->setState('filter.outcome', $outcome);

		$joomla_version = $this->getUserStateFromRequest($this->context . '.filter.joomla_version', 'filter_joomla_version');
		$this->setState('filter.joomla_version', $joomla_version);

		$created_by = $this->getUserStateFromRequest($this->context . '.filter.created_by', 'filter_created_by');
		$this->setState('filter.created_by', $created_by);
        
		$sorting = $this->getUserStateFromRequest($this->context . '.filter.sorting', 'filter_sorting', 0, 'int');
		$this->setState('filter.sorting', $sorting);
        
		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);
        
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);
        
		$created_by = $this->getUserStateFromRequest($this->context . '.filter.created_by', 'filter_created_by', '');
		$this->setState('filter.created_by', $created_by);

		$created = $this->getUserStateFromRequest($this->context . '.filter.created', 'filter_created');
		$this->setState('filter.created', $created);

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
		if ($access = $this->getState('filter.access'))
		{
			$query->where('a.access = ' . (int) $access);
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

		// Filter by context.
		if ($context = $this->getState('filter.context'))
		{
			$query->where('a.context = ' . $db->quote($db->escape($context)));
		}
		// Filter by action.
		if ($action = $this->getState('filter.action'))
		{
			$query->where('a.action = ' . $db->quote($db->escape($action)));
		}
		// Filter by Outcome.
		if ($outcome = $this->getState('filter.outcome'))
		{
			$query->where('a.outcome = ' . $db->quote($db->escape($outcome)));
		}
		// Filter by joomla_version.
		if ($joomla_version = $this->getState('filter.joomla_version'))
		{
			$query->where('a.joomla_version = ' . $db->quote($db->escape($joomla_version)));
		}
		// Filter by Created_by.
		if ($created_by = $this->getState('filter.created_by'))
		{
			$query->where('a.created_by = ' . $db->quote($db->escape($created_by)));
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
		$id .= ':' . $this->getState('filter.ordering');
		$id .= ':' . $this->getState('filter.created_by');
		$id .= ':' . $this->getState('filter.modified_by');
		$id .= ':' . $this->getState('filter.context');
		$id .= ':' . $this->getState('filter.action');
		$id .= ':' . $this->getState('filter.outcome');
		$id .= ':' . $this->getState('filter.joomla_version');

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
