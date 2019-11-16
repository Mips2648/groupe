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


if (init('id') == '') {
    throw new Exception('{{L\'id de l\'opération ne peut etre vide : }}' . init('op_id'));
}

$id = init('id');

$groupe = groupe::byId($id);

if (!is_object($groupe)) { 
		  
 throw new Exception(__('Aucun equipement ne  correspond : Il faut (re)-enregistrer l\'équipement ', __FILE__) . init('action'));
 }
 

$active = $groupe->getConfiguration('activAction');

$name_off = $groupe->getConfiguration('nameOff');
if ($name_off == '') {
	$name_off = 'OFF';
	
}
$name_on =  $groupe->getConfiguration('nameOn');
if ($name_on == '') {
	$name_on = 'ON';
	
}

$all = $groupe->getCmd();
$cmds = array();
$i=0;
foreach ($all as $one) {
	if ($one->getlogicalId () == '') {
		$id = $one->getConfiguration('state');
		$cmd = cmd::byId(str_replace('#', '', $id));
		$state = $cmd->execCmd();
		log::add('groupe', 'debug', 'state :' . $state);
		log::add('groupe', 'debug', 'name :' . $cmd->getName());
		log::add('groupe', 'debug', 'reverse :' . $groupe->getConfiguration('reverse'));
		if($one->getConfiguration('reverse') == 1) {
			
			($state == 0) ? $state = 1 : $state = 0;
		}
		$cmds[$one->getName()] = array($state,str_replace('#', '', $one->getConfiguration('ON')),str_replace('#', '', $one->getConfiguration('OFF')),$active,$name_on,$name_off);
	}
}


sendVarToJS('infoGroupe', $cmds);


?>

<center>
<h3>Équipements <?php echo  $name_on ?> </h1>
<table border="0"  id='activeTable'> 

<thead>
	<tr>
		<th>{{Nom}}</th>
		<th>{{Commande ON}}</th>
	   	<th>{{Commande OFF}}</th>
		<th>{{Dernière communication}}</th>
	</tr>
	
</thead>	
	<tbody></tbody>
	
	
</table>	
	
</center>


<center>
<h3>Équipements   <?php echo  $name_off ?></h1>
<table border="0"  id='inactiveTable'> 
<thead>
	<tr>
		<th>{{Nom}}</th>
		<th >{{Commande ON}}</th>
	   	<th >{{Commande OFF}}</th>
		<th >{{Dernière communication}}</th>
	</tr>
	
</thead>	
	
	<tbody></tbody>
	
</table>
</center>




<br />



<style>
#inactiveTable td,  #activeTable td{
	height:40px;
	width:150px;
	padding:10px;
}

	#inactiveTable th,  #activeTable th {
		text-align: center;
		padding:10px;
	}
	

.on {
	background-color: green;
	border:none !important;
	opacity: 0.8;
	width:100px;
}
.off {
	background-color: red;
	border:none !important;
	opacity: 0.8;
	width:100px;
}
</style>

<?php include_file('desktop', 'modal', 'js', 'groupe');?>

<script>
readTable(infoGroupe);
</script>


