/**
 * @package    Joomla.CMS
 * @subpackage com_release_checking
 *
 * @copyright  (C) 2020 Open Source Matters, Inc. <http://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */




// set actions that are on the page
actions = {};
var action = 0;
jQuery(document).ready(function($)
{
	// when it changes
	$('#adminForm').on('change', '#jform_context',function (e) {
		e.preventDefault();
		getAction();
	});
	// should the version change we should check again
	$('#adminForm').on('change', '#jform_joomla_version',function (e) {
		e.preventDefault();
		getAction();
	});
	// when it changes
	$('#adminForm').on('change', '#jform_action',function (e) {
		e.preventDefault();
		getActionDescription();
	});
	// set buckets
	jQuery("#jform_action option").each(function()
	{
		let key =  jQuery(this).val();
		let text =  jQuery(this).text();
		actions[key] = text;
	});
	action = jQuery('#jform_action').val();
	// on load we also update the selection
	getAction();
});

function getAction_server(context, joomla_version, current_id){
	var getUrl = JRouter("index.php?option=com_release_checking&task=ajax.getAction&raw=true&format=json");
	if(token.length > 0 && context > 0 && joomla_version > 0){
		var request = token+'=1&context='+context+'&joomla_version='+joomla_version+'&current_id='+current_id;
	}
	return jQuery.ajax({
		type: 'GET',
		url: getUrl,
		dataType: 'json',
		data: request,
		jsonp: false
	});
}
function getAction(){
	jQuery("#loading").show();
	// get country value if set
	var current_id = jQuery('#jform_id').val();
	var context = jQuery('#jform_context').val();
	var joomla_version = jQuery('#jform_joomla_version').val();
	// clear the selection
	jQuery('#jform_action').find('option').remove().end();
	jQuery('#jform_action').trigger('liszt:updated');
	// make sure we have a value selected
	if (context > 0 && joomla_version > 0) {
		getAction_server(context, joomla_version, current_id).done(function(result) {
			setAction(result);
			jQuery("#loading").hide();
			if (typeof actionButton !== 'undefined') {
				// ensure button is correct
				var action = jQuery('#jform_action').val();
				actionButton(action);
			}
		});
    } else {
		// we do not have a value so we remove the spinner
		jQuery("#loading").hide();
		// if version is not selected give notice to select the version
		if (context > 0 && joomla_version == 0) {
			alert(Joomla.JText._('COM_RELEASE_CHECKING_YOU_MUST_FIRST_SELECT_THE_JOOMLA_VERSION_BEING_TESTED'));
			jQuery('#jform_action').val(0);
			jQuery('#jform_action').trigger('liszt:updated');
			jQuery('#jform_context').val(0);
			jQuery('#jform_context').trigger('liszt:updated');
		}
    }
}
function setAction(array){
	if (array.ids && array.ids.length > 0) {
		jQuery('#jform_action').append('<option value="">' + Joomla.JText._('COM_RELEASE_CHECKING_SELECT') + '...</option>');
		jQuery.each(array.ids, function( i, id ) {
			if (id in actions) {
				jQuery('#jform_action').append('<option value="'+id+'">' + actions[id] + '</option>');
			}
			if (id == action) {
				jQuery('#jform_action').val(id);
			}
		});
	} else {
		if (array.removed_ids && array.removed_ids.length > 0){
			// this will only trigger if user has already tested all action in this context.
			jQuery('#jform_action').append('<option value="">' + Joomla.JText._('COM_RELEASE_CHECKING_ALL_DONE_HERE_SELECT_THE_NEXT_CONTEXT') + '...</option>');
		} else {
			// this will only trigger if this context has not actions set
			jQuery('#jform_action').append('<option value="">' + Joomla.JText._('COM_RELEASE_CHECKING_CREATE') + '...</option>');
		}
	}
	jQuery('#jform_action').trigger('liszt:updated');
}

function getActionDescription_server(action){
	var getUrl = JRouter("index.php?option=com_release_checking&task=ajax.getActionDescription&raw=true&format=json");
	if(token.length > 0 && action > 0){
		var request = token+'=1&action='+action;
	}
	return jQuery.ajax({
		type: 'GET',
		url: getUrl,
		dataType: 'json',
		data: request,
		jsonp: false
	});
}
function getActionDescription(){
	jQuery("#loading").show();
	// get country value if set
	var action = jQuery('#jform_action').val();
	// make sure we have a value selected
	if (action > 0) {
		getActionDescription_server(action).done(function(result) {
			setActionDescription(result);
			jQuery("#loading").hide();
		});
    } else {
		// we do not have a value so we remove the spinner
		jQuery("#loading").hide();
    }
}
function setActionDescription(desc){
	// we can load the action description to the page
	if (desc) {
		console.log(desc);
	} else {
		console.log(desc);
	}
} 
