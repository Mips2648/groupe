
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */
 

$("body").delegate(".listCmdInfo", 'click', function() {
	var type = $(this).attr('data-type');	
	var el = $(this).closest('.' + type).find('.cmdAttr[data-l1key=configuration][data-l2key=state]');
    jeedom.cmd.getSelectModal({cmd: {type: 'info', subtype: 'binary'}}, function(result) {
        el.value(result.human);
    });
});

$("body").delegate(".listCmdActionOff", 'click', function() {
    var type = $(this).attr('data-type');
    var el = $(this).closest('.' + type).find('.cmdAttr[data-l1key=configuration][data-l2key=OFF]');
    jeedom.cmd.getSelectModal({cmd: {type: 'action'}}, function(result) {
        el.value(result.human);
    });
});

$("body").delegate(".listCmdActionOn", 'click', function() {
    var type = $(this).attr('data-type');
    var el = $(this).closest('.' + type).find('.cmdAttr[data-l1key=configuration][data-l2key=ON]');
    jeedom.cmd.getSelectModal({cmd: {type: 'action'}}, function(result) {
        el.value(result.human);
    });
});



$('.cmdAction[data-action=addCmd]').on('click', function () {
	var _cmd = "";
    addCmdToTable(_cmd);
    $('.cmd:last .cmdAttr[data-l1key=type]').trigger('change');
});


 $('body').undelegate('.icone .iconeOn[data-l1key=chooseIcon]', 'click').delegate('.icone .iconeOn[data-l1key=chooseIcon]', 'click', function () {
    var mode = $(this).closest('.icone');
    chooseIcon(function (_icon) {
        mode.find('.iconeAttrOn[data-l2key=iconOn]').empty().append(_icon);
    });
});

 $('body').undelegate('.icone .iconeAttrOn[data-l2key=iconOn]', 'click').delegate('.icone .iconeAttrOn[data-l2key=iconOn]', 'click', function () {
    $(this).empty();
});

 $('body').undelegate('.icone .iconeOff[data-l1key=chooseIcon]', 'click').delegate('.icone .iconeOff[data-l1key=chooseIcon]', 'click', function () {
    var mode = $(this).closest('.icone');
    chooseIcon(function (_icon) {
        mode.find('.iconeAttrOff[data-l2key=iconOff]').empty().append(_icon);
    });
});

 $('body').undelegate('.icon .iconeAttrOff[data-l2key=iconOff]', 'click').delegate('.icone .iconeAttrOff[data-l2key=iconOff]', 'click', function () {
    $(this).empty();
});

function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }
				if (_cmd.name == 'Nombre On' || _cmd.name == 'Nombre Off' || _cmd.name == 'Etat' ) {
					var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '"  >';
					tr += '<td><input class="cmdAttr form-control" data-l1key="id" style="display : none;"><input class="cmdAttr form-control" data-l1key="name" style="width : 200px;margin-left:auto;margin-right:auto;" disabled /></td>';
					tr += '<span class="type" type="info" style="display : none;">' + jeedom.cmd.availableType() + '</span>';
					tr += '<span class="subType" subType="' + init(_cmd.subType) + '" style="display : none;"></span>';
					tr += '</td>';
					tr += '<td>';
					if (is_numeric(_cmd.id)) {
						tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> ';
						tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss pull-right"></i> {{Tester}}</a>';
					}
					tr += '</td>';					
					tr += '</tr>';	
					$('#table_info tbody').append(tr);
					$('#table_info tbody tr:last').setValues(_cmd, '.cmdAttr');
					if (isset(_cmd.type)) {
						$('#table_info tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
					}
					jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
				} else {
					if (!isset(_cmd.subType)) {
						_cmd.subType = "";
					}
					
					
					var tr = '<tr class="cmd ' + _cmd.type + '" data-cmd_id="' + init(_cmd.id) + '">';
					tr += '<td>';
					tr += '<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none;">';
					tr += '<span class="type" type="info" style="display : none;">' + jeedom.cmd.availableType() + '</span>';
					tr += '<input class="cmdAttr form-control" type="hidden" data-l1key="subType" value="groupe">';
					tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" 	 placeholder="{{Nom}}">';
					tr += '</td><td class="action" style="display: none;">';
					tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-type="' + _cmd.type + '" data-l2key="ON"  style="margin-bottom : 5px;width : 80%; display : inline-block;" disabled>';
					tr += '<a class="btn btn-default btn-sm cursor listCmdActionOn" data-type="' + _cmd.type + '" data-input="ON" style="margin-left : 5px;"><i class="fa fa-list-alt "></i></a>';
					tr += '<a class="iconeOn btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fa fa-flag"></i> {{Icône}}</a>';
					tr += ' <span class="cmdAttr iconeAttrOn label label-info cursor"  data-l1key="configuration" data-l2key="iconOn"  style="font-size : 1em;" ></span>';
					tr += '</td><td class="action" style="display: none;">';
					tr += ' <input class="cmdAttr form-control input-sm" data-l1key="configuration" data-type="' + _cmd.type + '" data-l2key="OFF"  style="margin-bottom : 5px;width : 80%; display : inline-block;" disabled>';
					tr += '<a class="btn btn-default btn-sm cursor listCmdActionOff" data-type="' + _cmd.type + '" data-input="OFF" style="margin-left : 5px;"><i class="fa fa-list-alt "></i></a>';
					tr += '<a class="iconeOff btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fa fa-flag"></i> {{Icône}}</a>';
					tr += ' <span class="cmdAttr iconeAttrOff label label-info cursor"  data-l1key="configuration" data-l2key="iconOff"  style="font-size : 1em;" ></span>';
					tr += '</td><td>';
					tr += ' <input class="cmdAttr form-control input-sm"  data-type="' + _cmd.type + '" data-l1key="configuration" data-l2key="state"  style="margin-bottom : 5px;width : 80%; display : inline-block;" disabled>';
					tr += ' <a class="btn btn-default btn-sm cursor listCmdInfo" data-type="' + _cmd.type + '"  style="margin-left : 5px;"><i class="fa fa-list-alt "></i></a>';
					tr += '</td><td>';
					tr += '<input type="checkbox" class="tooltips cmdAttr" data-l1key="configuration" data-l2key="reverse">';
					tr += '</td><td>';
					tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>';
					tr += '</td>';
					tr += '</tr>';
					$('#table_cmd tbody').append(tr);
					$('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
					if (isset(_cmd.type)) {
						$('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
					}
				}
			
	
}
