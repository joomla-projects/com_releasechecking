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
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\User\User;
use Joomla\Utilities\ArrayHelper;
use Joomla\Input\Input;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use VDM\Component\Releasechecking\Administrator\Helper\ReleasecheckingHelper;
use VDM\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * Releasechecking Ajax List Model
 *
 * @since  1.6
 */
class AjaxModel extends ListModel
{
	/**
	 * The component params.
	 *
	 * @var   Registry
	 * @since 3.2.0
	 */
	protected Registry $app_params;

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
		parent::__construct($config, $factory);

		$this->app_params = ComponentHelper::getParams('com_releasechecking');
		$this->app ??= Factory::getApplication();
	}

	// Used in release_check
	public function getAction($context, $joomlaVersion, $currentId)
	{
		// return array
		$result = array('removed_ids' => array(), 'ids' => array());
		// Get a db connection.
		$db = Factory::getDbo();
		// Get the user object.
		$user_id = Factory::getUser()->get('id', 0);
		// we first get what is already set
		$query = $db->getQuery(true);
		$query->select($db->quoteName( array('r.action') ));
		$query->from($db->quoteName('#__releasechecking_release_check', 'r'));
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
		$query->from($db->quoteName('#__releasechecking_action', 'a'));
		// we also filter out the actions this user already did on this context
		if (UtilitiesArrayHelper::check($result['removed_ids']))
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
		$db = Factory::getDbo();
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select($db->quoteName( array('a.description') ));
		$query->from($db->quoteName('#__releasechecking_action', 'a'));
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
