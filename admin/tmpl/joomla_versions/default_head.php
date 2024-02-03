<?php
/**
 * @package    Joomla.CMS
 * @maintainer Llewellyn van der Merwe <https://git.vdm.dev/Llewellyn>
 *
 * @created    29th July, 2020
 * @copyright  (C) 2020 Open Source Matters, Inc. <http://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;

// No direct access to this file
defined('_JEXEC') or die;

?>
<tr>
	<?php if ($this->canEdit&& $this->canState): ?>
		<th width="1%" class="nowrap center hidden-phone">
			<?php echo Html::_('searchtools.sort', '', 'a.ordering', $this->listDirn, $this->listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
		</th>
		<th width="20" class="nowrap center">
			<?php echo Html::_('grid.checkall'); ?>
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
			<?php echo Html::_('searchtools.sort', 'COM_RELEASECHECKING_JOOMLA_VERSION_NAME_LABEL', 'a.name', $this->listDirn, $this->listOrder); ?>
	</th>
	<?php if ($this->canState): ?>
		<th width="10" class="nowrap center" >
			<?php echo Html::_('searchtools.sort', 'COM_RELEASECHECKING_JOOMLA_VERSION_STATUS', 'a.published', $this->listDirn, $this->listOrder); ?>
		</th>
	<?php else: ?>
		<th width="10" class="nowrap center" >
			<?php echo Text::_('COM_RELEASECHECKING_JOOMLA_VERSION_STATUS'); ?>
		</th>
	<?php endif; ?>
	<th width="5" class="nowrap center hidden-phone" >
			<?php echo Html::_('searchtools.sort', 'COM_RELEASECHECKING_JOOMLA_VERSION_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
	</th>
</tr>