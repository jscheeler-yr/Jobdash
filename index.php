<?php
	include 'header.php';
?>
	<table width="100%" id="table_view" class="display" border="0" cellpadding="0" cellspacing="0">
  	<thead>
    	<tr>
      	<th>id</th>
      	<th>Project</th>
        <th>Type</th>
        <th>Job Number</th>
        <th>Milestone</th>
        <th>Milestone Description</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
    	<tr>
      	<td></td>
      	<td>Project</td>
        <td>Type</td>
        <td>Job Number</td>
        <td>Milestone</td>
        <td>Milestone Description</td>
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
	var projID = aData[0];
	var details;
	$.ajax({
		type:'GET', 
		url: 'getProjectData', 
		cache: false,
		data: {"pid": projID}, 
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
		"sAjaxSource": "getProjects",
		"fnDrawCallback": function() {
			$('div.fg-toolbar.ui-toolbar.ui-widget-header.ui-corner-tl.ui-corner-tr.ui-helper-clearfix').remove();
			$('div.fg-toolbar.ui-toolbar.ui-widget-header.ui-corner-bl.ui-corner-br.ui-helper-clearfix').remove();
		},
		"aoColumns": [
			/* id */ { "bVisible": false },
			/* Project */ { "sWidth": "280px"},
			/* Project Type */ { "sWidth": "180px"},
			/* Job Number */ { "sWidth": "180px"},
			/* Milestone */ { "sWidth": "100px"},
			/* Milestone desc */ { "bSortable": false, "sWidth": "210px" },
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
<?php
	require_once 'footer.php';
?>
