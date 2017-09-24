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
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

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
		$status->setSubType('other');
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
	
	public function get_info($_id=false){
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
			$infos = $this->get_info($this->id);
			$etat = $infos[0];
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
			return $this->postToHtml($_version, template_replace($replace, getTemplate('core', $version, 'groupe', 'groupe')));	
		} catch(Exception $e) {
			log::add('groupe', 'error', 'error :' . $e);
		}
	}
}

class groupeCmd extends cmd {
	
	public static $_widgetPossibility = array('custom' => false);
	
    public function execute($_options = array()) {
		 log::add('groupe', 'debug', 'Lancement de execute ');
    }
}

?>

