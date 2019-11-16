

function readTable(infoGroupe) {
	$("#activeTable tbody").empty();
	$("#inactiveTable tbody").empty();
	var html = "";
	var html1 = "";
	for (var id in infoGroupe) {
		if (infoGroupe[id][3] == 1) {;
			if (infoGroupe[id][0] == 0) {
				html += "<tr class='line1'><td> " + id + "</td><td><button  name=" + id + " data-id='" + infoGroupe[id][7] +  "' data-action='on' class='btn btn-success form-control action' value='" + infoGroupe[id][1] +  "'> " + infoGroupe[id][4] +   "</button></td><td><button   data-id='" + infoGroupe[id][7] +  "' data-action='off' class='btn btn-danger form-control action' name=" + id + " value='" + infoGroupe[id][2] +  "'> " + infoGroupe[id][5] +   "</button></td><td> " + infoGroupe[id][6] +"</td></tr>";
			} else {
				html1 += "<tr class='line2'><td> " + id + "</td><td><button  data-id='" + infoGroupe[id][7] +  "' data-action='on' name=" + id + " class='btn btn-success form-control action' value='" + infoGroupe[id][1] +  "'> " + infoGroupe[id][4] +   " </button></td><td><button  data-id='" + infoGroupe[id][7] +  "' data-action='off' class='btn btn-danger form-control action' name=" + id + " value='" + infoGroupe[id][2] +  "'> " + infoGroupe[id][5] +   "</button></td><td> " + infoGroupe[id][6] +"</td></tr>";
			}

		} else {
			if (infoGroupe[id][0] == 0) {
				html += "<tr class='line1'><td> " + id + "</td><td> " + infoGroupe[id][6] +"</td></tr>";
			} else {
				html1 += "<tr class='line2'><td> " + id + "</td><td> " + infoGroupe[id][6] +"</td></tr>";
			}			
		}
	}
	$("#activeTable tbody").append(html1);
	$("#inactiveTable tbody").append(html);	
	$('.action').on('click', function () {
		$.ajax({// fonction permettant de faire de l'ajax
			type: "POST", // methode de transmission des données au fichier php
			url: "plugins/groupe/core/ajax/groupe.ajax.php", // url du fichier php
			global:false,
			data: {
				action: "execCmdEq",
				id: $(this).value(),
				cmdId: $(this).attr('data-id')
			},
			dataType: 'json',
			error: function(request, status, error) {
				handleAjaxError(request, status, error);
			},
			success: function(data) { // si l'appel a bien fonctionné
				if (data.state != 'ok') {
					$('#div_alert').showAlert({message:  data.result,level: 'danger'});
					return;
				}
				readTable(data.result)

			}
		});	 				
	});
}

