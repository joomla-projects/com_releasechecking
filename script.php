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

JHTML::_('behavior.modal');

/**
 * Script File of Release_checking Component
 */
class com_release_checkingInstallerScript
{
	/**
	 * Constructor
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 */
	public function __construct(JAdapterInstance $parent) {}

	/**
	 * Called on installation
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function install(JAdapterInstance $parent) {}

	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 */
	public function uninstall(JAdapterInstance $parent)
	{
		// Get Application object
		$app = JFactory::getApplication();

		// Get The Database object
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Release_check alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_release_checking.release_check') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$release_check_found = $db->getNumRows();
		// Now check if there were any rows
		if ($release_check_found)
		{
			// Since there are load the needed  release_check type ids
			$release_check_ids = $db->loadColumn();
			// Remove Release_check from the content type table
			$release_check_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_release_checking.release_check') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($release_check_condition);
			$db->setQuery($query);
			// Execute the query to remove Release_check items
			$release_check_done = $db->execute();
			if ($release_check_done)
			{
				// If successfully remove Release_check add queued success message.
				$app->enqueueMessage(JText::_('The (com_release_checking.release_check) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Release_check items from the contentitem tag map table
			$release_check_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_release_checking.release_check') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($release_check_condition);
			$db->setQuery($query);
			// Execute the query to remove Release_check items
			$release_check_done = $db->execute();
			if ($release_check_done)
			{
				// If successfully remove Release_check add queued success message.
				$app->enqueueMessage(JText::_('The (com_release_checking.release_check) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Release_check items from the ucm content table
			$release_check_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_release_checking.release_check') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($release_check_condition);
			$db->setQuery($query);
			// Execute the query to remove Release_check items
			$release_check_done = $db->execute();
			if ($release_check_done)
			{
				// If successfully removed Release_check add queued success message.
				$app->enqueueMessage(JText::_('The (com_release_checking.release_check) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Release_check items are cleared from DB
			foreach ($release_check_ids as $release_check_id)
			{
				// Remove Release_check items from the ucm base table
				$release_check_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $release_check_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($release_check_condition);
				$db->setQuery($query);
				// Execute the query to remove Release_check items
				$db->execute();

				// Remove Release_check items from the ucm history table
				$release_check_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $release_check_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($release_check_condition);
				$db->setQuery($query);
				// Execute the query to remove Release_check items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Joomla_version alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_release_checking.joomla_version') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$joomla_version_found = $db->getNumRows();
		// Now check if there were any rows
		if ($joomla_version_found)
		{
			// Since there are load the needed  joomla_version type ids
			$joomla_version_ids = $db->loadColumn();
			// Remove Joomla_version from the content type table
			$joomla_version_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_release_checking.joomla_version') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($joomla_version_condition);
			$db->setQuery($query);
			// Execute the query to remove Joomla_version items
			$joomla_version_done = $db->execute();
			if ($joomla_version_done)
			{
				// If successfully remove Joomla_version add queued success message.
				$app->enqueueMessage(JText::_('The (com_release_checking.joomla_version) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Joomla_version items from the contentitem tag map table
			$joomla_version_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_release_checking.joomla_version') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($joomla_version_condition);
			$db->setQuery($query);
			// Execute the query to remove Joomla_version items
			$joomla_version_done = $db->execute();
			if ($joomla_version_done)
			{
				// If successfully remove Joomla_version add queued success message.
				$app->enqueueMessage(JText::_('The (com_release_checking.joomla_version) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Joomla_version items from the ucm content table
			$joomla_version_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_release_checking.joomla_version') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($joomla_version_condition);
			$db->setQuery($query);
			// Execute the query to remove Joomla_version items
			$joomla_version_done = $db->execute();
			if ($joomla_version_done)
			{
				// If successfully removed Joomla_version add queued success message.
				$app->enqueueMessage(JText::_('The (com_release_checking.joomla_version) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Joomla_version items are cleared from DB
			foreach ($joomla_version_ids as $joomla_version_id)
			{
				// Remove Joomla_version items from the ucm base table
				$joomla_version_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $joomla_version_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($joomla_version_condition);
				$db->setQuery($query);
				// Execute the query to remove Joomla_version items
				$db->execute();

				// Remove Joomla_version items from the ucm history table
				$joomla_version_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $joomla_version_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($joomla_version_condition);
				$db->setQuery($query);
				// Execute the query to remove Joomla_version items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Context alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_release_checking.context') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$context_found = $db->getNumRows();
		// Now check if there were any rows
		if ($context_found)
		{
			// Since there are load the needed  context type ids
			$context_ids = $db->loadColumn();
			// Remove Context from the content type table
			$context_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_release_checking.context') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($context_condition);
			$db->setQuery($query);
			// Execute the query to remove Context items
			$context_done = $db->execute();
			if ($context_done)
			{
				// If successfully remove Context add queued success message.
				$app->enqueueMessage(JText::_('The (com_release_checking.context) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Context items from the contentitem tag map table
			$context_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_release_checking.context') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($context_condition);
			$db->setQuery($query);
			// Execute the query to remove Context items
			$context_done = $db->execute();
			if ($context_done)
			{
				// If successfully remove Context add queued success message.
				$app->enqueueMessage(JText::_('The (com_release_checking.context) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Context items from the ucm content table
			$context_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_release_checking.context') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($context_condition);
			$db->setQuery($query);
			// Execute the query to remove Context items
			$context_done = $db->execute();
			if ($context_done)
			{
				// If successfully removed Context add queued success message.
				$app->enqueueMessage(JText::_('The (com_release_checking.context) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Context items are cleared from DB
			foreach ($context_ids as $context_id)
			{
				// Remove Context items from the ucm base table
				$context_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $context_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($context_condition);
				$db->setQuery($query);
				// Execute the query to remove Context items
				$db->execute();

				// Remove Context items from the ucm history table
				$context_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $context_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($context_condition);
				$db->setQuery($query);
				// Execute the query to remove Context items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Action alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_release_checking.action') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$action_found = $db->getNumRows();
		// Now check if there were any rows
		if ($action_found)
		{
			// Since there are load the needed  action type ids
			$action_ids = $db->loadColumn();
			// Remove Action from the content type table
			$action_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_release_checking.action') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($action_condition);
			$db->setQuery($query);
			// Execute the query to remove Action items
			$action_done = $db->execute();
			if ($action_done)
			{
				// If successfully remove Action add queued success message.
				$app->enqueueMessage(JText::_('The (com_release_checking.action) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Action items from the contentitem tag map table
			$action_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_release_checking.action') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($action_condition);
			$db->setQuery($query);
			// Execute the query to remove Action items
			$action_done = $db->execute();
			if ($action_done)
			{
				// If successfully remove Action add queued success message.
				$app->enqueueMessage(JText::_('The (com_release_checking.action) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Action items from the ucm content table
			$action_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_release_checking.action') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($action_condition);
			$db->setQuery($query);
			// Execute the query to remove Action items
			$action_done = $db->execute();
			if ($action_done)
			{
				// If successfully removed Action add queued success message.
				$app->enqueueMessage(JText::_('The (com_release_checking.action) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Action items are cleared from DB
			foreach ($action_ids as $action_id)
			{
				// Remove Action items from the ucm base table
				$action_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $action_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($action_condition);
				$db->setQuery($query);
				// Execute the query to remove Action items
				$db->execute();

				// Remove Action items from the ucm history table
				$action_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $action_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($action_condition);
				$db->setQuery($query);
				// Execute the query to remove Action items
				$db->execute();
			}
		}

		// If All related items was removed queued success message.
		$app->enqueueMessage(JText::_('All related items was removed from the <b>#__ucm_base</b> table'));
		$app->enqueueMessage(JText::_('All related items was removed from the <b>#__ucm_history</b> table'));

		// Remove release_checking assets from the assets table
		$release_checking_condition = array( $db->quoteName('name') . ' LIKE ' . $db->quote('com_release_checking%') );

		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__assets'));
		$query->where($release_checking_condition);
		$db->setQuery($query);
		$action_done = $db->execute();
		if ($action_done)
		{
			// If successfully removed release_checking add queued success message.
			$app->enqueueMessage(JText::_('All related items was removed from the <b>#__assets</b> table'));
		}


		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Release_checking from the action_logs_extensions table
		$release_checking_action_logs_extensions = array( $db->quoteName('extension') . ' = ' . $db->quote('com_release_checking') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_logs_extensions'));
		$query->where($release_checking_action_logs_extensions);
		$db->setQuery($query);
		// Execute the query to remove Release_checking
		$release_checking_removed_done = $db->execute();
		if ($release_checking_removed_done)
		{
			// If successfully remove Release_checking add queued success message.
			$app->enqueueMessage(JText::_('The com_release_checking extension was removed from the <b>#__action_logs_extensions</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Release_checking Release_check from the action_log_config table
		$release_check_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_release_checking.release_check') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($release_check_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_release_checking.release_check
		$release_check_action_log_config_done = $db->execute();
		if ($release_check_action_log_config_done)
		{
			// If successfully removed Release_checking Release_check add queued success message.
			$app->enqueueMessage(JText::_('The com_release_checking.release_check type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Release_checking Joomla_version from the action_log_config table
		$joomla_version_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_release_checking.joomla_version') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($joomla_version_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_release_checking.joomla_version
		$joomla_version_action_log_config_done = $db->execute();
		if ($joomla_version_action_log_config_done)
		{
			// If successfully removed Release_checking Joomla_version add queued success message.
			$app->enqueueMessage(JText::_('The com_release_checking.joomla_version type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Release_checking Context from the action_log_config table
		$context_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_release_checking.context') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($context_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_release_checking.context
		$context_action_log_config_done = $db->execute();
		if ($context_action_log_config_done)
		{
			// If successfully removed Release_checking Context add queued success message.
			$app->enqueueMessage(JText::_('The com_release_checking.context type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Release_checking Action from the action_log_config table
		$action_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_release_checking.action') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($action_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_release_checking.action
		$action_action_log_config_done = $db->execute();
		if ($action_action_log_config_done)
		{
			// If successfully removed Release_checking Action add queued success message.
			$app->enqueueMessage(JText::_('The com_release_checking.action type alias was removed from the <b>#__action_log_config</b> table'));
		}
		// little notice as after service, in case of bad experience with component.
		echo '<h2>Did something go wrong? Are you disappointed?</h2>
		<p>Please let me know at <a href="mailto:admin@joomla.org">admin@joomla.org</a>.
		<br />We at Open Source Matters are committed to building extensions that performs proficiently! You can help us, really!
		<br />Send me your thoughts on improvements that is needed, trust me, I will be very grateful!
		<br />Visit us at <a href="http://www.joomla.org" target="_blank">http://www.joomla.org</a> today!</p>';
	}

	/**
	 * Called on update
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function update(JAdapterInstance $parent){}

	/**
	 * Called before any type of action
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($type, JAdapterInstance $parent)
	{
		// get application
		$app = JFactory::getApplication();
		// is redundant or so it seems ...hmmm let me know if it works again
		if ($type === 'uninstall')
		{
			return true;
		}
		// the default for both install and update
		$jversion = new JVersion();
		if (!$jversion->isCompatible('3.8.0'))
		{
			$app->enqueueMessage('Please upgrade to at least Joomla! 3.8.0 before continuing!', 'error');
			return false;
		}
		// do any updates needed
		if ($type === 'update')
		{
		}
		// do any install needed
		if ($type === 'install')
		{
		}
		// check if the PHPExcel stuff is still around
		if (JFile::exists(JPATH_ADMINISTRATOR . '/components/com_release_checking/helpers/PHPExcel.php'))
		{
			// We need to remove this old PHPExcel folder
			$this->removeFolder(JPATH_ADMINISTRATOR . '/components/com_release_checking/helpers/PHPExcel');
			// We need to remove this old PHPExcel file
			JFile::delete(JPATH_ADMINISTRATOR . '/components/com_release_checking/helpers/PHPExcel.php');
		}
		return true;
	}

	/**
	 * Called after any type of action
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($type, JAdapterInstance $parent)
	{
		// get application
		$app = JFactory::getApplication();
		// We check if we have dynamic folders to copy
		$this->setDynamicF0ld3rs($app, $parent);
		// set the default component settings
		if ($type === 'install')
		{

			// Get The Database object
			$db = JFactory::getDbo();

			// Create the release_check content type object.
			$release_check = new stdClass();
			$release_check->type_title = 'Release_checking Release_check';
			$release_check->type_alias = 'com_release_checking.release_check';
			$release_check->table = '{"special": {"dbtable": "#__release_checking_release_check","key": "id","type": "Release_check","prefix": "release_checkingTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$release_check->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "context","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"context":"context","action":"action","outcome":"outcome","joomla_version":"joomla_version"}}';
			$release_check->router = 'Release_checkingHelperRoute::getRelease_checkRoute';
			$release_check->content_history_options = '{"formFile": "administrator/components/com_release_checking/models/forms/release_check.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","context","action","outcome","joomla_version","created_by"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "context","targetTable": "#__release_checking_context","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "action","targetTable": "#__release_checking_action","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "joomla_version","targetTable": "#__release_checking_joomla_version","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$release_check_Inserted = $db->insertObject('#__content_types', $release_check);

			// Create the joomla_version content type object.
			$joomla_version = new stdClass();
			$joomla_version->type_title = 'Release_checking Joomla_version';
			$joomla_version->type_alias = 'com_release_checking.joomla_version';
			$joomla_version->table = '{"special": {"dbtable": "#__release_checking_joomla_version","key": "id","type": "Joomla_version","prefix": "release_checkingTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$joomla_version->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","alias":"alias"}}';
			$joomla_version->router = 'Release_checkingHelperRoute::getJoomla_versionRoute';
			$joomla_version->content_history_options = '{"formFile": "administrator/components/com_release_checking/models/forms/joomla_version.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$joomla_version_Inserted = $db->insertObject('#__content_types', $joomla_version);

			// Create the context content type object.
			$context = new stdClass();
			$context->type_title = 'Release_checking Context';
			$context->type_alias = 'com_release_checking.context';
			$context->table = '{"special": {"dbtable": "#__release_checking_context","key": "id","type": "Context","prefix": "release_checkingTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$context->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","alias":"alias"}}';
			$context->router = 'Release_checkingHelperRoute::getContextRoute';
			$context->content_history_options = '{"formFile": "administrator/components/com_release_checking/models/forms/context.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$context_Inserted = $db->insertObject('#__content_types', $context);

			// Create the action content type object.
			$action = new stdClass();
			$action->type_title = 'Release_checking Action';
			$action->type_alias = 'com_release_checking.action';
			$action->table = '{"special": {"dbtable": "#__release_checking_action","key": "id","type": "Action","prefix": "release_checkingTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$action->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","context":"context","description":"description","alias":"alias"}}';
			$action->router = 'Release_checkingHelperRoute::getActionRoute';
			$action->content_history_options = '{"formFile": "administrator/components/com_release_checking/models/forms/action.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","context"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "context","targetTable": "#__release_checking_context","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$action_Inserted = $db->insertObject('#__content_types', $action);


			// Install the global extenstion params.
			$query = $db->getQuery(true);
			// Field to update.
			$fields = array(
				$db->quoteName('params') . ' = ' . $db->quote('{"autorName":"Joomla! Project","autorEmail":"admin@joomla.org","check_in":"-1 day","save_history":"1","history_limit":"10"}'),
			);
			// Condition.
			$conditions = array(
				$db->quoteName('element') . ' = ' . $db->quote('com_release_checking')
			);
			$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$allDone = $db->execute();

			echo '<a target="_blank" href="http://www.joomla.org" title="Track Release Checking">
				<img src="components/com_release_checking/assets/images/vdm-component.jpg"/>
				</a>';

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the release_checking action logs extensions object.
			$release_checking_action_logs_extensions = new stdClass();
			$release_checking_action_logs_extensions->extension = 'com_release_checking';

			// Set the object into the action logs extensions table.
			$release_checking_action_logs_extensions_Inserted = $db->insertObject('#__action_logs_extensions', $release_checking_action_logs_extensions);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the release_check action log config object.
			$release_check_action_log_config = new stdClass();
			$release_check_action_log_config->type_title = 'RELEASE_CHECK';
			$release_check_action_log_config->type_alias = 'com_release_checking.release_check';
			$release_check_action_log_config->id_holder = 'id';
			$release_check_action_log_config->title_holder = 'context';
			$release_check_action_log_config->table_name = '#__release_checking_release_check';
			$release_check_action_log_config->text_prefix = 'COM_RELEASE_CHECKING';

			// Set the object into the action log config table.
			$release_check_Inserted = $db->insertObject('#__action_log_config', $release_check_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the joomla_version action log config object.
			$joomla_version_action_log_config = new stdClass();
			$joomla_version_action_log_config->type_title = 'JOOMLA_VERSION';
			$joomla_version_action_log_config->type_alias = 'com_release_checking.joomla_version';
			$joomla_version_action_log_config->id_holder = 'id';
			$joomla_version_action_log_config->title_holder = 'name';
			$joomla_version_action_log_config->table_name = '#__release_checking_joomla_version';
			$joomla_version_action_log_config->text_prefix = 'COM_RELEASE_CHECKING';

			// Set the object into the action log config table.
			$joomla_version_Inserted = $db->insertObject('#__action_log_config', $joomla_version_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the context action log config object.
			$context_action_log_config = new stdClass();
			$context_action_log_config->type_title = 'CONTEXT';
			$context_action_log_config->type_alias = 'com_release_checking.context';
			$context_action_log_config->id_holder = 'id';
			$context_action_log_config->title_holder = 'name';
			$context_action_log_config->table_name = '#__release_checking_context';
			$context_action_log_config->text_prefix = 'COM_RELEASE_CHECKING';

			// Set the object into the action log config table.
			$context_Inserted = $db->insertObject('#__action_log_config', $context_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the action action log config object.
			$action_action_log_config = new stdClass();
			$action_action_log_config->type_title = 'ACTION';
			$action_action_log_config->type_alias = 'com_release_checking.action';
			$action_action_log_config->id_holder = 'id';
			$action_action_log_config->title_holder = 'name';
			$action_action_log_config->table_name = '#__release_checking_action';
			$action_action_log_config->text_prefix = 'COM_RELEASE_CHECKING';

			// Set the object into the action log config table.
			$action_Inserted = $db->insertObject('#__action_log_config', $action_action_log_config);
		}
		// do any updates needed
		if ($type === 'update')
		{

			// Get The Database object
			$db = JFactory::getDbo();

			// Create the release_check content type object.
			$release_check = new stdClass();
			$release_check->type_title = 'Release_checking Release_check';
			$release_check->type_alias = 'com_release_checking.release_check';
			$release_check->table = '{"special": {"dbtable": "#__release_checking_release_check","key": "id","type": "Release_check","prefix": "release_checkingTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$release_check->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "context","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"context":"context","action":"action","outcome":"outcome","joomla_version":"joomla_version"}}';
			$release_check->router = 'Release_checkingHelperRoute::getRelease_checkRoute';
			$release_check->content_history_options = '{"formFile": "administrator/components/com_release_checking/models/forms/release_check.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","context","action","outcome","joomla_version","created_by"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "context","targetTable": "#__release_checking_context","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "action","targetTable": "#__release_checking_action","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "joomla_version","targetTable": "#__release_checking_joomla_version","targetColumn": "id","displayColumn": "name"}]}';

			// Check if release_check type is already in content_type DB.
			$release_check_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($release_check->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$release_check->type_id = $db->loadResult();
				$release_check_Updated = $db->updateObject('#__content_types', $release_check, 'type_id');
			}
			else
			{
				$release_check_Inserted = $db->insertObject('#__content_types', $release_check);
			}

			// Create the joomla_version content type object.
			$joomla_version = new stdClass();
			$joomla_version->type_title = 'Release_checking Joomla_version';
			$joomla_version->type_alias = 'com_release_checking.joomla_version';
			$joomla_version->table = '{"special": {"dbtable": "#__release_checking_joomla_version","key": "id","type": "Joomla_version","prefix": "release_checkingTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$joomla_version->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","alias":"alias"}}';
			$joomla_version->router = 'Release_checkingHelperRoute::getJoomla_versionRoute';
			$joomla_version->content_history_options = '{"formFile": "administrator/components/com_release_checking/models/forms/joomla_version.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if joomla_version type is already in content_type DB.
			$joomla_version_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($joomla_version->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$joomla_version->type_id = $db->loadResult();
				$joomla_version_Updated = $db->updateObject('#__content_types', $joomla_version, 'type_id');
			}
			else
			{
				$joomla_version_Inserted = $db->insertObject('#__content_types', $joomla_version);
			}

			// Create the context content type object.
			$context = new stdClass();
			$context->type_title = 'Release_checking Context';
			$context->type_alias = 'com_release_checking.context';
			$context->table = '{"special": {"dbtable": "#__release_checking_context","key": "id","type": "Context","prefix": "release_checkingTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$context->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","alias":"alias"}}';
			$context->router = 'Release_checkingHelperRoute::getContextRoute';
			$context->content_history_options = '{"formFile": "administrator/components/com_release_checking/models/forms/context.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if context type is already in content_type DB.
			$context_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($context->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$context->type_id = $db->loadResult();
				$context_Updated = $db->updateObject('#__content_types', $context, 'type_id');
			}
			else
			{
				$context_Inserted = $db->insertObject('#__content_types', $context);
			}

			// Create the action content type object.
			$action = new stdClass();
			$action->type_title = 'Release_checking Action';
			$action->type_alias = 'com_release_checking.action';
			$action->table = '{"special": {"dbtable": "#__release_checking_action","key": "id","type": "Action","prefix": "release_checkingTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$action->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","context":"context","description":"description","alias":"alias"}}';
			$action->router = 'Release_checkingHelperRoute::getActionRoute';
			$action->content_history_options = '{"formFile": "administrator/components/com_release_checking/models/forms/action.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","context"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "context","targetTable": "#__release_checking_context","targetColumn": "id","displayColumn": "name"}]}';

			// Check if action type is already in content_type DB.
			$action_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($action->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$action->type_id = $db->loadResult();
				$action_Updated = $db->updateObject('#__content_types', $action, 'type_id');
			}
			else
			{
				$action_Inserted = $db->insertObject('#__content_types', $action);
			}


			echo '<a target="_blank" href="http://www.joomla.org" title="Track Release Checking">
				<img src="components/com_release_checking/assets/images/vdm-component.jpg"/>
				</a>
				<h3>Upgrade to Version 1.0.5 Was Successful! Let us know if anything is not working as expected.</h3>';

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the release_checking action logs extensions object.
			$release_checking_action_logs_extensions = new stdClass();
			$release_checking_action_logs_extensions->extension = 'com_release_checking';

			// Check if release_checking action log extension is already in action logs extensions DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_logs_extensions'));
			$query->where($db->quoteName('extension') . ' LIKE '. $db->quote($release_checking_action_logs_extensions->extension));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the action logs extensions table if not found.
			if (!$db->getNumRows())
			{
				$release_checking_action_logs_extensions_Inserted = $db->insertObject('#__action_logs_extensions', $release_checking_action_logs_extensions);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the release_check action log config object.
			$release_check_action_log_config = new stdClass();
			$release_check_action_log_config->id = null;
			$release_check_action_log_config->type_title = 'RELEASE_CHECK';
			$release_check_action_log_config->type_alias = 'com_release_checking.release_check';
			$release_check_action_log_config->id_holder = 'id';
			$release_check_action_log_config->title_holder = 'context';
			$release_check_action_log_config->table_name = '#__release_checking_release_check';
			$release_check_action_log_config->text_prefix = 'COM_RELEASE_CHECKING';

			// Check if release_check action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($release_check_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$release_check_action_log_config->id = $db->loadResult();
				$release_check_action_log_config_Updated = $db->updateObject('#__action_log_config', $release_check_action_log_config, 'id');
			}
			else
			{
				$release_check_action_log_config_Inserted = $db->insertObject('#__action_log_config', $release_check_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the joomla_version action log config object.
			$joomla_version_action_log_config = new stdClass();
			$joomla_version_action_log_config->id = null;
			$joomla_version_action_log_config->type_title = 'JOOMLA_VERSION';
			$joomla_version_action_log_config->type_alias = 'com_release_checking.joomla_version';
			$joomla_version_action_log_config->id_holder = 'id';
			$joomla_version_action_log_config->title_holder = 'name';
			$joomla_version_action_log_config->table_name = '#__release_checking_joomla_version';
			$joomla_version_action_log_config->text_prefix = 'COM_RELEASE_CHECKING';

			// Check if joomla_version action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($joomla_version_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$joomla_version_action_log_config->id = $db->loadResult();
				$joomla_version_action_log_config_Updated = $db->updateObject('#__action_log_config', $joomla_version_action_log_config, 'id');
			}
			else
			{
				$joomla_version_action_log_config_Inserted = $db->insertObject('#__action_log_config', $joomla_version_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the context action log config object.
			$context_action_log_config = new stdClass();
			$context_action_log_config->id = null;
			$context_action_log_config->type_title = 'CONTEXT';
			$context_action_log_config->type_alias = 'com_release_checking.context';
			$context_action_log_config->id_holder = 'id';
			$context_action_log_config->title_holder = 'name';
			$context_action_log_config->table_name = '#__release_checking_context';
			$context_action_log_config->text_prefix = 'COM_RELEASE_CHECKING';

			// Check if context action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($context_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$context_action_log_config->id = $db->loadResult();
				$context_action_log_config_Updated = $db->updateObject('#__action_log_config', $context_action_log_config, 'id');
			}
			else
			{
				$context_action_log_config_Inserted = $db->insertObject('#__action_log_config', $context_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the action action log config object.
			$action_action_log_config = new stdClass();
			$action_action_log_config->id = null;
			$action_action_log_config->type_title = 'ACTION';
			$action_action_log_config->type_alias = 'com_release_checking.action';
			$action_action_log_config->id_holder = 'id';
			$action_action_log_config->title_holder = 'name';
			$action_action_log_config->table_name = '#__release_checking_action';
			$action_action_log_config->text_prefix = 'COM_RELEASE_CHECKING';

			// Check if action action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($action_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$action_action_log_config->id = $db->loadResult();
				$action_action_log_config_Updated = $db->updateObject('#__action_log_config', $action_action_log_config, 'id');
			}
			else
			{
				$action_action_log_config_Inserted = $db->insertObject('#__action_log_config', $action_action_log_config);
			}
		}
		return true;
	}

	/**
	 * Remove folders with files
	 * 
	 * @param   string   $dir     The path to folder to remove
	 * @param   boolean  $ignore  The folders and files to ignore and not remove
	 *
	 * @return  boolean   True in all is removed
	 * 
	 */
	protected function removeFolder($dir, $ignore = false)
	{
		if (JFolder::exists($dir))
		{
			$it = new RecursiveDirectoryIterator($dir);
			$it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
			// remove ending /
			$dir = rtrim($dir, '/');
			// now loop the files & folders
			foreach ($it as $file)
			{
				if ('.' === $file->getBasename() || '..' ===  $file->getBasename()) continue;
				// set file dir
				$file_dir = $file->getPathname();
				// check if this is a dir or a file
				if ($file->isDir())
				{
					$keeper = false;
					if ($this->checkArray($ignore))
					{
						foreach ($ignore as $keep)
						{
							if (strpos($file_dir, $dir.'/'.$keep) !== false)
							{
								$keeper = true;
							}
						}
					}
					if ($keeper)
					{
						continue;
					}
					JFolder::delete($file_dir);
				}
				else
				{
					$keeper = false;
					if ($this->checkArray($ignore))
					{
						foreach ($ignore as $keep)
						{
							if (strpos($file_dir, $dir.'/'.$keep) !== false)
							{
								$keeper = true;
							}
						}
					}
					if ($keeper)
					{
						continue;
					}
					JFile::delete($file_dir);
				}
			}
			// delete the root folder if not ignore found
			if (!$this->checkArray($ignore))
			{
				return JFolder::delete($dir);
			}
			return true;
		}
		return false;
	}

	/**
	 * Check if have an array with a length
	 *
	 * @input	array   The array to check
	 *
	 * @returns bool/int  number of items in array on success
	 */
	protected function checkArray($array, $removeEmptyString = false)
	{
		if (isset($array) && is_array($array) && ($nr = count((array)$array)) > 0)
		{
			// also make sure the empty strings are removed
			if ($removeEmptyString)
			{
				foreach ($array as $key => $string)
				{
					if (empty($string))
					{
						unset($array[$key]);
					}
				}
				return $this->checkArray($array, false);
			}
			return $nr;
		}
		return false;
	}

	/**
	 * Method to set/copy dynamic folders into place (use with caution)
	 *
	 * @return void
	 */
	protected function setDynamicF0ld3rs($app, $parent)
	{
		// get the instalation path
		$installer = $parent->getParent();
		$installPath = $installer->getPath('source');
		// get all the folders
		$folders = JFolder::folders($installPath);
		// check if we have folders we may want to copy
		$doNotCopy = array('media','admin','site'); // Joomla already deals with these
		if (count((array) $folders) > 1)
		{
			foreach ($folders as $folder)
			{
				// Only copy if not a standard folders
				if (!in_array($folder, $doNotCopy))
				{
					// set the source path
					$src = $installPath.'/'.$folder;
					// set the destination path
					$dest = JPATH_ROOT.'/'.$folder;
					// now try to copy the folder
					if (!JFolder::copy($src, $dest, '', true))
					{
						$app->enqueueMessage('Could not copy '.$folder.' folder into place, please make sure destination is writable!', 'error');
					}
				}
			}
		}
	}
}
