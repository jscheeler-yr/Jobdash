	<table width="100%" id="table_view" class="display" border="0" cellpadding="0" cellspacing="0">
  	<thead>
    	<tr>
      	<th>id</th>
      	<th># of Tasks</th>
        <th>Resource</th>
        <th>Title</th>
        <th>Last Logged In</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
    	<tr>
      	<td></td>
      	<td align="center"># of Tasks</td>
        <td>Resource</td>
        <td>Title</td>
        <td>Last Update</td>
        <td></td>
      </tr>
    </tbody>    
  </table>
<script src="js/dataTables.date.js"></script>
<script src="js/dataTables.dateTypeDetection.js"></script>
<script type="text/javascript">
function fnFormatDetails ( oTable, nTr )
{
	var sOut;
	var aData = oTable.fnGetData( nTr );
	//Get the ID of the project
	var uid = aData[0];
	var details;
	$.ajax({
		type:'GET', 
		url: 'getResourceData', 
		cache: false,
		data: {"uid": uid}, 
		success: function(html) {
			sOut = html;
			oTable.fnOpen(nTr, sOut, 'details');
		}
	});

	
	
}

$(document).ready(function() {
	/*var nCloneTh = document.createElement( 'th' );
	//var nCloneTd = document.createElement( 'td' );
	//nCloneTd.innerHTML = '<img src="images/details_open.png">';
	//nCloneTd.className = "center";
	
	$('#table_view thead tr').each( function () {
		$(this).append( nCloneTh);
	} );
	
	$('#table_view tbody tr').each( function () {
		$(this).append(  nCloneTd.cloneNode( true ) );
	} );
	*/
	var oTable = $('#table_view').dataTable({
		'bJQueryUI':	true,
		'bPaginate':	false,
		'bFilter':		false,
		'bInfo':			false,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "getResources/<?php echo $regionNameID;?>",
		"fnDrawCallback": function() {
			$('div.fg-toolbar.ui-toolbar.ui-widget-header.ui-corner-tl.ui-corner-tr.ui-helper-clearfix').remove();
			$('div.fg-toolbar.ui-toolbar.ui-widget-header.ui-corner-bl.ui-corner-br.ui-helper-clearfix').remove();
		},
		"aoColumns": [
			/* id */ { "bVisible": false },
			/* numTasks */ { "sWidth": "50px", "bSortable": false, },
			/* Resource */ { "sWidth": "180px"},
			/* Title */ { "sWidth": "180px"},
			/* Last Logged In */ { "bSortable": false, "sWidth": "210px" },
			/* Sort  */{ "bSortable": false, "aTargets": [ 0 ], "sWidth": "9px" }
		],
	});
	
	$('#table_view tbody td img').live('click', function () {
		var nTr = this.parentNode.parentNode;
		if ( this.src.match('details_close') )
		{
			$(nTr).removeClass("details");
			/* This row is already open - close it */
			this.src = "images/details_open.png";
			oTable.fnClose( nTr );
		}
		else
		{
			/* Open this row */
			$(nTr).addClass("details");
			this.src = "images/details_close.png";
			fnFormatDetails(oTable, nTr);
		}
	} );
});	
</script>
