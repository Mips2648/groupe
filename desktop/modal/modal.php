<?php

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


if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

//include_file('desktop', 'panel', 'css', 'comptes'); 


if (init('id') == '') {
    throw new Exception('{{L\'id de l\'opération ne peut etre vide : }}' . init('op_id'));
}

$id = init('id');
$groupe = groupe::byId($id);
	if (!is_object($groupe)) { 
			  
	 throw new Exception(__('Aucun equipement ne  correspond : Il faut (re)-enregistrer l\'équipement ', __FILE__) . init('action'));
	 }
//$cmd->execCmd()
$all = $groupe->getCmd();
$cmds = array();
$i=0;
foreach ($all as $one) {
	if ($one->getlogicalId () == '') {
		$id = $one->getConfiguration('state');
		$cmd = cmd::byId(str_replace('#', '', $id));
		$state = $cmd->execCmd();
		$cmds[$one->getName()] = array($state,str_replace('#', '', $one->getConfiguration('ON')),str_replace('#', '', $one->getConfiguration('OFF')));

	}
}


sendVarToJS('infoGroupe', $cmds);

?>

<h3>Équipements actifs</h1>
<table border="0"  id='activeTable'> </table>
<h3>Équipements  inactifs</h1>
<table border="0"  id='inactiveTable'> </table>


<script>
//console.log(Object.keys(infoGroupe.configuration.cmd_off).length);
//var arr = $.map(infoGroupe.configuration.cmd_off, function(el) { return el });

readTable(infoGroupe);

function readTable(infoGroupe) {
	$("#activeTable").html('');
	$("#inactiveTable").html('');
	var html = "";
	var html1 = "";
	for (var id in infoGroupe) {
		if (infoGroupe[id][0] == 0) {
			html += "<tr><td> " + id + "</td><td><button type='button' name=" + id + " class='on' value='" + infoGroupe[id][1] +  "'>ON</button></td><td><button type='button' class='off' name=" + id + " value='" + infoGroupe[id][2] +  "'>OFF</button></td></tr>";
		} else {
			html1 += "<tr><td> " + id + "</td><td><button type='button' name=" + id + " class='on' value='" + infoGroupe[id][1] +  "'>ON</button></td><td><button type='button' class='off' name=" + id + " value='" + infoGroupe[id][2] +  "'>OFF</button></td></tr>";
		}
		
	}
	$("#activeTable").html(html1);
	$("#inactiveTable").html(html);
	$('table button').on('click', function() {
		if($(this).attr("class") == 'on') {
			infoGroupe[this.name][0] = '1'
		} else {
			infoGroupe[this.name][0] = '0'
		}
		readTable(infoGroupe);
		jeedom.cmd.execute({id: this.value});
			
	});
}



</script>

<style>
td {
	height:40px;
	width:60px;
}

.on {
	background-color: green;
	border:none !important;
	opacity: 0.8;
}
.off {
	background-color: red;
	border:none !important;
	opacity: 0.8;
}
</style>




