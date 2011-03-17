jQuery.fn.dataTableExt.aTypes.unshift(
	function ( sData )
	{
		if (sData.match(/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[012])\/(19|20|21)\d\d$/))
		{
			return 'uk_date';
		}
		return null;
	} 
);