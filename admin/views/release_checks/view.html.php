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
		// Add the list ordering clause.
		$this->listOrder = $this->escape($this->state->get('list.ordering', 'a.id'));
		$this->listDirn = $this->escape($this->state->get('list.direction', 'desc'));
		$this->saveOrder = $this->listOrder == 'ordering';
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

		if ($this->canState)
		{
			JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_PUBLISHED'),
				'filter_published',
				JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
			);
			// only load if batch allowed
			if ($this->canBatch)
			{
				JHtmlBatch_::addListSelection(
					JText::_('COM_RELEASE_CHECKING_KEEP_ORIGINAL_STATE'),
					'batch[published]',
					JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('all' => false)), 'value', 'text', '', true)
				);
			}
		}

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_ACCESS'),
			'filter_access',
			JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
		);

		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			JHtmlBatch_::addListSelection(
				JText::_('COM_RELEASE_CHECKING_KEEP_ORIGINAL_ACCESS'),
				'batch[access]',
				JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text')
			);
		}

		// Set Context Name Selection
		$this->contextNameOptions = JFormHelper::loadFieldType('Contexts')->options;
		// We do some sanitation for Context Name filter
		if (Release_checkingHelper::checkArray($this->contextNameOptions) &&
			isset($this->contextNameOptions[0]->value) &&
			!Release_checkingHelper::checkString($this->contextNameOptions[0]->value))
		{
			unset($this->contextNameOptions[0]);
		}
		// Only load Context Name filter if it has values
		if (Release_checkingHelper::checkArray($this->contextNameOptions))
		{
			// Context Name Filter
			JHtmlSidebar::addFilter(
				'- Select '.JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_CONTEXT_LABEL').' -',
				'filter_context',
				JHtml::_('select.options', $this->contextNameOptions, 'value', 'text', $this->state->get('filter.context'))
			);

			if ($this->canBatch && $this->canCreate && $this->canEdit)
			{
				// Context Name Batch Selection
				JHtmlBatch_::addListSelection(
					'- Keep Original '.JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_CONTEXT_LABEL').' -',
					'batch[context]',
					JHtml::_('select.options', $this->contextNameOptions, 'value', 'text')
				);
			}
		}

		// Set Action Name Selection
		$this->actionNameOptions = JFormHelper::loadFieldType('Actions')->options;
		// We do some sanitation for Action Name filter
		if (Release_checkingHelper::checkArray($this->actionNameOptions) &&
			isset($this->actionNameOptions[0]->value) &&
			!Release_checkingHelper::checkString($this->actionNameOptions[0]->value))
		{
			unset($this->actionNameOptions[0]);
		}
		// Only load Action Name filter if it has values
		if (Release_checkingHelper::checkArray($this->actionNameOptions))
		{
			// Action Name Filter
			JHtmlSidebar::addFilter(
				'- Select '.JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_ACTION_LABEL').' -',
				'filter_action',
				JHtml::_('select.options', $this->actionNameOptions, 'value', 'text', $this->state->get('filter.action'))
			);

			if ($this->canBatch && $this->canCreate && $this->canEdit)
			{
				// Action Name Batch Selection
				JHtmlBatch_::addListSelection(
					'- Keep Original '.JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_ACTION_LABEL').' -',
					'batch[action]',
					JHtml::_('select.options', $this->actionNameOptions, 'value', 'text')
				);
			}
		}

		// Set Outcome Selection
		$this->outcomeOptions = $this->getTheOutcomeSelections();
		// We do some sanitation for Outcome filter
		if (Release_checkingHelper::checkArray($this->outcomeOptions) &&
			isset($this->outcomeOptions[0]->value) &&
			!Release_checkingHelper::checkString($this->outcomeOptions[0]->value))
		{
			unset($this->outcomeOptions[0]);
		}
		// Only load Outcome filter if it has values
		if (Release_checkingHelper::checkArray($this->outcomeOptions))
		{
			// Outcome Filter
			JHtmlSidebar::addFilter(
				'- Select '.JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_OUTCOME_LABEL').' -',
				'filter_outcome',
				JHtml::_('select.options', $this->outcomeOptions, 'value', 'text', $this->state->get('filter.outcome'))
			);

			if ($this->canBatch && $this->canCreate && $this->canEdit)
			{
				// Outcome Batch Selection
				JHtmlBatch_::addListSelection(
					'- Keep Original '.JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_OUTCOME_LABEL').' -',
					'batch[outcome]',
					JHtml::_('select.options', $this->outcomeOptions, 'value', 'text')
				);
			}
		}

		// Set Joomla Version Name Selection
		$this->joomla_versionNameOptions = JFormHelper::loadFieldType('Joomlaversions')->options;
		// We do some sanitation for Joomla Version Name filter
		if (Release_checkingHelper::checkArray($this->joomla_versionNameOptions) &&
			isset($this->joomla_versionNameOptions[0]->value) &&
			!Release_checkingHelper::checkString($this->joomla_versionNameOptions[0]->value))
		{
			unset($this->joomla_versionNameOptions[0]);
		}
		// Only load Joomla Version Name filter if it has values
		if (Release_checkingHelper::checkArray($this->joomla_versionNameOptions))
		{
			// Joomla Version Name Filter
			JHtmlSidebar::addFilter(
				'- Select '.JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_JOOMLA_VERSION_LABEL').' -',
				'filter_joomla_version',
				JHtml::_('select.options', $this->joomla_versionNameOptions, 'value', 'text', $this->state->get('filter.joomla_version'))
			);

			if ($this->canBatch && $this->canCreate && $this->canEdit)
			{
				// Joomla Version Name Batch Selection
				JHtmlBatch_::addListSelection(
					'- Keep Original '.JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_JOOMLA_VERSION_LABEL').' -',
					'batch[joomla_version]',
					JHtml::_('select.options', $this->joomla_versionNameOptions, 'value', 'text')
				);
			}
		}

		// Set Created By Selection
		$this->created_byOptions = $this->getTheCreated_bySelections();
		// We do some sanitation for Created By filter
		if (Release_checkingHelper::checkArray($this->created_byOptions) &&
			isset($this->created_byOptions[0]->value) &&
			!Release_checkingHelper::checkString($this->created_byOptions[0]->value))
		{
			unset($this->created_byOptions[0]);
		}
		// Only load Created By filter if it has values
		if (Release_checkingHelper::checkArray($this->created_byOptions))
		{
			// Created By Filter
			JHtmlSidebar::addFilter(
				'- Select '.JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_CREATED_BY_LABEL').' -',
				'filter_created_by',
				JHtml::_('select.options', $this->created_byOptions, 'value', 'text', $this->state->get('filter.created_by'))
			);

			if ($this->canBatch && $this->canCreate && $this->canEdit)
			{
				// Created By Batch Selection
				JHtmlBatch_::addListSelection(
					'- Keep Original '.JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_CREATED_BY_LABEL').' -',
					'batch[created_by]',
					JHtml::_('select.options', $this->created_byOptions, 'value', 'text')
				);
			}
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
			'ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.published' => JText::_('JSTATUS'),
			'g.name' => JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_CONTEXT_LABEL'),
			'h.name' => JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_ACTION_LABEL'),
			'a.outcome' => JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_OUTCOME_LABEL'),
			'i.name' => JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_JOOMLA_VERSION_LABEL'),
			'a.created_by' => JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_CREATED_BY_LABEL'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}

	protected function getTheOutcomeSelections()
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select the text.
		$query->select($db->quoteName('outcome'));
		$query->from($db->quoteName('#__release_checking_release_check'));
		$query->order($db->quoteName('outcome') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$results = $db->loadColumn();

		if ($results)
		{
			// get model
			$model = $this->getModel();
			$results = array_unique($results);
			$_filter = array();
			foreach ($results as $outcome)
			{
				// Translate the outcome selection
				$text = $model->selectionTranslation($outcome,'outcome');
				// Now add the outcome and its text to the options array
				$_filter[] = JHtml::_('select.option', $outcome, JText::_($text));
			}
			return $_filter;
		}
		return false;
	}

	protected function getTheCreated_bySelections()
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select the text.
		$query->select($db->quoteName('created_by'));
		$query->from($db->quoteName('#__release_checking_release_check'));
		$query->order($db->quoteName('created_by') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$results = $db->loadColumn();

		if ($results)
		{
			$results = array_unique($results);
			$_filter = array();
			foreach ($results as $created_by)
			{
				// Now add the created_by and its text to the options array
				$_filter[] = JHtml::_('select.option', $created_by, JFactory::getUser($created_by)->name);
			}
			return $_filter;
		}
		return false;
	}
}
