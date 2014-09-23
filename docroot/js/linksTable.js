var updatetime = 0;

buildChart();

/*
Fetches the most recent updatetime, if that update time is more recent than the data we have
then it also fetches row data
*/
var filterText = '';
function maybeFilter(table, text, limiter){
	filterText = ((filterText == text) ? "" : text );
	$.uiTableFilter(table, filterText, limiter);
}

function buildChart(){
	$.get("calls.php?links=" + updatetime, function(data){
	    $("#links_table").trigger("update");
		if (data.updated != 'none'){
			updatetime = data.updated;
			$("#links_table tbody tr").remove();
			//get the chunk value
			chunk = $('#chunkchooser').val();
			data = data.rows;
			for(i=0; i < data.length; i++){			
				if(chunk =='' || data[i].chunk != chunk){
					writestyle = '';
				}
				else{
					writestyle = "style='background-color:#FC6C85'";
				}
				row = "<tr class=" + data[i].chunk + " + " + writestyle + ">\
						<td>" + data[i].nodenumber + "</td>\
						<td><a href='" + data[i].newurl + "'>" + data[i].newurl + "</a></td>\
						<td><a href='http://www.lanecc.edu" + data[i].oldurl + "'>" + data[i].oldurl + "</a></td>\
						<td onclick='maybeFilter($(\"#links_table\"), \"" + data[i].chunk + "\", \"Chunk\")'>" + data[i].chunk + "</td>\
						<td class='" + data[i].status+ "' onclick='maybeFilter($(\"#links_table\"), \"" + data[i].status + "\", \"Status\")'>" + data[i].status + "</td>\
						<td><a class='edit' id='edit-" + data[i].id + "' href='#'><span class='ui-icon ui-icon-pencil'></span></a></td>\
						<td><a class='delete' id='delete-" + data[i].id + "' href='?delete=" + data[i].id + "'><span class='ui-icon ui-icon-trash'></span></a></td>\
						<td><a class='history' id='history-" + data[i].id + "' href='#'><span class='ui-icon ui-icon-clock'></span></a></td>\
						<td class='centerthis'><img class='tooltip' title='" + data[i].name + "' src='http://www.gravatar.com/avatar/" + data[i].email + "?s=18&d=mm'></td>\
						<td>" + data[i].t + "</td>\
					   </tr>";
				$('#links_table').children('tbody').append(row);
			}
			setchunks(); 
		}
	},"json");
	setTimeout(buildChart,3000);
}
