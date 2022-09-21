<?php
/**
 * @package    Joomla.CMS
 * @maintainer Llewellyn van der Merwe <https://git.vdm.dev/Llewellyn>
 *
 * @created    29th July, 2020
 * @copyright  (C) 2020 Open Source Matters, Inc. <http://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Utilities\ArrayHelper;

/**
 * Release_checking Ajax List Model
 */
class Release_checkingModelAjax extends ListModel
{
	protected $app_params;
	
	public function __construct() 
	{		
		parent::__construct();		
		// get params
		$this->app_params	= JComponentHelper::getParams('com_release_checking');
		
	}

	// Used in release_check
	public function getAction($context, $joomlaVersion, $currentId)
	{
		// return array
		$result = array('removed_ids' => array(), 'ids' => array());
		// Get a db connection.
		$db = JFactory::getDbo();
		// Get the user object.
		$user_id = JFactory::getUser()->get('id', 0);
		// we first get what is already set
		$query = $db->getQuery(true);
		$query->select($db->quoteName( array('r.action') ));
		$query->from($db->quoteName('#__release_checking_release_check', 'r'));
		// we also filter out the actions this user already did on this context
		$query->where($db->quoteName('r.created_by') . ' = ' . (int) $user_id);
		$query->where($db->quoteName('r.context') . ' = ' . (int) $context);
		$query->where($db->quoteName('r.joomla_version') . ' = ' . (int) $joomlaVersion);
		if ($currentId > 0)
		{
			$query->where($db->quoteName('r.id') . ' != ' . (int) $currentId);
		}
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows())
		{
			$result['removed_ids'] = $db->loadColumn();
		}
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select($db->quoteName( array('a.id') ));
		$query->from($db->quoteName('#__release_checking_action', 'a'));
		// we also filter out the actions this user already did on this context
		if (Release_checkingHelper::checkArray($result['removed_ids']))
		{
			$query->where($db->quoteName('a.id') . ' NOT IN (' . implode(', ', $result['removed_ids']) . ')');
		}
		// check for context and action
		$query->where($db->quoteName('a.context') . ' = '. (int) $context);
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows())
		{
			$result['ids'] = $db->loadColumn();
		}
		return $result;
	}

	public function getActionDescription($action)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select($db->quoteName( array('a.description') ));
		$query->from($db->quoteName('#__release_checking_action', 'a'));
		// check for context and action
		$query->where($db->quoteName('a.id') . ' = ' . (int) $action);
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows())
		{
			return $db->loadResult();
		}
		return false;
	}

}
