<?php
	require_once('functions.php');
	$gaSql['link'] = connect();
	$sTable = "users";
	$regionID = $_GET['regionID'];
	/*
	 * Script:    DataTables server-side script for PHP and MySQL
	 * Copyright: 2010 - Allan Jardine
	 * License:   GPL v2 or BSD (3-point)
	 */
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
	$aColumns = array( 'id', ' ', 'name', 'titleID', 'lastLoggedIn');	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "id";
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
	 * no need to edit below this line
	 */
	
	/* 
	 * MySQL connection
	 */
	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}
	
	
	/*
	 * Ordering
	 */
	if ( isset( $_GET['iSortCol_0'] ) )
	{
			$sOrder = "ORDER BY ";
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
				}
			}
			
			$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" )
			{
				$sOrder = "";
			}
	}
	
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$sWhere = "WHERE regionID='$regionID'";
	if (isset($_GET['sSearch'])) {
		if ( $_GET['sSearch'] != "" )
			{
				$sWhere = " AND (";
				for ( $i=0 ; $i<count($aColumns) ; $i++ )
				{
					$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
				}
				$sWhere = substr_replace( $sWhere, "", -3 );
				$sWhere .= ')';
			}
			
			/* Individual column filtering */
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
				{
						$sWhere .= " AND ";
						$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
				}
			}
		}
	
	
	/*
	 * SQL queries
	 * Get data to display
	 */
	$colCount = count($aColumns);
	$columns = "firstname,lastname,";
	foreach($aColumns as $column) {
		if (($column != " ") && ($column != "name")) {
			$columns .= $column . ",";
		}
	}
	//Trim comma from end of $columns;
	$sCol = substr($columns, 0,strlen($columns)-1);
	$sQuery = "SELECT SQL_CALC_FOUND_ROWS ".$sCol." FROM $sTable $sWhere $sOrder $sLimit";
	$rResult = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());

	/* Data set length after filtering */
	$sQuery = "SELECT FOUND_ROWS()";
	$rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "SELECT COUNT(".$sIndexColumn.") FROM $sTable";
	$rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	
	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ($aColumns[$i] == 'titleID' ){
				$titleID = $aRow[$aColumns[$i]];
				$type = mysql_fetch_array(mysql_query("SELECT full FROM user_titles WHERE id='$titleID'"));
				$row[] = $type['full'];//($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];
			} else if ($aColumns[$i] == 'lastLoggedIn' ) {
				if ($aRow[ $aColumns[$i] ] != NULL) {
					list($date, $time) = explode(" ", $aRow[ $aColumns[$i] ]);
					list($year, $month, $day) = explode("-", $date);
					list($hour, $minute, $second) = explode(":", $time);
					
					$lastLoggedIn = date("D M j g:ia");
					$row[] = $lastLoggedIn; 
				} else {
					$row[] ="";
				}
				$row[] = "<img src=\"images/details_open.png\">";
			} else if ($aColumns[$i] == 'name' ) {
				$name = $aRow['lastname'] . ", " .$aRow['firstname'];
				$row[] = $name;
			} else if ( $aColumns[$i] != ' ' ){
				/* General output */
				if (strlen($aRow[ $aColumns[$i] ]) >= 30) {
					$row[] = substr($aRow[ $aColumns[$i] ], 0, 30) . "...";
				} else {
					$row[] = $aRow[ $aColumns[$i] ];
				}
			}
			else if ( $aColumns[$i] == ' ' ) {
				$resourceID = $aRow['id'];
				$tasksByResource = queryArray("SELECT COUNT(*) FROM tasks WHERE userID='$resourceID'");
				$numTasks = $tasksByResource['COUNT(*)'];
				$row[] = $numTasks;
			}
			
		}
		$output['aaData'][] = $row;
	}
	
	echo json_encode( $output );
?>