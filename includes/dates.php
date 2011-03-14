<?php
	require_once('functions.php');
	$msTypes = queryMySQL("SELECT * FROM milestone_types");
	$msTypesHTML = "";
	while ($row = mysql_fetch_array($msTypes)) {
		$msTypesHTML .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
	}

?>
<div class="msDialog" style="text-align:center">
	<div class="msType">
  	<label for="msType">Type:</label>
    <select id="msType" name="msType">
    	<option value="none">--- Select Type ---</option>
    <?php echo $msTypesHTML;?>
    </select>
  </div>
  <div class="divider"></div>
  <div id="calendar" style="text-align:center">
  	
  </div>
</div>