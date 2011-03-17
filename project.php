<?php
	require_once 'header.php';
	
		//Get the possible types of projects
	$projectTypes = queryMySQL("SELECT * FROM project_types");
	$typesHTML = "";
	while ($row = mysql_fetch_array($projectTypes)) {
		$typesHTML .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
	}
	
	$resources = queryMySQL("SELECT id, firstname, lastname FROM users WHERE regionID='$regionID'");
	$resourcesHTML = "";
	while ($row = mysql_fetch_array($resources)) {
		$resourcesHTML .= '<option value="'.$row['id'].'">'.$row['firstname'].' '.$row['lastname'].'</option>';
	}

	if ($_GET['action'] == 'add') {
		$action = 'add';
		$pageTitle = "Add Project";
		$frmAction ='project/add/submit';
		$btnAction = 'Add Project';
		
	} else if ($_GET['action'] == 'edit') {
		$pid = $_GET['pid'];
		$editProject = mysql_fetch_array(queryMySQL("SELECT * FROM projects WHERE id='$pid'"));
		$action = 'edit';
		$pageTitle = "Edit Project: ". $editProject['name'];
		$frmAction ='submit?pid='.$pid;
		$btnAction = 'Save';
		
		$status = queryMySQL("SELECT * FROM task_status");
		$statusHTML = "<select class='status' name='status[]'>";
		while ($row = mysql_fetch_array($status)) {
			$statusHTML .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
		}
		$statusHTML .= "</select>";

		
		//Get the milestones and tasks
		$milestones = queryMySQL("SELECT * FROM milestones WHERE projectID='$pid'");
		$milestonesHTML = "";
		$iDate = 0;
		while($row=mysql_fetch_array($milestones)) {
			$typeID = $row['milestone_typeID'];
			$milestoneType = mysql_fetch_array(queryMySQL("SELECT name FROM milestone_types WHERE id='$typeID'"));
			$msType = $milestoneType['name'];
			
			$datePicked = $row['milestone'];
			
			$msIDInput = '<input type="hidden" value="'.$row['id'].'" name="msID[]" />';
			$msTypeInput = '<input type="hidden" value="'.$typeID.'" name="msType[]" />';
			$msDateInput = '<input type="hidden" value="'.$datePicked.'" name="msDate[]" />';
			$milestonesHTML .= '<div id="ms-'.$iDate.'">'.$msIDInput.$msTypeInput.$msDateInput.'<span class="msCol txtMSDate">'.$datePicked.'</span><span class="msCol txtMSType">'.$msType.'</span> <span class="msCol"><img src="'.DOC_ROOT.'images/b_edit.png" width="16" height="16" alt="Edit" class="msButton inline Edit" /><img src="'.DOC_ROOT.'images/b_delete.png" width="16" height="16" alt="Delete" class="msButton inline DeleteDate" /></span></div>';
			$iDate++;
		}
		
		$tasks = queryMySQL("SELECT * FROM tasks WHERE projectID='$pid'");
		$tasksHTML = '';
		while($row=mysql_fetch_array($tasks)){
			$resourceID = $row['userID'];
			$statusID = $row['statusID'];
			$tasksHTML .= '<tr><td><img src="'.DOC_ROOT.'images/b_delete.png" width="16" height="16" alt="Delete" class="DeleteTask" /><input type="hidden" name="taskID[]" value="'.$row['id'].'" /></td><td><input type="input" name="taskName[]" value="'.stripslashes($row['name']).'" /></td><td><input type="hidden" value="'.$resourceID.'" /><select class="resource" name="resource[]"><option>--- Select Resource ---</option>'.$resourcesHTML.'</select></td><td><input type="hidden" value="'.$statusID.'" />'.$statusHTML.'</td><td></td></tr>';
		}
	}


?>
<h2><?php echo $pageTitle;?></h2>
	<form id="frmProjectInfo" method="post">
  	<p><label for="name">Project Name: </label>
    <input type="input" id="name" name="name" class="form" /></p>
  	<p><label for="type">Project Type: </label>
    <select id="type" name="type" class="form">
    	<option>--- Select Type ---</option>
    	<?php echo $typesHTML; ?>
    </select></p>
  	<p><label for="jobNum">Job Number: </label>
    <input type="input" id="jobNum" name="jobNum" class="form" alt="jobNum" /></p>
		<div id="milestones">
      <div class="left"><label for="milestones">Milestones: </label></div>
      <div id="msList" class="form">
        <span><img src="<?php echo DOC_ROOT;?>images/calendar.png" width="16" height="16" class="inline" align="top" id="datepicker" /> Click to add Milestone</span>
<?php if ($action == 'edit') { echo $milestonesHTML; } ?>
      </div>
		</div>
    <div class="divider"></div>
    <h3>Tasks</h3>
    <table width="100%" cellpadding="1" cellspacing="0" class="tablesorter">
      <thead>
        <tr>
        	<th width="20"></th>
          <th width="25%">Name</th>
          <th width="25%">Resource</th>
          <th width="25%">Status</th>
          <th width="25%">Comments</th>
        </tr>
      </thead>
      <tfoot>
      	<tr>
          <th colspan="5">
            <div>
              <a id="btnCopy" class="submit small" onclick="copy();">Copy</a>
              <a id="btnAddRow" class="submit small" onclick="addRow();">Add Row</a>
            </div>
          </th>
        </tr>
      </tfoot>
      <tbody>
<?php if ($action == 'edit') { echo $tasksHTML; } ?>    
      </tbody>
		</table>
  </form>
 <div id="buttons">
  <a id="btnCancel" class="submit orange medium" onclick="cancel();">Cancel</a>
  <a id="btnAddUser" class="submit green medium" onclick="frmSubmit();"><?php echo $btnAction; ?></a>
</div>

<?php
	require_once 'footer.php';
?>
<script type="text/javascript">
$('input[name="jobNum"]').setMask('***-***-******');
var date = 0;
var $dialog = $('<div></div>');
var iDate = 0; 
var currentDate;
$(document).ready(function() {  
<?php if ($action == 'add') { ?>
	addRow();
<?php } ?>
	$('#frmProjectInfo').attr('action', '<?php echo $frmAction; ?>');
	$('#datepicker').click(function(){
			dates();
	});
	
	$dialog.load('<?php echo DOC_ROOT;?>includes/dates.php')
					.dialog({
						autoOpen: false,
						title: 'Set milestones',
						width: 220,
						modal: true
					});
//If page is in edit mode
<?php if ($action == 'edit') { ?>
	$('#name').val('<?php echo stripslashes($editProject['name']);?>');
	$('#type option[value="<?php echo $editProject['typeID'];?>"]').attr('selected', 'selected');
	$('#jobNum').val('<?php echo $editProject['jobNumber'];?>');
	$('.resource').each(function() {
		var resourceID = $(this).siblings('input[type="hidden"]').val();
		$(this).children('option[value="'+resourceID+'"]').attr('selected', 'selected');
	});
	$('.status').each(function() {
		var statusID = $(this).siblings('input[type="hidden"]').val();
		$(this).children('option[value="'+statusID+'"]').attr('selected', 'selected');
	});
	
	$('.DeleteTask').click(function() {
			deleteTask($(this));
	});
	$('.DeleteDate').click(function() {
			deleteDate($(this));
	});
	$('.Edit').click(function() {
			editDate($(this).parent().parent('div').attr('id'));
	});


<?php } ?>
});

function frmSubmit() {
		$('#frmProjectInfo').submit();
}

function cancel() {
	window.location = "<?php echo DOC_ROOT;?>";
}

function addRow() {
	var row = '<tr><td><img src="<?php echo DOC_ROOT;?>images/b_delete.png" width="16" height="16" alt="Delete" class="DeleteTaskAdd" /></td><td><input type="input" name="taskName[]" /></td><td><select class="resource" name="resource[]"><option>--- Select Resource ---</option> <?php echo $resourcesHTML; ?></select></td><td>ASSIGNED</td><td></td></tr>';
	$(row).appendTo('tbody');
	
	$('.DeleteTaskAdd').click(function() {
			deleteTask($(this));
	});
}

function copy() {
	var taskName = 	$(".tablesorter tbody tr:last input[name^='taskName']").val();
	var resourceID = $(".tablesorter tbody tr:last select option:selected").val();
	
	addRow();
	$(".tablesorter tbody tr:last input[name^='taskName']").val(taskName);
	$(".tablesorter tbody tr:last option[value='"+resourceID+"']").attr('selected', 'selected');
}

function dates() {
		$dialog.dialog('open');
		$dialog.dialog({
			buttons: {
				"Next": function() {
					//Add date
					addMS($('#msType option:selected'), date);
					//Reset calendar and drop down
					$("#calendar").datepicker("setDate", null);
					$("#msType option[value='none']").attr('selected', 'selected');
				},
				"Done": function() {
					//Add date
					addMS($('#msType option:selected'), date);
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});
		$( "#calendar" ).datepicker({
				onSelect: function(dateText, inst) {
					date = dateText;
				}
		});
		$("#calendar").datepicker("option", "dateFormat", 'yy-mm-dd');

}

function addMS(selected, datePicked) {
	var msTypeInput = "<input type='hidden' value='"+$(selected).val()+"' name='msType[]' />";
	var msDateInput = "<input type='hidden' value='"+datePicked+"' name='msDate[]' />";
	
	var dateDiv = '<div id="ms-'+iDate+'">'+msTypeInput + msDateInput+'<span class="msCol txtMSDate">'+datePicked+'</span><span class="msCol txtMSType">'+$(selected).text()+'</span> <span class="msCol"><img src="<?php echo DOC_ROOT;?>images/b_edit.png" width="16" height="16" alt="Edit" class="msButton inline Edit" /><img src="<?php echo DOC_ROOT;?>images/b_delete.png" width="16" height="16" alt="Delete" class="msButton inline DeleteDateAdd" /></span></div>';
	$('#msList').append(dateDiv);
	iDate++;
	
	$('.Edit').click(function() {
			editDate($(this).parent().parent('div').attr('id'));
	});
	$('.DeleteDateAdd').click(function() {
			deleteDate($(this));
	});
}

function editDate(div) {
	var option =  $('#'+div+' input[name^="msType"]').val();
	$dialog.dialog('open');
	$dialog.dialog({
		buttons: {
			"Done": function() {
				//Edit Date
				$('#'+div).children('input[name="msType[]"]').val($('#msType option:selected').val());
				$('#'+div).children('input[name="msDate[]"]').val(date)
				$('#'+div).children('.txtMSDate').text(date)
				$('#'+div).children('.txtMSType').text($('#msType option:selected').text())
				$( this ).dialog( "close" );
			}
		}});
	$("#calendar").datepicker("setDate", $('#'+div+' input[name^="msDate"]').val());
	$("#msType option[value='"+option+"']").attr('selected', 'selected');
}

function deleteDate(milestone) {
<?php if ($action == 'edit') { ?>
	if (milestone.attr('class') != "msButton inline DeleteDateAdd") {
		var msID = milestone.parent().parent().children('input[name^="msID"]').val();
		var removeMS = '<input type="hidden" name="removeMS[]" value="'+msID+'" />';
		$('#frmProjectInfo').append(removeMS);
	}
<?php } ?>
	milestone.parent().parent().remove();
}

function deleteTask(task) {
<?php if ($action == 'edit') { ?>

	if (task.attr('class') != "DeleteTaskAdd") {
		var taskID = task.siblings('input[type="hidden"]').val();
		var removeTask = '<input type="hidden" name="removeTask[]" value="'+taskID+'" />';
		$('#frmProjectInfo').append(removeTask);
	}
<?php } ?>
	task.parent().parent().remove();
}
</script>

</body>
</html>