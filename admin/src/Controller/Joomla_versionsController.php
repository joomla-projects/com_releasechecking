<?php
/**
 * @package    Joomla.CMS
 * @maintainer Llewellyn van der Merwe <https://git.vdm.dev/Llewellyn>
 *
 * @created    29th July, 2020
 * @copyright  (C) 2020 Open Source Matters, Inc. <http://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace VDM\Component\Releasechecking\Administrator\Controller;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use VDM\Component\Releasechecking\Administrator\Helper\ReleasecheckingHelper;
use VDM\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;
use VDM\Joomla\Utilities\ObjectHelper;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * Joomla_versions Admin Controller
 *
 * @since  1.6
 */
class Joomla_versionsController extends AdminController
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_RELEASECHECKING_JOOMLA_VERSIONS';

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Joomla_version', $prefix = 'Administrator', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function exportData()
	{
		// Check for request forgeries
		Session::checkToken() or die(Text::_('JINVALID_TOKEN'));
		// check if export is allowed for this user.
		$user = Factory::getApplication()->getIdentity();
		if ($user->authorise('joomla_version.export', 'com_releasechecking') && $user->authorise('core.export', 'com_releasechecking'))
		{
			// Get the input
			$input = Factory::getApplication()->input;
			$pks = $input->post->get('cid', array(), 'array');
			// Sanitize the input
			$pks = ArrayHelper::toInteger($pks);
			// Get the model
			$model = $this->getModel('Joomla_versions');
			// get the data to export
			$data = $model->getExportData($pks);
			if (UtilitiesArrayHelper::check($data))
			{
				// now set the data to the spreadsheet
				$date = Factory::getDate();
				ReleasecheckingHelper::xls($data,'Joomla_versions_'.$date->format('jS_F_Y'),'Joomla versions exported ('.$date->format('jS F, Y').')','joomla versions');
			}
		}
		// Redirect to the list screen with error.
		$message = Text::_('COM_RELEASECHECKING_EXPORT_FAILED');
		$this->setRedirect(Route::_('index.php?option=com_releasechecking&view=joomla_versions', false), $message, 'error');
		return;
	}


	public function importData()
	{
		// Check for request forgeries
		Session::checkToken() or die(Text::_('JINVALID_TOKEN'));
		// check if import is allowed for this user.
		$user = Factory::getApplication()->getIdentity();
		if ($user->authorise('joomla_version.import', 'com_releasechecking') && $user->authorise('core.import', 'com_releasechecking'))
		{
			// Get the import model
			$model = $this->getModel('Joomla_versions');
			// get the headers to import
			$headers = $model->getExImPortHeaders();
			if (ObjectHelper::check($headers))
			{
				// Load headers to session.
				$session = Factory::getSession();
				$headers = json_encode($headers);
				$session->set('joomla_version_VDM_IMPORTHEADERS', $headers);
				$session->set('backto_VDM_IMPORT', 'joomla_versions');
				$session->set('dataType_VDM_IMPORTINTO', 'joomla_version');
				// Redirect to import view.
				$message = Text::_('COM_RELEASECHECKING_IMPORT_SELECT_FILE_FOR_JOOMLA_VERSIONS');
				$this->setRedirect(Route::_('index.php?option=com_releasechecking&view=import', false), $message);
				return;
			}
		}
		// Redirect to the list screen with error.
		$message = Text::_('COM_RELEASECHECKING_IMPORT_FAILED');
		$this->setRedirect(Route::_('index.php?option=com_releasechecking&view=joomla_versions', false), $message, 'error');
		return;
	}
}