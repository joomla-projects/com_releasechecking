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

JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', '.multipleAccessLevels', null, array('placeholder_text_multiple' => '- ' . JText::_('COM_RELEASE_CHECKING_FILTER_SELECT_ACCESS') . ' -'));
JHtml::_('formbehavior.chosen', 'select');
if ($this->saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_release_checking&task=contexts.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'contextList', 'adminForm', strtolower($this->listDirn), $saveOrderingUrl);
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_release_checking&view=contexts'); ?>" method="post" name="adminForm" id="adminForm">
<?php if(!empty( $this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif; ?>
<?php
	// Add the trash helper layout
	echo JLayoutHelper::render('trashhelper', $this);
	// Add the searchtools
	echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
?>
<?php if (empty($this->items)): ?>
	<div class="alert alert-no-items">
		<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	</div>
<?php else : ?>
	<table class="table table-striped" id="contextList">
		<thead><?php echo $this->loadTemplate('head');?></thead>
		<tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
		<tbody><?php echo $this->loadTemplate('body');?></tbody>
	</table>
	<?php // Load the batch processing form. ?>
	<?php if ($this->canCreate && $this->canEdit) : ?>
		<?php echo JHtml::_(
			'bootstrap.renderModal',
			'collapseModal',
			array(
				'title' => JText::_('COM_RELEASE_CHECKING_CONTEXTS_BATCH_OPTIONS'),
				'footer' => $this->loadTemplate('batch_footer')
			),
			$this->loadTemplate('batch_body')
		); ?>
	<?php endif; ?>
	<input type="hidden" name="boxchecked" value="0" />
	</div>
<?php endif; ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
