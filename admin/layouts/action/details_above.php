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
defined('_JEXEC') or die;

// get the form
$form = $displayData->getForm();

// get the layout fields override method name (from layout path/ID)
$layout_path_array = explode('.', $this->getLayoutId());
// Since we cannot pass the layout and tab names as parameters to the model method
// this name combination of tab and layout in the method name is the only work around
// seeing that JCB uses those two values (tab_name & layout_name) as the layout file name.
// example of layout name: details_left.php
// example of method name: getFields_details_left()
$fields_tab_layout = 'fields_' . $layout_path_array[1];

// get the fields
$fields = $displayData->get($fields_tab_layout) ?: [
	'name',
	'alias'
];

// Ensure $fields is treated as an array and count its size.
$size = count((array) $fields);

// Use a ternary operator to determine the class.
// If there are 1 to 4 fields, set the class to divide the 12-grid column equally among the fields.
// For more than 4 fields, default to four columns (3-grid each) for medium and larger screens.
$css_class = ($size > 0 && $size <= 4) ? 'col-12 col-md-' . round(12 / $size) : 'col-12 col-md-3';

$hiddenFields = $displayData->get('hidden_fields') ?: [];

?>
<?php if ($fields && count((array) $fields)) :?>
<div class="row title-alias form-vertical mb-3">
	<?php foreach($fields as $field): ?>
		<?php if (in_array($field, $hiddenFields)) : ?>
			<?php $form->setFieldAttribute($field, 'type', 'hidden'); ?>
		<?php endif; ?>
		<?php echo $form->renderField($field, null, null, ['class' => $css_class . ' control-wrapper-' . $field]); ?>
	<?php endforeach; ?>
</div>
<?php endif; ?>
