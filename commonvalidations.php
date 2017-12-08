<?php

/*
STANDARD SET OF INPUTS

1. Mobile Number (NNNNNNNNNN) - 10 digit number
2. Email ID (xxx@xxxx.xxx) - Any email ID
3. Date (DD-MM-YYYY) - Our standard (31-31-0000 should not pass through!)
4. Time (HH:II) - Our Standard (Example - 13:15 for 01:30 Noon) (44:44 should not pass through!) {{24 Hours Format}}
5. Name (Characters only - with . DOT and space permitted) (No ' or ` or , or anything of that sort) | Maximum length of 30

C1. Date Comparator
C2. Time Comparator

*/




/* 	
	1. Mobile Number Valdiation
*/
function validateMobileNumber($phone){
	if(preg_match("/^[789]\d{9}$/", $phone)){
		return true;
	}
	else{
		return false;
	}
}






/*
	C1. Date Comparator
*/
function isSecondDateGreater($first, $second){
//This expects $first and $second in OUR STANDARDS
$formattedFirst = date('Ymd', strtotime($first));
$formattedSecond = date('Ymd', strtotime($second));

	if($formattedFirst <= $formattedSecond){
		return 1;
	}
	else{
		return 0;
	}
}

?>