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

/**
 * Release_checking View class for the Release_checks
 */
class Release_checkingViewRelease_checks extends JViewLegacy
{
	/**
	 * Release_checks view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		if ($this->getLayout() !== 'modal')
		{
			// Include helper submenu
			Release_checkingHelper::addSubmenu('release_checks');
		}

		// Assign data to the view
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->user = JFactory::getUser();
		// Load the filter form from xml.
		$this->filterForm = $this->get('FilterForm');
		// Load the active filters.
		$this->activeFilters = $this->get('ActiveFilters');
		// Add the list ordering clause.
		$this->listOrder = $this->escape($this->state->get('list.ordering', 'a.id'));
		$this->listDirn = $this->escape($this->state->get('list.direction', 'desc'));
		$this->saveOrder = $this->listOrder == 'a.ordering';
		// set the return here value
		$this->return_here = urlencode(base64_encode((string) JUri::getInstance()));
		// get global action permissions
		$this->canDo = Release_checkingHelper::getActions('release_check');
		$this->canEdit = $this->canDo->get('core.edit');
		$this->canState = $this->canDo->get('core.edit.state');
		$this->canCreate = $this->canDo->get('core.create');
		$this->canDelete = $this->canDo->get('core.delete');
		$this->canBatch = $this->canDo->get('core.batch');

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
			// load the batch html
			if ($this->canCreate && $this->canEdit && $this->canState)
			{
				$this->batchDisplay = JHtmlBatch_::render();
			}
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		JToolBarHelper::title(JText::_('COM_RELEASE_CHECKING_RELEASE_CHECKS'), 'pencil-2');
		JHtmlSidebar::setAction('index.php?option=com_release_checking&view=release_checks');
		JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

		if ($this->canCreate)
		{
			JToolBarHelper::addNew('release_check.add');
		}

		// Only load if there are items
		if (Release_checkingHelper::checkArray($this->items))
		{
			if ($this->canEdit)
			{
				JToolBarHelper::editList('release_check.edit');
			}

			if ($this->canState)
			{
				JToolBarHelper::publishList('release_checks.publish');
				JToolBarHelper::unpublishList('release_checks.unpublish');
				JToolBarHelper::archiveList('release_checks.archive');

				if ($this->canDo->get('core.admin'))
				{
					JToolBarHelper::checkin('release_checks.checkin');
				}
			}

			// Add a batch button
			if ($this->canBatch && $this->canCreate && $this->canEdit && $this->canState)
			{
				// Get the toolbar object instance
				$bar = JToolBar::getInstance('toolbar');
				// set the batch button name
				$title = JText::_('JTOOLBAR_BATCH');
				// Instantiate a new JLayoutFile instance and render the batch button
				$layout = new JLayoutFile('joomla.toolbar.batch');
				// add the button to the page
				$dhtml = $layout->render(array('title' => $title));
				$bar->appendButton('Custom', $dhtml, 'batch');
			}

			if ($this->state->get('filter.published') == -2 && ($this->canState && $this->canDelete))
			{
				JToolbarHelper::deleteList('', 'release_checks.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($this->canState && $this->canDelete)
			{
				JToolbarHelper::trash('release_checks.trash');
			}

			if ($this->canDo->get('core.export') && $this->canDo->get('release_check.export'))
			{
				JToolBarHelper::custom('release_checks.exportData', 'download', '', 'COM_RELEASE_CHECKING_EXPORT_DATA', true);
			}
		}

		if ($this->canDo->get('core.import') && $this->canDo->get('release_check.import'))
		{
			JToolBarHelper::custom('release_checks.importData', 'upload', '', 'COM_RELEASE_CHECKING_IMPORT_DATA', false);
		}

		// set help url for this view if found
		$help_url = Release_checkingHelper::getHelpUrl('release_checks');
		if (Release_checkingHelper::checkString($help_url))
		{
				JToolbarHelper::help('COM_RELEASE_CHECKING_HELP_MANAGER', false, $help_url);
		}

		// add the options comp button
		if ($this->canDo->get('core.admin') || $this->canDo->get('core.options'))
		{
			JToolBarHelper::preferences('com_release_checking');
		}

		// Only load published batch if state and batch is allowed
		if ($this->canState && $this->canBatch)
		{
			JHtmlBatch_::addListSelection(
				JText::_('COM_RELEASE_CHECKING_KEEP_ORIGINAL_STATE'),
				'batch[published]',
				JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('all' => false)), 'value', 'text', '', true)
			);
		}

		// Only load access batch if create, edit and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			JHtmlBatch_::addListSelection(
				JText::_('COM_RELEASE_CHECKING_KEEP_ORIGINAL_ACCESS'),
				'batch[access]',
				JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text')
			);
		}

		// Only load Context Name batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Context Name Selection
			$this->contextNameOptions = JFormHelper::loadFieldType('Contexts')->options;
			// We do some sanitation for Context Name filter
			if (Release_checkingHelper::checkArray($this->contextNameOptions) &&
				isset($this->contextNameOptions[0]->value) &&
				!Release_checkingHelper::checkString($this->contextNameOptions[0]->value))
			{
				unset($this->contextNameOptions[0]);
			}
			// Context Name Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_CONTEXT_LABEL').' -',
				'batch[context]',
				JHtml::_('select.options', $this->contextNameOptions, 'value', 'text')
			);
		}

		// Only load Action Name batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Action Name Selection
			$this->actionNameOptions = JFormHelper::loadFieldType('Actions')->options;
			// We do some sanitation for Action Name filter
			if (Release_checkingHelper::checkArray($this->actionNameOptions) &&
				isset($this->actionNameOptions[0]->value) &&
				!Release_checkingHelper::checkString($this->actionNameOptions[0]->value))
			{
				unset($this->actionNameOptions[0]);
			}
			// Action Name Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_ACTION_LABEL').' -',
				'batch[action]',
				JHtml::_('select.options', $this->actionNameOptions, 'value', 'text')
			);
		}

		// Only load Outcome batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Outcome Selection
			$this->outcomeOptions = JFormHelper::loadFieldType('releasechecksfilteroutcome')->options;
			// We do some sanitation for Outcome filter
			if (Release_checkingHelper::checkArray($this->outcomeOptions) &&
				isset($this->outcomeOptions[0]->value) &&
				!Release_checkingHelper::checkString($this->outcomeOptions[0]->value))
			{
				unset($this->outcomeOptions[0]);
			}
			// Outcome Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_OUTCOME_LABEL').' -',
				'batch[outcome]',
				JHtml::_('select.options', $this->outcomeOptions, 'value', 'text')
			);
		}

		// Only load Joomla Version Name batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Joomla Version Name Selection
			$this->joomla_versionNameOptions = JFormHelper::loadFieldType('Joomlaversions')->options;
			// We do some sanitation for Joomla Version Name filter
			if (Release_checkingHelper::checkArray($this->joomla_versionNameOptions) &&
				isset($this->joomla_versionNameOptions[0]->value) &&
				!Release_checkingHelper::checkString($this->joomla_versionNameOptions[0]->value))
			{
				unset($this->joomla_versionNameOptions[0]);
			}
			// Joomla Version Name Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_JOOMLA_VERSION_LABEL').' -',
				'batch[joomla_version]',
				JHtml::_('select.options', $this->joomla_versionNameOptions, 'value', 'text')
			);
		}

		// Only load Created By batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Created By Selection
			$this->created_byOptions = JFormHelper::loadFieldType('releasechecksfiltercreatedby')->options;
			// We do some sanitation for Created By filter
			if (Release_checkingHelper::checkArray($this->created_byOptions) &&
				isset($this->created_byOptions[0]->value) &&
				!Release_checkingHelper::checkString($this->created_byOptions[0]->value))
			{
				unset($this->created_byOptions[0]);
			}
			// Created By Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_CREATED_BY_LABEL').' -',
				'batch[created_by]',
				JHtml::_('select.options', $this->created_byOptions, 'value', 'text')
			);
		}
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		if (!isset($this->document))
		{
			$this->document = JFactory::getDocument();
		}
		$this->document->setTitle(JText::_('COM_RELEASE_CHECKING_RELEASE_CHECKS'));
		$this->document->addStyleSheet(JURI::root() . "administrator/components/com_release_checking/assets/css/release_checks.css", (Release_checkingHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * @param   mixed  $var  The output to escape.
	 *
	 * @return  mixed  The escaped value.
	 */
	public function escape($var)
	{
		if(strlen($var) > 50)
		{
			// use the helper htmlEscape method instead and shorten the string
			return Release_checkingHelper::htmlEscape($var, $this->_charset, true);
		}
		// use the helper htmlEscape method instead.
		return Release_checkingHelper::htmlEscape($var, $this->_charset);
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 */
	protected function getSortFields()
	{
		return array(
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.published' => JText::_('JSTATUS'),
			'g.name' => JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_CONTEXT_LABEL'),
			'h.name' => JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_ACTION_LABEL'),
			'a.outcome' => JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_OUTCOME_LABEL'),
			'i.name' => JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_JOOMLA_VERSION_LABEL'),
			'a.created_by' => JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_CREATED_BY_LABEL'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
