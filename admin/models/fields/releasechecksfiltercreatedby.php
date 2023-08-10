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

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Releasechecksfiltercreatedby Form Field class for the Release_checking component
 */
class JFormFieldReleasechecksfiltercreatedby extends JFormFieldList
{
	/**
	 * The releasechecksfiltercreatedby field type.
	 *
	 * @var		string
	 */
	public $type = 'releasechecksfiltercreatedby';

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
		$query->select($db->quoteName('created_by'));
		$query->from($db->quoteName('#__release_checking_release_check'));
		$query->order($db->quoteName('created_by') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$_results = $db->loadColumn();
		$_filter = array();

		if ($_results)
		{
			$_results = array_unique($_results);
			foreach ($_results as $created_by)
			{
				// Now add the created_by and its text to the options array
				$_filter[] = JHtml::_('select.option', $created_by, JFactory::getUser($created_by)->name);
			}
		}
		return $_filter;
	}
}
