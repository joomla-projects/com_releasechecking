<?php
/**
 * @package    Joomla.CMS
 * @maintainer Llewellyn van der Merwe <https://git.vdm.dev/Llewellyn>
 *
 * @created    29th July, 2020
 * @copyright  (C) 2020 Open Source Matters, Inc. <http://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace VDM\Component\Releasechecking\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\User\User;
use Joomla\Utilities\ArrayHelper;
use Joomla\Input\Input;
use Joomla\CMS\Helper\TagsHelper;
use VDM\Component\Releasechecking\Administrator\Helper\ReleasecheckingHelper;
use VDM\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;
use VDM\Joomla\Utilities\ObjectHelper;
use VDM\Joomla\Utilities\StringHelper;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * Release_checks List Model
 *
 * @since  1.6
 */
class Release_checksModel extends ListModel
{
	/**
	 * The application object.
	 *
	 * @var   CMSApplicationInterface  The application instance.
	 * @since 3.2.0
	 */
	protected CMSApplicationInterface $app;

	/**
	 * Constructor
	 *
	 * @param   array                 $config   An array of configuration options (name, state, dbo, table_path, ignore_request).
	 * @param   ?MVCFactoryInterface  $factory  The factory.
	 *
	 * @since   1.6
	 * @throws  \Exception
	 */
	public function __construct($config = [], MVCFactoryInterface $factory = null)
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

		parent::__construct($config, $factory);

		$this->app ??= Factory::getApplication();
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
	 * @since   1.7.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = $this->app;

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
	 * @since   1.6
	 */
	public function getItems()
	{
		// Check in items
		$this->checkInNow();

		// load parent items
		$items = parent::getItems();

		// Set values to display correctly.
		if (UtilitiesArrayHelper::check($items))
		{
			// Get the user object if not set.
			if (!isset($user) || !ObjectHelper::check($user))
			{
				$user = $this->getCurrentUser();
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
		if (UtilitiesArrayHelper::check($items))
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
	 * @return  string   The translatable string.
	 */
	public function selectionTranslation($value,$name)
	{
		// Array of outcome language strings
		if ($name === 'outcome')
		{
			$outcomeArray = array(
				2 => 'COM_RELEASECHECKING_RELEASE_CHECK_UNDECIDED',
				-1 => 'COM_RELEASECHECKING_RELEASE_CHECK_FAILURE',
				1 => 'COM_RELEASECHECKING_RELEASE_CHECK_SUCCESSFUL'
			);
			// Now check if value is found in this array
			if (isset($outcomeArray[$value]) && StringHelper::check($outcomeArray[$value]))
			{
				return $outcomeArray[$value];
			}
		}
		return $value;
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string    An SQL query
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Get the user object.
		$user = $this->getCurrentUser();
		// Create a new query object.
		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('a.*');

		// From the releasechecking_item table
		$query->from($db->quoteName('#__releasechecking_release_check', 'a'));

		// From the releasechecking_context table.
		$query->select($db->quoteName('g.name','context_name'));
		$query->join('LEFT', $db->quoteName('#__releasechecking_context', 'g') . ' ON (' . $db->quoteName('a.context') . ' = ' . $db->quoteName('g.id') . ')');

		// From the releasechecking_action table.
		$query->select($db->quoteName('h.name','action_name'));
		$query->join('LEFT', $db->quoteName('#__releasechecking_action', 'h') . ' ON (' . $db->quoteName('a.action') . ' = ' . $db->quoteName('h.id') . ')');

		// From the releasechecking_joomla_version table.
		$query->select($db->quoteName('i.name','joomla_version_name'));
		$query->join('LEFT', $db->quoteName('#__releasechecking_joomla_version', 'i') . ' ON (' . $db->quoteName('a.joomla_version') . ' = ' . $db->quoteName('i.id') . ')');

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
		elseif (UtilitiesArrayHelper::check($_access))
		{
			// Secure the array for the query
			$_access = ArrayHelper::toInteger($_access);
			// Filter by the Access Array.
			$query->where('a.access IN (' . implode(',', $_access) . ')');
		}
		// Implement View Level Access
		if (!$user->authorise('core.options', 'com_releasechecking'))
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
		elseif (StringHelper::check($_context))
		{
			$query->where('a.context = ' . $db->quote($db->escape($_context)));
		}
		elseif (UtilitiesArrayHelper::check($_context))
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
				elseif (StringHelper::check($val))
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
		elseif (StringHelper::check($_action))
		{
			$query->where('a.action = ' . $db->quote($db->escape($_action)));
		}
		elseif (UtilitiesArrayHelper::check($_action))
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
				elseif (StringHelper::check($val))
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
		elseif (StringHelper::check($_outcome))
		{
			$query->where('a.outcome = ' . $db->quote($db->escape($_outcome)));
		}
		elseif (UtilitiesArrayHelper::check($_outcome))
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
				elseif (StringHelper::check($val))
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
		elseif (StringHelper::check($_joomla_version))
		{
			$query->where('a.joomla_version = ' . $db->quote($db->escape($_joomla_version)));
		}
		elseif (UtilitiesArrayHelper::check($_joomla_version))
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
				elseif (StringHelper::check($val))
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
		elseif (StringHelper::check($_created_by))
		{
			$query->where('a.created_by = ' . $db->quote($db->escape($_created_by)));
		}
		elseif (UtilitiesArrayHelper::check($_created_by))
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
				elseif (StringHelper::check($val))
				{
					return $db->quote($db->escape($val));
				}
			}, $_created_by);
			// Filter by the Created_by Array.
			$query->where('a.created_by IN (' . implode(',', $_created_by) . ')');
		}

		// Add the list ordering clause.
		$orderCol = $this->getState('list.ordering', 'a.id');
		$orderDirn = $this->getState('list.direction', 'desc');
		if ($orderCol != '')
		{
			// Check that the order direction is valid encase we have a field called direction as part of filers.
			$orderDirn = (is_string($orderDirn) && in_array(strtolower($orderDirn), ['asc', 'desc'])) ? $orderDirn : 'desc';
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
		if (($pks_size = UtilitiesArrayHelper::check($pks)) !== false || 'bulk' === $pks)
		{
			// Set a value to know this is export method. (USE IN CUSTOM CODE TO ALTER OUTCOME)
			$_export = true;
			// Get the user object if not set.
			if (!isset($user) || !ObjectHelper::check($user))
			{
				$user = $this->getCurrentUser();
			}
			// Create a new query object.
			$db = $this->getDatabase();
			$query = $db->getQuery(true);

			// Select some fields
			$query->select('a.*');

			// From the releasechecking_release_check table
			$query->from($db->quoteName('#__releasechecking_release_check', 'a'));
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
			$export_text_only = ComponentHelper::getParams('com_releasechecking')->get('export_text_only', 0);
			// Add these queries only if text only is required
			if ($export_text_only)
			{

				// From the releasechecking_context table.
				$query->select($db->quoteName('g.name','context'));
				$query->join('LEFT', $db->quoteName('#__releasechecking_context', 'g') . ' ON (' . $db->quoteName('a.context') . ' = ' . $db->quoteName('g.id') . ')');

				// From the releasechecking_action table.
				$query->select($db->quoteName('h.name','action'));
				$query->join('LEFT', $db->quoteName('#__releasechecking_action', 'h') . ' ON (' . $db->quoteName('a.action') . ' = ' . $db->quoteName('h.id') . ')');

				// From the releasechecking_joomla_version table.
				$query->select($db->quoteName('i.name','joomla_version'));
				$query->join('LEFT', $db->quoteName('#__releasechecking_joomla_version', 'i') . ' ON (' . $db->quoteName('a.joomla_version') . ' = ' . $db->quoteName('i.id') . ')');
			}
			// Implement View Level Access
			if (!$user->authorise('core.options', 'com_releasechecking'))
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
				if (UtilitiesArrayHelper::check($items))
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
				if (ObjectHelper::check($headers))
				{
					array_unshift($items,$headers);
				}
			// Add these translation only if text only is required
			if ($export_text_only)
			{

					// set selection value to a translatable value
					if (UtilitiesArrayHelper::check($items))
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
		$db = Factory::getDbo();
		// get the columns
		$columns = $db->getTableColumns("#__releasechecking_release_check");
		if (UtilitiesArrayHelper::check($columns))
		{
			// remove the headers you don't import/export.
			unset($columns['asset_id']);
			unset($columns['checked_out']);
			unset($columns['checked_out_time']);
			$headers = new \stdClass();
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
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.id');
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		// Check if the value is an array
		$_access = $this->getState('filter.access');
		if (UtilitiesArrayHelper::check($_access))
		{
			$id .= ':' . implode(':', $_access);
		}
		// Check if this is only an number or string
		elseif (is_numeric($_access)
		 || StringHelper::check($_access))
		{
			$id .= ':' . $_access;
		}
		$id .= ':' . $this->getState('filter.ordering');
		$id .= ':' . $this->getState('filter.modified_by');
		// Check if the value is an array
		$_context = $this->getState('filter.context');
		if (UtilitiesArrayHelper::check($_context))
		{
			$id .= ':' . implode(':', $_context);
		}
		// Check if this is only an number or string
		elseif (is_numeric($_context)
		 || StringHelper::check($_context))
		{
			$id .= ':' . $_context;
		}
		// Check if the value is an array
		$_action = $this->getState('filter.action');
		if (UtilitiesArrayHelper::check($_action))
		{
			$id .= ':' . implode(':', $_action);
		}
		// Check if this is only an number or string
		elseif (is_numeric($_action)
		 || StringHelper::check($_action))
		{
			$id .= ':' . $_action;
		}
		// Check if the value is an array
		$_outcome = $this->getState('filter.outcome');
		if (UtilitiesArrayHelper::check($_outcome))
		{
			$id .= ':' . implode(':', $_outcome);
		}
		// Check if this is only an number or string
		elseif (is_numeric($_outcome)
		 || StringHelper::check($_outcome))
		{
			$id .= ':' . $_outcome;
		}
		// Check if the value is an array
		$_joomla_version = $this->getState('filter.joomla_version');
		if (UtilitiesArrayHelper::check($_joomla_version))
		{
			$id .= ':' . implode(':', $_joomla_version);
		}
		// Check if this is only an number or string
		elseif (is_numeric($_joomla_version)
		 || StringHelper::check($_joomla_version))
		{
			$id .= ':' . $_joomla_version;
		}

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to checkin all items left checked out longer then a set time.
	 *
	 * @return bool
	 * @since 3.2.0
	 */
	protected function checkInNow(): bool
	{
		// Get set check in time
		$time = ComponentHelper::getParams('com_releasechecking')->get('check_in');

		if ($time)
		{
			// Get a db connection.
			$db = $this->getDatabase();
			// Reset query.
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->quoteName('#__releasechecking_release_check'));
			// Only select items that are checked out.
			$query->where($db->quoteName('checked_out') . '!=0');
			$db->setQuery($query, 0, 1);
			$db->execute();
			if ($db->getNumRows())
			{
				// Get Yesterdays date.
				$date = Factory::getDate()->modify($time)->toSql();
				// Reset query.
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

				// Check table.
				$query->update($db->quoteName('#__releasechecking_release_check'))->set($fields)->where($conditions); 

				$db->setQuery($query);

				return $db->execute();
			}
		}

		return false;
	}
}
