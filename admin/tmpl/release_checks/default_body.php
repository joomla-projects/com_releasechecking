<?php
/**
 * @package    Joomla.CMS
 * @maintainer Llewellyn van der Merwe <https://git.vdm.dev/Llewellyn>
 *
 * @created    29th July, 2020
 * @copyright  (C) 2020 Open Source Matters, Inc. <http://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use VDM\Component\Releasechecking\Administrator\Helper\ReleasecheckingHelper;

// No direct access to this file
defined('_JEXEC') or die;

$edit = "index.php?option=com_releasechecking&view=release_checks&task=release_check.edit";

?>
<?php foreach ($this->items as $i => $item): ?>
	<?php
		$canCheckin = $this->user->authorise('core.manage', 'com_checkin') || $item->checked_out == $this->user->id || $item->checked_out == 0;
		$userChkOut = Factory::getContainer()->
			get(\Joomla\CMS\User\UserFactoryInterface::class)->
				loadUserById($item->checked_out);
		$canDo = ReleasecheckingHelper::getActions('release_check',$item,'release_checks');
	?>
	<tr class="row<?php echo $i % 2; ?>">
		<td class="order nowrap center hidden-phone">
		<?php if ($canDo->get('core.edit.state')): ?>
			<?php
				$iconClass = '';
				if (!$this->saveOrder)
				{
					$iconClass = ' inactive tip-top" hasTooltip" title="' . Html::tooltipText('JORDERINGDISABLED');
				}
			?>
			<span class="sortable-handler<?php echo $iconClass; ?>">
				<i class="icon-menu"></i>
			</span>
			<?php if ($this->saveOrder) : ?>
				<input type="text" style="display:none" name="order[]" size="5"
				value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
			<?php endif; ?>
		<?php else: ?>
			&#8942;
		<?php endif; ?>
		</td>
		<td class="nowrap center">
		<?php if ($canDo->get('core.edit')): ?>
				<?php if ($item->checked_out) : ?>
					<?php if ($canCheckin) : ?>
						<?php echo Html::_('grid.id', $i, $item->id); ?>
					<?php else: ?>
						&#9633;
					<?php endif; ?>
				<?php else: ?>
					<?php echo Html::_('grid.id', $i, $item->id); ?>
				<?php endif; ?>
		<?php else: ?>
			&#9633;
		<?php endif; ?>
		</td>
		<td class="nowrap">
			<div>
			<?php if ($canDo->get('core.edit')): ?>
				<a href="<?php echo $edit; ?>&id=<?php echo $item->id; ?>"><?php echo $this->escape($item->context_name); ?></a>
				<?php if ($item->checked_out): ?>
					<?php echo Html::_('jgrid.checkedout', $i, $userChkOut->name, $item->checked_out_time, 'release_checks.', $canCheckin); ?>
				<?php endif; ?>
			<?php else: ?>
				<?php echo $this->escape($item->context_name); ?>
			<?php endif; ?>
<br /><small>
<?php echo Text::_('COM_RELEASECHECKING_CHECKED_BY'); ?>: 

			<?php if ($this->user->authorise('core.edit', 'com_users')): ?>
				<a href="index.php?option=com_users&task=user.edit&id=<?php echo (int) $item->created_by ?>"><?php echo Factory::getContainer()->get(\Joomla\CMS\User\UserFactoryInterface::class)->loadUserById((int) $item->created_by)->name; ?></a>
			<?php else: ?>
				<?php echo Factory::getContainer()->get(\Joomla\CMS\User\UserFactoryInterface::class)->loadUserById((int) $item->created_by)->name; ?>
			<?php endif; ?>
</small>
			</div>
		</td>
		<td class="hidden-phone">
			<?php echo $this->escape($item->action_name); ?>
		</td>
		<td class="hidden-phone">
			<div><span class="outcome <?php echo $item->outcome_style; ?>">

			<?php echo Text::_($item->outcome); ?>
</span>
			</div>
		</td>
		<td class="hidden-phone">
			<?php echo $this->escape($item->joomla_version_name); ?>
		</td>
		<td class="center">
		<?php if ($canDo->get('core.edit.state')) : ?>
				<?php if ($item->checked_out) : ?>
					<?php if ($canCheckin) : ?>
						<?php echo Html::_('jgrid.published', $item->published, $i, 'release_checks.', true, 'cb'); ?>
					<?php else: ?>
						<?php echo Html::_('jgrid.published', $item->published, $i, 'release_checks.', false, 'cb'); ?>
					<?php endif; ?>
				<?php else: ?>
					<?php echo Html::_('jgrid.published', $item->published, $i, 'release_checks.', true, 'cb'); ?>
				<?php endif; ?>
		<?php else: ?>
			<?php echo Html::_('jgrid.published', $item->published, $i, 'release_checks.', false, 'cb'); ?>
		<?php endif; ?>
		</td>
		<td class="nowrap center hidden-phone">
			<?php echo $item->id; ?>
		</td>
	</tr>
<?php endforeach; ?>