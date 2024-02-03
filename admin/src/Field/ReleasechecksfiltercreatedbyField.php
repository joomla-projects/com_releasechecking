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
 * Releasechecksfiltercreatedby Form Field class for the Releasechecking component
 *
 * @since  1.6
 */
class ReleasechecksfiltercreatedbyField extends ListField
{
	/**
	 * The releasechecksfiltercreatedby field type.
	 *
	 * @var        string
	 */
	public $type = 'Releasechecksfiltercreatedby';

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
		$query->select($db->quoteName('created_by'));
		$query->from($db->quoteName('#__releasechecking_release_check'));
		$query->order($db->quoteName('created_by') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$_results = $db->loadColumn();
		$_filter = [];

		if ($_results)
		{
			$_results = array_unique($_results);
			foreach ($_results as $created_by)
			{
				// Now add the created_by and its text to the options array
				$_filter[] = Html::_('select.option', $created_by,
					Factory::getContainer()->
					get(\Joomla\CMS\User\UserFactoryInterface::class)->
					loadUserById($created_by)->name
					);
			}
		}
		return $_filter;
	}
}
