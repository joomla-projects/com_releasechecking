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



// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_release_checking'))
{
	throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
};

// Add CSS file for all pages
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_release_checking/assets/css/admin.css');
$document->addScript('components/com_release_checking/assets/js/admin.js');

// require helper files
JLoader::register('Release_checkingHelper', __DIR__ . '/helpers/release_checking.php');
JLoader::register('JHtmlBatch_', __DIR__ . '/helpers/html/batch_.php');

// Get an instance of the controller prefixed by Release_checking
$controller = JControllerLegacy::getInstance('Release_checking');

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
