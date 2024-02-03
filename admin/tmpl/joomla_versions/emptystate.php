<?php
/**
 * @package    Joomla.CMS
 * @maintainer Llewellyn van der Merwe <https://git.vdm.dev/Llewellyn>
 *
 * @created    29th July, 2020
 * @copyright  (C) 2020 Open Source Matters, Inc. <http://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Layout\LayoutHelper;

// No direct access to this file
defined('_JEXEC') or die;

$displayData = [
	'textPrefix' => 'COM_RELEASECHECKING_JOOMLA_VERSIONS',
	'formURL'    => 'index.php?option=com_releasechecking&view=joomla_versions',
	'icon'       => 'icon-joomla',
];

if ($this->user->authorise('joomla_version.create', 'com_releasechecking'))
{
	$displayData['createURL'] = 'index.php?option=com_releasechecking&task=joomla_version.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);
