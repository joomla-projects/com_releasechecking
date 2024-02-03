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
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use VDM\Component\Releasechecking\Administrator\Helper\ReleasecheckingHelper;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * Releasechecking Import Base Controller
 *
 * @since  1.6
 */
class ImportController extends BaseController
{
	/**
	 * Import an spreadsheet.
	 *
	 * @return  void
	 */
	public function import()
	{
		// Check for request forgeries
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$model = $this->getModel('import');
		if ($model->import())
		{
			$cache = Factory::getCache('mod_menu');
			$cache->clean();
			// TODO: Reset the users acl here as well to kill off any missing bits
		}

		$app = Factory::getApplication();
		$redirect_url = $app->getUserState('com_releasechecking.redirect_url');
		if (empty($redirect_url))
		{
			$redirect_url = Route::_('index.php?option=com_releasechecking&view=import', false);
		}
		else
		{
			// wipe out the user state when we're going to redirect
			$app->setUserState('com_releasechecking.redirect_url', '');
			$app->setUserState('com_releasechecking.message', '');
			$app->setUserState('com_releasechecking.extension_message', '');
		}
		$this->setRedirect($redirect_url);
	}
}
