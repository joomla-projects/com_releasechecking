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
 * Releasechecksfilteroutcome Form Field class for the Release_checking component
 */
class JFormFieldReleasechecksfilteroutcome extends JFormFieldList
{
	/**
	 * The releasechecksfilteroutcome field type.
	 *
	 * @var		string
	 */
	public $type = 'releasechecksfilteroutcome';

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
		$query->select($db->quoteName('outcome'));
		$query->from($db->quoteName('#__release_checking_release_check'));
		$query->order($db->quoteName('outcome') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$results = $db->loadColumn();
		$_filter = array();

		if ($results)
		{
			// get release_checksmodel
			$model = Release_checkingHelper::getModel('release_checks');
			$results = array_unique($results);
			foreach ($results as $outcome)
			{
				// Translate the outcome selection
				$text = $model->selectionTranslation($outcome,'outcome');
				// Now add the outcome and its text to the options array
				$_filter[] = JHtml::_('select.option', $outcome, JText::_($text));
			}
		}
		return $_filter;
	}
}
