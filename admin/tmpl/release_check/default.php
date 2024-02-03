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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('keepalive')->useScript('form.validate');
Html::_('bootstrap.tooltip');
use VDM\Component\Releasechecking\Administrator\Helper\ReleasecheckingHelper;

// No direct access to this file
defined('_JEXEC') or die;

?>
<script type="text/javascript">
	// waiting spinner
	var outerDiv = document.querySelector('body');
	var loadingDiv = document.createElement('div');
	loadingDiv.id = 'loading';
	loadingDiv.style.cssText = "background: rgba(255, 255, 255, .8) url('components/com_releasechecking/assets/images/import.gif') 50% 15% no-repeat; top: " + (outerDiv.getBoundingClientRect().top + window.pageYOffset) + "px; left: " + (outerDiv.getBoundingClientRect().left + window.pageXOffset) + "px; width: " + outerDiv.offsetWidth + "px; height: " + outerDiv.offsetHeight + "px; position: fixed; opacity: 0.80; -ms-filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=80); filter: alpha(opacity=80); display: none;";
	outerDiv.appendChild(loadingDiv);
	loadingDiv.style.display = 'block';
	// when page is ready remove and show
	window.addEventListener('load', function() {
		var componentLoader = document.getElementById('releasechecking_loader');
		if (componentLoader) componentLoader.style.display = 'block';
		loadingDiv.style.display = 'none';
	});
</script>
<div id="releasechecking_loader" style="display: none;">
<form action="<?php echo Route::_('index.php?option=com_releasechecking&layout=edit&id='. (int) $this->item->id . $this->referral); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">

	<?php echo LayoutHelper::render('release_check.checking_above', $this); ?>
<div class="main-card">

	<?php echo Html::_('uitab.startTabSet', 'release_checkTab', array('active' => 'checking')); ?>

	<?php echo Html::_('uitab.addTab', 'release_checkTab', 'checking', Text::_('COM_RELEASECHECKING_RELEASE_CHECK_CHECKING', true)); ?>
		<div class="row">
			<div class="col-md-6">
				<?php echo LayoutHelper::render('release_check.checking_left', $this); ?>
			</div>
			<div class="col-md-6">
				<?php echo LayoutHelper::render('release_check.checking_right', $this); ?>
			</div>
		</div>
	<?php echo Html::_('uitab.endTab'); ?>

	<?php $this->ignore_fieldsets = array('details','metadata','vdmmetadata','accesscontrol'); ?>
	<?php $this->tab_name = 'release_checkTab'; ?>
	<?php echo LayoutHelper::render('joomla.edit.params', $this); ?>

	<?php if ($this->canDo->get('release_check.edit.created_by') || $this->canDo->get('core.edit.created') || $this->canDo->get('core.edit.state') || ($this->canDo->get('core.delete') && $this->canDo->get('core.edit.state'))) : ?>
	<?php echo Html::_('uitab.addTab', 'release_checkTab', 'publishing', Text::_('COM_RELEASECHECKING_RELEASE_CHECK_PUBLISHING', true)); ?>
		<div class="row">
			<div class="col-md-6">
				<?php echo LayoutHelper::render('release_check.publishing', $this); ?>
			</div>
			<div class="col-md-6">
				<?php echo LayoutHelper::render('release_check.publlshing', $this); ?>
			</div>
		</div>
	<?php echo Html::_('uitab.endTab'); ?>
	<?php endif; ?>

	<?php if ($this->canDo->get('core.admin')) : ?>
	<?php echo Html::_('uitab.addTab', 'release_checkTab', 'permissions', Text::_('COM_RELEASECHECKING_RELEASE_CHECK_PERMISSION', true)); ?>
		<div class="row">
			<div class="col-md-12">
				<fieldset class="adminform">
					<div class="adminformlist">
					<?php foreach ($this->form->getFieldset('accesscontrol') as $field): ?>
						<div>
							<?php echo $field->label; echo $field->input;?>
						</div>
						<div class="clearfix"></div>
					<?php endforeach; ?>
					</div>
				</fieldset>
			</div>
		</div>
	<?php echo Html::_('uitab.endTab'); ?>
	<?php endif; ?>

	<?php echo Html::_('uitab.endTabSet'); ?>

	<div>
		<input type="hidden" name="task" value="release_check.edit" />
		<?php echo Html::_('form.token'); ?>
	</div>
</div>
</form>
</div>

<script type="text/javascript">




<?php
	$app = Factory::getApplication();
?>
function JRouter(link) {
<?php
	if ($app->isClient('site'))
	{
		echo 'var url = "'. \Joomla\CMS\Uri\Uri::root() . '";';
	}
	else
	{
		echo 'var url = "";';
	}
?>
	return url+link;
}
</script>
