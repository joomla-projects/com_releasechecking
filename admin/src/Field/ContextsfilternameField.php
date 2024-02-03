<?php
/**
 * @package    Joomla.CMS
 * @maintainer Llewellyn van der Merwe <https://git.vdm.dev/Llewellyn>
 *
 * @created    29th July, 2020
 * @copyright  (C) 2020 Open Source Matters, Inc. <http://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace VDM\Component\Releasechecking\Administrator\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Component\ComponentHelper;
use VDM\Component\Releasechecking\Administrator\Helper\ReleasecheckingHelper;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * Contextsfiltername Form Field class for the Releasechecking component
 *
 * @since  1.6
 */
class ContextsfilternameField extends ListField
{
	/**
	 * The contextsfiltername field type.
	 *
	 * @var        string
	 */
	public $type = 'Contextsfiltername';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return  array    An array of Html options.
	 * @since   1.6
	 */
	protected function getOptions()
	{
		// Get a db connection.
		$db = Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select the text.
		$query->select($db->quoteName('name'));
		$query->from($db->quoteName('#__releasechecking_context'));
		$query->order($db->quoteName('name') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$_results = $db->loadColumn();
		$_filter = [];
		$_filter[] = Html::_('select.option', '', '- ' . Text::_('COM_RELEASECHECKING_FILTER_SELECT_NAME') . ' -');

		if ($_results)
		{
			$_results = array_unique($_results);
			foreach ($_results as $name)
			{
				// Now add the name and its text to the options array
				$_filter[] = Html::_('select.option', $name, $name);
			}
		}
		return $_filter;
	}
}
