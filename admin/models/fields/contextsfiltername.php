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

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Contextsfiltername Form Field class for the Release_checking component
 */
class JFormFieldContextsfiltername extends JFormFieldList
{
	/**
	 * The contextsfiltername field type.
	 *
	 * @var		string
	 */
	public $type = 'contextsfiltername';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array    An array of JHtml options.
	 */
	protected function getOptions()
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select the text.
		$query->select($db->quoteName('name'));
		$query->from($db->quoteName('#__release_checking_context'));
		$query->order($db->quoteName('name') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$results = $db->loadColumn();
		$_filter = array();
		$_filter[] = JHtml::_('select.option', '', '- ' . JText::_('COM_RELEASE_CHECKING_FILTER_SELECT_NAME') . ' -');

		if ($results)
		{
			$results = array_unique($results);
			foreach ($results as $name)
			{
				// Now add the name and its text to the options array
				$_filter[] = JHtml::_('select.option', $name, $name);
			}
		}
		return $_filter;
	}
}
