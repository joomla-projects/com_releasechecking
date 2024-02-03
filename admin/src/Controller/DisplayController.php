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

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * Releasechecking master admin display controller.
 *
 * @since  4.0
 */
class DisplayController extends BaseController
{
	/**
	 * The default view.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $default_view = 'release_checks';

	/**
	 * display task
	 *
	 * @return void
	 */
	function display($cachable = false, $urlparams = false)
	{
		return parent::display($cachable, $urlparams);
	}
}
