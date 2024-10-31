(function(){
	var help_url_video = 'https://youtu.be/fx6mTPqev3c?hd=1';
	var help_url_docs = 'https://johnalarcon.com';
	tinymce.create('tinymce.plugins.reverberation', {
		createControl : function(id, controlManager) {
			if (id == 'reverberation_button') {
				var button = controlManager.createButton('reverberation_button', {
					title : 'Reverberation Shortcode',
				onclick : function() {
						var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
						W = W - 80;
						H = H - 84;
						tb_show('Reverberation Shortcode', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=reverberation-form');
					}
				});
				return button;
			}
			return null;
		}
	});
	tinymce.PluginManager.add('reverberation', tinymce.plugins.reverberation);
	jQuery(function(){
		var form = jQuery('<div id="reverberation-form">\
		<table id="reverberation-table" class="form-table">\
			<tr id="reverberation-row-type">\
				<th class="reverberation-th"><label for="reverberation-type">Type Of Widget</label></th>\
				<td class="reverberation-td">\
					<select id="reverberation-type" name="type" size="1" onchange="showHideRows(this.value);">\
						<option value="hideform" selected="selected">Choose:</option>\
						<option value="solosong">Play a particular song</option>\
						<option value="newmusic">Play the latest song by an artist</option>\
						<option value="allsongs">Play all songs by an artist</option>\
						<option value="playlist">Play a predefined playlist</option>\
						<option value="schedule">Display show schedule</option>\
						<option value="maillist">Collect email addresses</option>\
					</select>\
				</td>\
			</tr>\
			<tr id="reverberation-row-aid">\
				<th class="reverberation-th"><label for="reverberation-aid">Artist ID</label></th>\
				<td class="reverberation-td"><input id="reverberation-aid" type="text" name="aid" value="" /><br />\
				<small>The id of the ReverbNation artist. <a href="'+help_url_video+'" title="Video: Find the Artist ID" target="wcjcs_youtube">Need help?</a></small></td>\
			</tr>\
			<tr id="reverberation-row-sid">\
				<th class="reverberation-th"><label for="reverberation-sid">Song ID</label></th>\
				<td class="reverberation-td"><input id="reverberation-sid" type="text" name="sid" value="" /><br />\
				<small>The id of the song you want to include.</small> <a href="'+help_url_video+'" title="Video: Find the Song ID" target="wcjcs_youtube">Need help?</td>\
			</tr>\
			<tr id="reverberation-row-pid">\
				<th class="reverberation-th"><label for="reverberation-pid">Playlist ID</label></th>\
				<td class="reverberation-td"><input id="reverberation-pid" type="text" name="pid" value="" /><br />\
				<small>The id of the playlist you want to play.</small> <a href="'+help_url_video+'" title="Video: Find the Playlist ID" target="wcjcs_youtube">Need help?</td>\
			</tr>\
			<tr id="reverberation-row-photo">\
				<th class="reverberation-th"><label for="reverberation-photo">Photo</label></th>\
				<td class="reverberation-td"><select id="reverberation-photo" name="photo" size="1">\
					<option value="1">Show</option>\
					<option value="0" selected="selected">Hide</option>\
					</select> <small>Show or hide the artist photo.</small></td>\
			</tr>\
			<tr id="reverberation-row-w">\
				<th class="reverberation-th"><label for="reverberation-w">Width</label></th>\
				<td class="reverberation-td"><input id="reverberation-w" type="text" name="w" value="" /><br />\
				<small>The width of the widget.</small></td>\
			</tr>\
			<tr id="reverberation-row-h">\
				<th class="reverberation-th"><label for="reverberation-h">Height</label></th>\
				<td class="reverberation-td"><input id="reverberation-h" type="text" name="h" value="" /><br />\
				<small>The height of the widget.</small></td>\
			</tr>\
			<tr id="reverberation-row-fit">\
				<th class="reverberation-th"><label for="reverberation-fit">Stretch To Fit</label></th>\
				<td class="reverberation-td"><select id="reverberation-fit" name="fit" size="1">\
				<option value="1">Yes</option>\
				<option value="0" selected="selected">No</option>\
				</select> <small>Stretch widget width to fit; overrides width setting above.</small></td>\
			</tr>\
			<tr id="reverberation-row-bg">\
				<th class="reverberation-th"><label for="reverberation-bg">Background Color</label></th>\
				<td class="reverberation-td"><input id="reverberation-bg" type="text" name="bg" class="color{caps:false}" value="333333" /><br />\
				<small>Background color of the widget.</small></td>\
			</tr>\
			<tr id="reverberation-row-show-map">\
				<th class="reverberation-th"><label for="reverberation-show-map">Show Map</label></th>\
				<td class="reverberation-td"><select id="reverberation-show-map" name="show_map" size="1">\
					<option value="1">Yes</option>\
					<option value="0" selected="selected">No</option>\
					</select> <small>Display interactive map of shows.</small></td>\
			</tr>\
			<tr id="reverberation-row-layout">\
				<th class="reverberation-th"><label for="reverberation-layout">Layout</label></th>\
				<td class="reverberation-td"><select id="reverberation-layout" name="layout" size="1">\
					<option value="compact" selected="selected">Compact</option>\
					<option value="detailed">Detailed</option>\
					</select> <small>Display shows in compact or detailed mode.</small></td>\
			</tr>\
			<tr id="reverberation-row-posted-by">\
				<th class="reverberation-th"><label for="reverberation-posted-by">Posted By</label></th>\
				<td class="reverberation-td"><input id="reverberation-posted-by" type="text" name="posted_by" value="" size="" /><br />\
				<small>If you have your own ReverbNation id, include it here to aid in stats tracking.</small></td>\
			</tr>\
			<tr id="reverberation-row-documentation">\
				<th class="reverberation-th">&nbsp;</th>\
				<td class="reverberation-td"><a href="http://static.wcjcs.com/docs/wordpress/plugins/reverberation/" title="Read The Online Documentation" target="wcjcs_docs">Documentation</a></td>\
			<tr>\
			<tr id="reverberation-row-submit">\
				<th class="reverberation-th">&nbsp;</th>\
				<td class="reverberation-td"><p>&nbsp;</p><input type="button" id="reverberation-submit" class="button-primary" value="Insert Shortcode" name="submit" /></td>\
			<tr>\
		</table>\
		</div>');
		var table = form.find('table');
		form.appendTo('body').hide();
		form.find('#reverberation-row-submit').click(function(){
			var attr_defaults = {'type':'', 'aid':'', 'sid':'', 'pid':'', 'photo':0, 'fit':0, 'h':'', 'w':'', 'bg':'333333', 'show_map':'', 'layout':'compact', 'posted_by':false, };
			var widget_type   = table.find('#reverberation-type').val();
			var attr_keys = {
				'newmusic' : new Array('aid', 'photo', 'fit', 'w', 'bg', 'posted-by'),
				'solosong' : new Array('aid', 'sid', 'photo', 'fit', 'w', 'h', 'bg', 'posted-by'),
				'allsongs' : new Array('aid', 'photo', 'fit', 'w', 'h', 'bg', 'posted-by'),
				'playlist' : new Array('pid', 'photo', 'fit', 'w', 'h', 'bg', 'posted-by'),
				'schedule' : new Array('aid', 'fit', 'w', 'h', 'bg', 'show-map', 'layout', 'posted-by'),
				'maillist' : new Array('aid', 'fit', 'w', 'h', 'bg', 'posted-by'),
			}
			var stretch = 0;
			var shortcode = '[reverb';
			shortcode += ' ' + "type='" + widget_type + "'";
			for(var index in attr_keys[widget_type]) {
				var value = table.find('#reverberation-' + attr_keys[widget_type][index]).val();
				value = (attr_keys[widget_type][index]==='w') ? value.replace(/[^A-Za-z0-9%]/g, '') : value.replace(/[^A-Za-z0-9]/g, '');
				if (attr_keys[widget_type][index] == 'fit' && value == 1) { stretch=1; }
				if (stretch > 0 && attr_keys[widget_type][index] == 'w') { value='100%'; }
				if (value !== attr_defaults[attr_keys[widget_type][index]] && value != 0 && value !== undefined && value.length != 0) {
					shortcode += ' ' + attr_keys[widget_type][index].replace(/-/g, '_') + "='" + value + "'";
				}
			}
			shortcode += ']';
			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			tb_remove();
		});
	});
})()

function showHideRows(element)
{
	var attr_keys = {
		'hideform' : {'type':true,	'aid':false,'sid':false,'pid':false,	'photo':false,	'fit':false,'w':false,	'h':false,	'bg':false,	'show-map':false,	'layout':false,	'posted-by':false,	'submit':false, 'documentation':true},
		'newmusic' : {'type':true,	'aid':true,	'sid':false,'pid':false,	'photo':true,	'fit':true,	'w':true,	'h':false,	'bg':true,	'show-map':false,	'layout':false,	'posted-by':true,	'submit':true, 'documentation':true},
		'solosong' : {'type':true,	'aid':true,	'sid':true,	'pid':false,	'photo':true,	'fit':true,	'w':true,	'h':true,	'bg':true,	'show-map':false,	'layout':false,	'posted-by':true,	'submit':true, 'documentation':true},
		'allsongs' : {'type':true,	'aid':true,	'sid':false,'pid':false,	'photo':true,	'fit':true,	'w':true,	'h':true,	'bg':true,	'show-map':false,	'layout':false,	'posted-by':true,	'submit':true, 'documentation':true},
		'playlist' : {'type':true,	'aid':false,'sid':false,'pid':true,		'photo':true,	'fit':true,	'w':true,	'h':true,	'bg':true,	'show-map':false,	'layout':false,	'posted-by':true,	'submit':true, 'documentation':true},
		'schedule' : {'type':true,	'aid':true,	'sid':false,'pid':false,	'photo':false,	'fit':true,	'w':true,	'h':true,	'bg':true,	'show-map':true,	'layout':true,	'posted-by':true,	'submit':true, 'documentation':true},
		'maillist' : {'type':true,	'aid':true,	'sid':false,'pid':false,	'photo':false,	'fit':true,	'w':true,	'h':true,	'bg':true,	'show-map':false,	'layout':false,	'posted-by':true,	'submit':true, 'documentation':true},
	}
	for(var index in attr_keys[element]) {
		var value = attr_keys[element][index];
		if (!value) {
			document.getElementById('reverberation-row-'+index).style.display = 'none';
		} else {
			document.getElementById('reverberation-row-'+index).style.display = 'block';
		}
	}	
}

