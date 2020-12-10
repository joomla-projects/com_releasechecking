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

?>
<tr>
	<?php if ($this->canEdit&& $this->canState): ?>
		<th width="1%" class="nowrap center hidden-phone">
			<?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $this->listDirn, $this->listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
		</th>
		<th width="20" class="nowrap center">
			<?php echo JHtml::_('grid.checkall'); ?>
		</th>
	<?php else: ?>
		<th width="20" class="nowrap center hidden-phone">
			&#9662;
		</th>
		<th width="20" class="nowrap center">
			&#9632;
		</th>
	<?php endif; ?>
	<th class="nowrap" >
			<?php echo JHtml::_('searchtools.sort', 'COM_RELEASE_CHECKING_RELEASE_CHECKS_DETAILS', 'g.name', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo JHtml::_('searchtools.sort', 'COM_RELEASE_CHECKING_RELEASE_CHECK_ACTION_LABEL', 'h.name', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo JHtml::_('searchtools.sort', 'COM_RELEASE_CHECKING_RELEASE_CHECK_OUTCOME_LABEL', 'a.outcome', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo JHtml::_('searchtools.sort', 'COM_RELEASE_CHECKING_RELEASE_CHECK_JOOMLA_VERSION_LABEL', 'i.name', $this->listDirn, $this->listOrder); ?>
	</th>
	<?php if ($this->canState): ?>
		<th width="10" class="nowrap center" >
			<?php echo JHtml::_('searchtools.sort', 'COM_RELEASE_CHECKING_RELEASE_CHECK_STATUS', 'a.published', $this->listDirn, $this->listOrder); ?>
		</th>
	<?php else: ?>
		<th width="10" class="nowrap center" >
			<?php echo JText::_('COM_RELEASE_CHECKING_RELEASE_CHECK_STATUS'); ?>
		</th>
	<?php endif; ?>
	<th width="5" class="nowrap center hidden-phone" >
			<?php echo JHtml::_('searchtools.sort', 'COM_RELEASE_CHECKING_RELEASE_CHECK_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
	</th>
</tr>