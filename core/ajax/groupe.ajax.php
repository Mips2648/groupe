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

try {
    require_once __DIR__ . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (init('action') == 'launchAction') {
        groupe::launchCmd(init('id'));
		ajax::success();
    }
    if (init('action') == 'getStatus') {
        $groupe = groupe::byId(init('id'));
		$return = $groupe->getConfiguration('activAction');
		ajax::success($return);
    }
	
    if (init('action') == 'actionAll') {
        groupe::actionAll(init('id'));
		ajax::success();
    }
    if (init('action') == 'getCmdEq') {
        $return = groupe::getCmdEq(init('id'));
		ajax::success($return);
    }
    if (init('action') == 'execCmdEq') {
		$cmdEq= cmd::byId(init('cmdId'));
		$groupe = $cmdEq->getEqLogic();
		$cmdAction = cmd::byId(init('id'));
		if (!is_object($groupe) || !is_object($cmdEq) || !is_object($cmdAction)) { 
		 throw new Exception(__('Aucun equipement ne  correspond ou problème avec une commande: Il faut vérifier l\'équipement ou effacer une commande ', __FILE__) . init('action'));
		 }
		$cmdAction->execCmd();
		$active = $groupe->getConfiguration('activAction');
		$name_off = $groupe->getConfiguration('nameOff','OFF');
		$name_on =  $groupe->getConfiguration('nameOn','ON');
		$all = $groupe->getCmd();
		$cmds = array();
		$i=0;
		foreach ($all as $one) {
			if ($one->getlogicalId () == '') {
				$id = $one->getConfiguration('state');
				$cmd = cmd::byId(str_replace('#', '', $id));
				if ($cmdEq->getId() == $one->getId()) {
					if(init('id') == str_replace('#', '', $one->getConfiguration('ON'))) {
						$state =1;
						log::add('groupe', 'debug', 'Commande ON :' . $cmdEq->getName() );
					} else {
						log::add('groupe', 'debug', 'Commande OFF :' . $cmdEq->getName());
						$state =0;
					}
					$last_seen =  date('Y-m-d H:i:s');
				} else {
					$state = $cmd->execCmd();
					$last_seen =  $cmd->getCollectDate();					
				}
				if($one->getConfiguration('reverse') == 1) {
					($state == 0) ? $state = 1 : $state = 0;
				}
				array_push($cmds,array($state,str_replace('#', '', $one->getConfiguration('ON')),str_replace('#', '', $one->getConfiguration('OFF')),$active,$name_on,$name_off,$last_seen,$one->getID(),$one->getName()));
			}
		}
		usort($cmds, array('groupe','compareCmds'));
		ajax::success($cmds);
	}
	
			
    throw new Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>