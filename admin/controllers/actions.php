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

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\Utilities\ArrayHelper;

/**
 * Actions Admin Controller
 */
class Release_checkingControllerActions extends AdminController
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_RELEASE_CHECKING_ACTIONS';

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
	public function getModel($name = 'Action', $prefix = 'Release_checkingModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function exportData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if export is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('action.export', 'com_release_checking') && $user->authorise('core.export', 'com_release_checking'))
		{
			// Get the input
			$input = JFactory::getApplication()->input;
			$pks = $input->post->get('cid', array(), 'array');
			// Sanitize the input
			$pks = ArrayHelper::toInteger($pks);
			// Get the model
			$model = $this->getModel('Actions');
			// get the data to export
			$data = $model->getExportData($pks);
			if (Release_checkingHelper::checkArray($data))
			{
				// now set the data to the spreadsheet
				$date = JFactory::getDate();
				Release_checkingHelper::xls($data,'Actions_'.$date->format('jS_F_Y'),'Actions exported ('.$date->format('jS F, Y').')','actions');
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_RELEASE_CHECKING_EXPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_release_checking&view=actions', false), $message, 'error');
		return;
	}


	public function importData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if import is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('action.import', 'com_release_checking') && $user->authorise('core.import', 'com_release_checking'))
		{
			// Get the import model
			$model = $this->getModel('Actions');
			// get the headers to import
			$headers = $model->getExImPortHeaders();
			if (Release_checkingHelper::checkObject($headers))
			{
				// Load headers to session.
				$session = JFactory::getSession();
				$headers = json_encode($headers);
				$session->set('action_VDM_IMPORTHEADERS', $headers);
				$session->set('backto_VDM_IMPORT', 'actions');
				$session->set('dataType_VDM_IMPORTINTO', 'action');
				// Redirect to import view.
				$message = JText::_('COM_RELEASE_CHECKING_IMPORT_SELECT_FILE_FOR_ACTIONS');
				$this->setRedirect(JRoute::_('index.php?option=com_release_checking&view=import', false), $message);
				return;
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_RELEASE_CHECKING_IMPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_release_checking&view=actions', false), $message, 'error');
		return;
	}
}
