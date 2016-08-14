<?PHP
			  
	  // * validate value has presence
	  // use trim() so empty spaces don't count
	  // use === to avoid false positives
	  // empty() would consider "0" to be empty
	  function has_presence($value) {
	  	$trimmed_value = trim($value);
	    return isset($trimmed_value) && $trimmed_value !== "";
	  }	  
	
?>