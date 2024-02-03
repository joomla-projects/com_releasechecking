<?php
/**
 * @package    Joomla.CMS
 * @maintainer Llewellyn van der Merwe <https://git.vdm.dev/Llewellyn>
 *
 * @created    29th July, 2020
 * @copyright  (C) 2020 Open Source Matters, Inc. <http://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace VDM\Component\Releasechecking\Administrator\View\Import;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use VDM\Component\Releasechecking\Administrator\Helper\ReleasecheckingHelper;
use VDM\Joomla\Utilities\StringHelper;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * Releasechecking Import Html View
 *
 * @since  1.6
 */
class HtmlView extends BaseHtmlView
{
	protected $headerList;
	protected $hasPackage = false;
	protected $headers;
	protected $hasHeader = 0;
	protected $dataType;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 * @since  1.6
	 */
	public function display($tpl = null)
	{
		$paths = new \StdClass;
		$paths->first = '';
		$state = $this->get('state');

		$this->paths = &$paths;
		$this->state = &$state;
		// get global action permissions
		$this->canDo = ReleasecheckingHelper::getActions('import');

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
		}

		// get the session object
		$session = Factory::getSession();
		// check if it has package
		$this->hasPackage     = $session->get('hasPackage', false);
		$this->dataType     = $session->get('dataType', false);
		if($this->hasPackage && $this->dataType)
		{
			$this->headerList     = json_decode($session->get($this->dataType.'_VDM_IMPORTHEADERS', false),true);
			$this->headers         = ReleasecheckingHelper::getFileHeaders($this->dataType);
			// clear the data type
			$session->clear('dataType');
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \Exception(implode("\n", $errors), 500);
		}

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 * @since   1.6
	 */
	protected function addToolbar(): void
	{
		ToolbarHelper::title(Text::_('COM_RELEASECHECKING_IMPORT_TITLE'), 'upload');

		if ($this->canDo->get('core.admin') || $this->canDo->get('core.options'))
		{
			ToolbarHelper::preferences('com_releasechecking');
		}

		// set help url for this view if found
		$this->help_url = ReleasecheckingHelper::getHelpUrl('import');
		if (StringHelper::check($this->help_url))
		{
			ToolbarHelper::help('COM_RELEASECHECKING_HELP_MANAGER', false, $this->help_url);
		}
	}
}
