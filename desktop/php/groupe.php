<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
sendVarToJS('eqType', 'groupe');
$eqLogics = eqLogic::byType('groupe');
?>

<div class="row row-overflow">
    <div class="col-lg-2 col-md-3 col-sm-4">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter un groupe}}</a>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
                <?php
foreach ($eqLogics as $eqLogic) {
	echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
}
?>
           </ul>
       </div>
   </div>

   <div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
    <legend>{{Mes groupes}}
    </legend>

    <div class="eqLogicThumbnailContainer">
      <div class="cursor eqLogicAction" data-action="add" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
         <center>
            <i class="fa fa-plus-circle" style="font-size : 7em;color:#00A9EC;"></i>
        </center>
        <span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#00A9EC"><center>{{Ajouter}}</center></span>
    </div>
    
    
    <?php
foreach ($eqLogics as $eqLogic) {
	echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
	echo "<center>";
	echo '<img src="plugins/groupe/doc/images/groupe_icon.png" height="105" width="95" />';
	echo "</center>";
	echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
	echo '</div>';
}
?>
</div>
</div>

<div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">

 <ul class="nav nav-tabs" role="tablist">
  <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
  <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-tachometer"></i> {{Equipement}}</a></li>
  <li role="presentation"><a href="#infotab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Configuration}}</a></li>
  <li role="presentation"><a href="#infocmd" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
  <i class='fa fa-cogs eqLogicAction pull-right cursor expertModeVisible' data-action='configure'></i>
</ul>
	<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
		<div role="tabpanel" class="tab-pane active" id="eqlogictab">  
            <form class="form-horizontal">
                <fieldset>
                	<br />
                    <div class="form-group">
                        <label class="col-md-2 control-label">{{Nom de l'équipement groupe}}</label>
                        <div class="col-sm-3">
                            <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                            <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement groupe}}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label" >{{Objet parent}}</label>
                        <div class="col-sm-3">
                            <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                                <option value="">{{Aucun}}</option>
                                <?php
                                    foreach (object::all() as $object) {
                                        echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                                    }
                                ?>
                           </select>
                       </div>
                   </div>
                <div class="form-group">
                  <label class="col-md-2 control-label">{{Catégorie}}</label>
                  <div class="col-md-8">
                    <?php
                                foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                                    echo '<label class="checkbox-inline">';
                                    echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                                    echo '</label>';
                                }
                                ?>
                  </div>
                </div>           
                <div class="form-group">
                  <label class="col-md-2 control-label" >{{Activer}}</label>
                  <div class="col-md-1">
                    <input type="checkbox" class="eqLogicAttr checkbox-inline" data-label-text="{{Activer}}" data-l1key="isEnable" checked/>
                  </div>
                  <label class="col-md-2 control-label prog_visible" >{{Visible}}</label>
                  <div class="col-md-1 prog_visible">
                    <input type="checkbox" class="eqLogicAttr checkbox-inline" data-label-text="{{Visible}}" data-l1key="isVisible" checked/>
                  </div>
                </div>
        </fieldset>
        </form>

        <form class="form-horizontal">
            <fieldset>
                <div class="form-actions">
                    <a class="btn btn-danger eqLogicAction" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
                    <a class="btn btn-success eqLogicAction" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
                </div>
            </fieldset>
        </form>             
        </div>
        
         <div role="tabpanel" class="tab-pane" id="infotab">      	      	
             <form class="form-horizontal">
            

         <br />
         <a  class="btn btn-success btn-sm cmdAction btAdd_table_cmd" data-action="addCmd"><i class="fa fa-plus-circle"></i> {{Ajouter un équipement}}</a>
        <br/>            
        <legend >{{Equipements }}</legend>
	
                <table id="table_cmd" class="table table-bordered table-condensed ui-sortable table_cmd">
                    <thead>
                        <tr>
                            <th style="width: 10%;">{{Nom}}</th>
                            <th  class="etat" >{{Commande Etat}}</th>
                            <th style=" display: none;" class="action" >{{Commande ON}}</th>
                            <th style=" display: none;" class="action" >{{Commande OFF}}</th>
                            <th style="width: 5%;">{{Inverser}}</th>
                            <th style="width: 5%;">{{Effacer}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>   
            <form class="form-horizontal">
            <label class="col-sm-1 control-label">{{Icône On}}</label>
            <div id="div_on">
            	<div class="icone">
                     <div class="col-sm-2">
                        <a class="iconeOn btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fa fa-flag"></i> {{Icône}}</a>
                        <span class="eqLogicAttr iconeAttrOn label label-info cursor"  data-l1key="configuration" data-l2key="iconOn"  style="font-size : 1em;" ></span>
                     </div>                  
                 </div>
            </div>      
            </form>
            <form class="form-horizontal">
            <label class="col-sm-1 control-label">{{Icône Off}}</label>
            <div id="div_off">
            	<div class="icone">
                     <div class="col-sm-2">
                        <a class="iconeOff btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fa fa-flag"></i> {{Icône}}</a>
                        <span class="eqLogicAttr iconeAttrOff label label-info cursor" data-l1key="configuration" data-l2key="iconOff"  style="font-size : 1em;" ></span>
                     </div>                   
                 </div>
            </div>      
            </form>                                          
            
            </form>
  
            <br/>

                    <div class="form-actions col-sm-2">
                        <a class="btn btn-success eqLogicAction" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
                    </div>                                      
		</div>
        
                     <div role="tabpanel" class="tab-pane" id="infocmd">  
                        <table style="width: 400px" id="table_info" class="table table-bordered table-condensed">
                            <thead>
                                <tr>
                                    <th>{{Nom}}</th>
                                    <th>{{Action}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>             
                     
                     </div>
	</div>
</div>
</div>

         

			

<?php include_file('desktop', 'groupe', 'js', 'groupe');?>
<?php include_file('core', 'plugin.template', 'js');?>