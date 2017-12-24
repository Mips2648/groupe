
function readTable(infoGroupe) {
	$("#activeTable").html('');
	$("#inactiveTable").html('');
	var html = "";
	var html1 = "";
	for (var id in infoGroupe) {
		if (infoGroupe[id][3] == 1) {;
			if (infoGroupe[id][0] == 0) {
				html += "<tr><td> " + id + "</td><td><button onclick='launchCmdOn(" + infoGroupe[id][1] +" , \"" + id + "\")' type='button' name=" + id + " class='btn btn-success form-control' value='" + infoGroupe[id][1] +  "'> " + infoGroupe[id][4] +   "</button></td><td><button onclick='launchCmdOff(" + infoGroupe[id][2] +" , \"" + id + "\")' type='button' class='btn btn-danger form-control' name=" + id + " value='" + infoGroupe[id][2] +  "'> " + infoGroupe[id][5] +   "</button></td></tr>";
			} else {
				html1 += "<tr><td> " + id + "</td><td><button onclick='launchCmdOn(" + infoGroupe[id][1] +" , \"" + id + "\")' type='button' name=" + id + " class='btn btn-success form-control' value='" + infoGroupe[id][1] +  "'> " + infoGroupe[id][4] +   " </button></td><td><button onclick='launchCmdOff(" + infoGroupe[id][2] +" , \"" + id + "\")' type='button' class='btn btn-danger form-control' name=" + id + " value='" + infoGroupe[id][2] +  "'> " + infoGroupe[id][5] +   "</button></td></tr>";
			}
					
		} else {
			if (infoGroupe[id][0] == 0) {
				html += "<tr><td> " + id + "</td></tr>";
			} else {
				html1 += "<tr><td> " + id + "</td></tr>";
			}			
			
		}
	}
	$("#activeTable").html(html1);
	$("#inactiveTable").html(html);

}


function launchCmdOn(value,name) {
	console.log(value)
	jeedom.cmd.execute({id: value});
	infoGroupe[name][0] = '1'
	readTable(infoGroupe);	
}

function launchCmdOff(value,name) {
	console.log(value)
	jeedom.cmd.execute({id: value});
	infoGroupe[name][0] = '0'
	readTable(infoGroupe);	
}
