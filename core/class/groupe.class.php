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

/* * ***************************Includes********************************* */
require_once __DIR__ . '/../../../../core/php/core.inc.php';

class groupe extends eqLogic {
	
	public static $_widgetPossibility = array('custom' => true);

    public static function pull($_option) {
		$groupe = groupe::byId($_option['groupe_id']);
		if (is_object($groupe) && $groupe->getIsEnable() == 1) {
			$groupe->execute($_option['event_id'], $_option['value']);
        }
    }

    public function launch($_trigger_id, $_value) {
        return true;
    }

	 public function preSave() {
		if($this->getConfiguration('activAction') == 1) { 
			$allon = $this->getCmd(null, 'allon');
			if (!is_object($allon)) {
				$allon = new groupeCmd();
				$allon->setName(__('All on', __FILE__));
								
			}
			$allon->setLogicalId('allon');
			$allon->setEqLogic_id($this->getId());
			$allon->setType('action');
			$allon->setSubType('other');
			$allon->save(); 
			
			$alloff = $this->getCmd(null, 'alloff');
			if (!is_object($alloff)) {
				$alloff = new groupeCmd();
				$alloff->setName(__('All off', __FILE__));
								
			}
			$alloff->setLogicalId('alloff');
			$alloff->setEqLogic_id($this->getId());
			$alloff->setType('action');
			$alloff->setSubType('other');
			$alloff->save(); 
		} else {
			$allon = $this->getCmd(null, 'allon');
			if (is_object($allon)) {
				$allon->remove();
			}
			$alloff = $this->getCmd(null, 'alloff');
			if (is_object($alloff)) {
				$alloff->remove();
			}							
		}
	 }


    public function postUpdate() {
		$statusOn = $this->getCmd(null, 'statuson');
		if (!is_object($statusOn)) {
			$statusOn = new groupeCmd();
			$statusOn->setName(__('Nombre On', __FILE__));
							
		}
		
		$statusOn->setLogicalId('statuson');
		$statusOn->setEqLogic_id($this->getId());
		$statusOn->setType('info');
		$statusOn->setSubType('numeric');
		$statusOn->save(); 			
		
		$statusOff = $this->getCmd(null, 'statusoff');
		if (!is_object($statusOff)) {
			$statusOff = new groupeCmd();
			$statusOff->setName(__('Nombre Off', __FILE__));					
		}
		$statusOff->setLogicalId('statusoff');
		$statusOff->setEqLogic_id($this->getId());
		$statusOff->setType('info');
		$statusOff->setSubType('numeric');
		$statusOff->save(); 
		
		$status = $this->getCmd(null, 'status');
		if (!is_object($status)) {
			$status = new groupeCmd();
			$status->setName(__('Etat', __FILE__));
							
		}
		$status->setLogicalId('status');
		$status->setEqLogic_id($this->getId());
		$status->setType('info');
		$status->setSubType('binary');
		$status->save(); 

		$status = $this->getCmd(null, 'last');
		if (!is_object($status)) {
			$status = new groupeCmd();
			$status->setName(__('Dernier déclencheur', __FILE__));
							
		}
		$status->setLogicalId('last');
		$status->setEqLogic_id($this->getId());
		$status->setType('info');
		$status->setSubType('string');
		$status->save();

		if ($this->getIsEnable() == 1) {
			$listener = listener::byClassAndFunction('groupe', 'pull', array('groupe_id' => intval($this->getId())));
			if (!is_object($listener)) {
				$listener = new listener();
			}
			$listener->setClass('groupe');
			$listener->setFunction('pull');
			$listener->setOption(array('groupe_id' => intval($this->getId())));
			$listener->emptyEvent();
			$etats = $this->getConfiguration('etat');
			foreach ($etats as $etat) {
					$cmd = cmd::byId(str_replace('#', '', $etat));
					if (!is_object($cmd)) {
						throw new Exception(__('Commande déclencheur inconnue : ' . $etat, __FILE__));
					}
					$listener->addEvent($etat);
			}
			$listener->save();
			$this->get_info();
		} else {
			$listener = listener::byClassAndFunction('groupe', 'pull', array('groupe_id' => intval($this->getId())));
			if (is_object($listener)) {
				$listener->remove();			
			}
		}
    }

	public function preRemove() {
		$listener = listener::byClassAndFunction('groupe', 'pull', array('groupe_id' => intval($this->getId())));
		if (is_object($listener)) {
			$listener->remove();
		}
	}
	
	public function getState($i,$j,$etat,$name) {
		$changed = false;
		$changed = $this->checkAndUpdateCmd('statuson', $i) || $changed;
		$changed = $this->checkAndUpdateCmd('statusoff', $j) || $changed;
		$changed = $this->checkAndUpdateCmd('status', $etat) || $changed;
		if ($changed) {
			$this->refreshWidget();
		}			
	}
	
	public function getCmdEq($_id){
		$groupe = groupe::byId($_id);

		if (!is_object($groupe)) { 

		 throw new Exception(__('Aucun equipement ne  correspond : Il faut (re)-enregistrer l\'équipement ', __FILE__) . init('action'));
		 }


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
				$state = $cmd->execCmd();
				$last_seen =  $cmd->getValueDate();
				$status = $groupe->getCmd(null, 'last');
				if (is_object($status)) {
					$last = $status->execCmd();

				}				
				
				log::add('groupe', 'debug', 'state :' . $state);
				log::add('groupe', 'debug', 'name :' . $cmd->getName());
				log::add('groupe', 'debug', 'reverse :' . $groupe->getConfiguration('reverse'));
				if($one->getConfiguration('reverse') == 1) {

					($state == 0) ? $state = 1 : $state = 0;
				}
				
				$cmds[$one->getName()] = array($state,str_replace('#', '', $one->getConfiguration('ON')),str_replace('#', '', $one->getConfiguration('OFF')),$active,$name_on,$name_off,$last_seen,$one->getID());
			}
		}		
		return $cmds;
	}
	
	public function actionAll($_id, $_state=false){
		$groupe = groupe::byId($_id);
		if ($_state) {
			$state = $_state;
		} else {
			
			$cmdstatus = $groupe->getCmd(null, 'status');
			if (!is_object($cmdstatus)) {
				return;
			}	
			$state = $cmdstatus->execCmd();
		}
		$cmds = $groupe->getCmd();
		$except = array('alloff','allon','status','last','statuson','statusoff');
		foreach ($cmds as $cmd) {
			if (!in_array( $cmd->getLogicalId(), $except)) {
				if ($state == 0) {
					  $cmdon = cmd::byId(str_replace('#', '', $cmd->getConfiguration('ON')));
					  if(!is_object($cmdon)) {
						  log::add('groupe','debug','cmd ON non trouvé' . $cmd->getName() );
						  continue;
					  }
					  $cmdon->execCmd();			
					
				} else {
					  $cmdoff = cmd::byId(str_replace('#', '', $cmd->getConfiguration('OFF')));
					  if(!is_object($cmdoff)) {
						  log::add('groupe','debug','cmd OFF non trouvé' . $cmd->getName() );
						  continue;
					  }
					  $cmdoff->execCmd();				
				}
			}
		}
	}
	
	public function get_info(){
		try{
			$infos = array();
			$i=0;
			$j=0;
			$z=0;
			$triggers = $this->getCmd();
			foreach ($triggers as $trigger) {
				
				if ($trigger->getConfiguration('state') != "") {
					$z++;
					$cmd = cmd::byId(str_replace('#', '', $trigger->getConfiguration('state')));
					if(!is_object($cmd)) {
						log::add('groupe','debug','cmd non trouvé' . $trigger->getName() );
						continue;
					}

					$val = $cmd->execCmd();
					if($trigger->getConfiguration('reverse') == 0) {
						($val == 0) ? $j++ : $i++;
					} else {
						($val == 0) ? $i++ : $j++;
					}
				}
			}

			if ($i == $z){
				$etat = 1;
				$nbon= $i;
				$name =  $this->getName();
				self::getState($i,$j,$etat,$name);
			} elseif ( $j == $z) {
				$etat = 0;
				$nboff = $j;
				$name =  $this->getName();
				self::getState($i,$j,$etat,$name);
			} else {
				$etat = 1;
				$name =  $this->getName();
				self::getState($i,$j,$etat,$name);
			}
			$data = array($etat, $i, $j,$z);
			if($_id = true) {
				return($data);				
			}			
		} catch(Exception $e) {
			log::add('groupe', 'error', 'error :' . $e);	
		}
	}
	
    public function execute($_trigger_id, $_value) {
		$cmds = $this->getCmd();
		foreach ($cmds as $cmd) {
			if ($cmd->getConfiguration('state') == ('#' .$_trigger_id . '#')) {
				$this->checkAndUpdateCmd('last', $cmd->getName());
				
				break;
			}
		}

		$this->get_info();	
    }
	
	public function dontRemoveCmd() {
		return true;
	}	
	
	public function toHtml($_version = 'dashboard') {
		try{
			$replace = $this->preToHtml($_version);
			if (!is_array($replace)) {
				return $replace;
			}
			$version = jeedom::versionAlias($_version);
			$infos = $this->get_info();
			$etat = $infos[0];
			$replace['#etat#'] = $etat;
			$nbons = $infos[1];
			$nboffs = $infos[2];
			$nb_triggers = $infos[3];
			
			if ($etat == 1) {
				$replace['#icon#'] = $this->getConfiguration('iconOn');
				$replace['#nb#'] = $nbons;
				$replace['#nb_triggers#'] = $nb_triggers;

			} else {
				$replace['#icon#'] = $this->getConfiguration('iconOff');
				$replace['#nb#'] = '0';
				$replace['#nb_triggers#'] = $nb_triggers;				
			}
			
			$action = "onClick='group_action_" . $this->getId() . "()'";
			$replace['#action#'] = $action;	
			$info = "onClick='group_info_" . $this->getId() . "()'";
			$replace['#info#'] = $info;				
			return $this->postToHtml($_version, template_replace($replace, getTemplate('core', $version, 'groupe', 'groupe')));	
		} catch(Exception $e) {
			log::add('groupe', 'error', 'error :' . $e);
		}
	}
}

class groupeCmd extends cmd {
	
	public static $_widgetPossibility = array('custom' => false);
	
    public function execute($_options = array()) {
		
		$groupe = $this->getEqLogic();
		if ($groupe->getConfiguration('activAction') == 0) {
			return;
		}
		$cmds = $groupe->getCmd();
		switch ($this->getLogicalId()) {
			case 'allon': 
				foreach ($cmds as $cmd) {
					$cmdon = cmd::byId(str_replace('#', '', $cmd->getConfiguration('ON')));
					if(!is_object($cmdon) || $cmd->getConfiguration('ON') == "") {
						continue;
					}
					$cmdon->execCmd();					
				}
			break;
			case 'alloff':
				foreach ($cmds as $cmd) {
					$cmdoff = cmd::byId(str_replace('#', '', $cmd->getConfiguration('OFF')));
					if(!is_object($cmdoff) || $cmd->getConfiguration('OFF') == "") {
						continue;
					}
					$cmdoff->execCmd();					
					
				}			
			break;
		}		
    }
}

?>

