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
 * Joomla_versions Controller
 */
class Release_checkingControllerJoomla_versions extends JControllerAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_RELEASE_CHECKING_JOOMLA_VERSIONS';

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JModelLegacy  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Joomla_version', $prefix = 'Release_checkingModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function exportData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if export is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('joomla_version.export', 'com_release_checking') && $user->authorise('core.export', 'com_release_checking'))
		{
			// Get the input
			$input = JFactory::getApplication()->input;
			$pks = $input->post->get('cid', array(), 'array');
			// Sanitize the input
			$pks = ArrayHelper::toInteger($pks);
			// Get the model
			$model = $this->getModel('Joomla_versions');
			// get the data to export
			$data = $model->getExportData($pks);
			if (Release_checkingHelper::checkArray($data))
			{
				// now set the data to the spreadsheet
				$date = JFactory::getDate();
				Release_checkingHelper::xls($data,'Joomla_versions_'.$date->format('jS_F_Y'),'Joomla versions exported ('.$date->format('jS F, Y').')','joomla versions');
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_RELEASE_CHECKING_EXPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_release_checking&view=joomla_versions', false), $message, 'error');
		return;
	}


	public function importData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if import is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('joomla_version.import', 'com_release_checking') && $user->authorise('core.import', 'com_release_checking'))
		{
			// Get the import model
			$model = $this->getModel('Joomla_versions');
			// get the headers to import
			$headers = $model->getExImPortHeaders();
			if (Release_checkingHelper::checkObject($headers))
			{
				// Load headers to session.
				$session = JFactory::getSession();
				$headers = json_encode($headers);
				$session->set('joomla_version_VDM_IMPORTHEADERS', $headers);
				$session->set('backto_VDM_IMPORT', 'joomla_versions');
				$session->set('dataType_VDM_IMPORTINTO', 'joomla_version');
				// Redirect to import view.
				$message = JText::_('COM_RELEASE_CHECKING_IMPORT_SELECT_FILE_FOR_JOOMLA_VERSIONS');
				$this->setRedirect(JRoute::_('index.php?option=com_release_checking&view=import', false), $message);
				return;
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_RELEASE_CHECKING_IMPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_release_checking&view=joomla_versions', false), $message, 'error');
		return;
	}
}
