<?php
include_once("./include/connect.php"); 
include_once("./include/preconfig.php"); 
error_reporting(E_ALL);
ini_set('display_errors', 'On');
#require_once 'PEAR.php'; /*call pear interpreter*/
/*dirt purge categorizes mobile numbers base on operators into tables and moves non-nigerian numbers to a dirty table*/
$con = con_string(); /*call the connection string*/
$datetime = tdate(); /*calls the date time function to generate the date time data for the log time stamping*/
$count = 0;
$succ_count = 0;
$fail_count = 0;
$bad = 0;
$good = 0;

/*invalid mobile number remover*/
function rmv_mobile($number,$con){
	$sql = "SELECT * FROM pitch_list WHERE mobile = '$number'";
	$query = mysqli_query($con,$sql);
	$tb_pl_mobile = mysqli_fetch_array($query);
	$fname = $tb_pl_mobile['fname'];
	$lname = $tb_pl_mobile['lname'];
	$email = $tb_pl_mobile['email'];
	$mobile = $tb_pl_mobile['mobile'];
	if($fname == ''){$fname = 'not available';}
	if($lname == ''){$lname = 'not available';}
	if($email == ''){$email = 'not available';}	

	/*copy the data to dirty list*/
	$sql2 = "INSERT INTO pitch_list_dirty (fname,lname,email,mobile) VALUES ('$fname','$lname','$email','$mobile')";
	if(!mysqli_query($con,$sql2)) { die('Error: could not insert dirty number into dirty pl table' . mysqli_error()); }

	/*delete data from pitch list*/
	$sql3 = "DELETE FROM pitch_list WHERE mobile = '$number'";
	if(!mysqli_query($con,$sql3)) { die('Error: could not delete dirty number from pl table' . mysqli_error()); }
	
	return true;							    
								    }

/*log writer code*/
function log_writer($con,$datetime,$payload,$thread){
	$payload_count = count($payload);
	//echo "this log instance is $payload_count<br>";
	//mobile number checking process
	if($thread==0){
		for($i=0;$i<$payload_count;$i++){
			$a = count($payload[$i]);
		//	echo "this log instance content is <b>$a</b><br>";
			if($a > 0){
				$good = $payload[$i][0];
				$bad = $payload[$i][1];
				$succ_count = $payload[$i][2];
				$fail_count = $payload[$i][3];
				$log_type = $payload[$i][4];
				$num = $payload[$i][5];
				$x = $payload[$i][6];
				$sql="INSERT INTO mobile_vd_log (date,log_type,num_records,initiated,pitch_list_count,bad,success_count,failed_count) 
			VALUES ('$datetime','$log_type','$x','$num','$good','$bad','$succ_count','$fail_count')";	
			if(!mysqli_query($con,$sql)) { die('Error: ' . mysqli_error()); }
							}
										}
					}
	//mobile number sorting process
	if($thread==1){
		for($i=0;$i<$payload_count;$i++){
			$a = count($payload[$i]);
		//	echo "this log instance content is <b>$a</b><br>";
			if($a > 0){
				$good = $payload[$i][0];
				$succ_count = $payload[$i][1];
				$fail_count = $payload[$i][2];
				$info = $payload[$i][3];
				$op_type = $payload[$i][4];
				$count = $payload[$i][5];
				$num = $payload[$i][6];
				//
//echo "for the $i count these:<br> pitch_list_count: $good<br>bad: $bad<br>success: $succ_count<br>fail: $fail_count<br>log: $log_type<br>count: $count<br>initiated: $num are collected";
				//
				$sql="INSERT INTO mobile_vd_log (date,log_type,num_records,initiated,pitch_list_count,bad,success_count,failed_count) 
			VALUES ('$datetime','$op_type','$num','$count','$good','$info','$succ_count','$fail_count')";	
			if(!mysqli_query($con,$sql)) { die('Error: ' . mysqli_error()); }
						 }
											}
					}
	//
	if($thread==2){
		for($i=0;$i<$payload_count;$i++){
			$a = count($payload[$i]);
			//echo "this log instance content is <b>$a</b><br>";
			if($a > 0){
				$good = $payload[$i][0];
				$bad = $payload[$i][1];
				$succ_count = $payload[$i][2];
				$fail_count = $payload[$i][3];
				$log_type = $payload[$i][4];
				$count = $payload[$i][5];
				$num = $payload[$i][6];
				$sql="INSERT INTO mobile_vd_log (date,log_type,num_records,initiated,pitch_list_count,bad,success_count,failed_count) 
			VALUES ('$datetime','$log_type','$num','$count','$good','$bad','$succ_count','$fail_count')";	
			if(!mysqli_query($con,$sql)) { die('Error: ' . mysqli_error()); }
						 }
											}
					}
	return true;
														 }

/*this funtion checks the lenght of the user entered mobile number to ensure it does not exceed the required limit of 11 digits*/
function num_cleaner($query,$num,$con){
	$count = 0;
	$succ_count = 0;
	$fail_count = 0;
	$bad = 0;
	$good = 0;
	$log_type = 'check mobile lenght';
	for($x=0;$x <$num; $x++) {
		$tb_pl_data = mysqli_fetch_array($query);
        $number = $tb_pl_data['mobile']; //place number in variable
        $length = strlen($number); //count the string
		if($length == 11)
			{$good = $good + 1;}
		else if($length !== 11){
				$bad = $bad + 1;
		if(rmv_mobile($number,$con) == true)
			{$succ_count = $succ_count + 1;}
		else {$fail_count = $fail_count + 1;}
							     }
    						   }//for loop ends
	$pay_load[0] = $good;
	$pay_load[1] = $bad;
	$pay_load[2] = $succ_count;
	$pay_load[3] = $fail_count;
	$pay_load[4] = $log_type;
	$pay_load[5] = $num; // number of records queried
	$pay_load[6] = $x; //number of records processed in this loop
	$payload = array($pay_load); //inserting array into array
	return $payload;
										 }

/*engine to move matchin records from pitch list table to expected operator pitch list 
invalid mobile number remover by passing new array inst_OP() of sorted numbers to it.*/
function sort_mobile($sorted_mobile,$tablename,$con){
				$mobile_num = $sorted_mobile;
				$sql1 = "SELECT * FROM pitch_list WHERE mobile = $mobile_num";
				$query = mysqli_query($con,$sql1);
				$tb_pl_mobile = mysqli_fetch_array($query);
				$fname = $tb_pl_mobile['fname'];
				$lname = $tb_pl_mobile['lname'];
				$email = $tb_pl_mobile['email'];
				if($fname == ''){$fname = 'not available';}
				if($lname == ''){$lname = 'not available';}
				if($email == ''){$email = 'not available';}	

				/*copy the data to correct operator number header table list*/
				$sql2 = "SELECT * FROM $tablename WHERE mobile = $mobile_num";
				$query2 = mysqli_query($con,$sql2);
				$num2 = mysqli_num_rows($query2);// used to check if msisdn already existing in table
				if($num2 < 1){
					$sql3 = "INSERT INTO $tablename (fname, lname, email, mobile) VALUES ('$fname','$lname','$email','$mobile_num')";	
				if(mysqli_query($con,$sql3)){ 
					/*delete data from pitch list*/
					$sql4 = "DELETE FROM pitch_list WHERE mobile =  $mobile_num";
					if(!mysqli_query($con,$sql4)){die("Error as deleting $mobile_num from table: pitch_list Failed " . mysqli_error($con)); }
						 					  }
				else{ die("Error as inserting $mobile_num into table: $tablename failed: " . mysqli_error($con)); }

								}
				else{
					/*let this part handle issues with msisdns already existing in destination table*/
					/*will be developed in time*/
						}
			return true;    
								    				   }


/*this code block fetches all the mobile numbers from pl_tb and stores them in a php array that can be handled more efficiently. 
We will repeate this block from loop to loop*/
function pl_array($ref_query,$numcounted){
		for($t=0;$t<$numcounted;$t++){
			$ref_array_raw = mysqli_fetch_array($ref_query);
			$ref_array[$t] = $ref_array_raw['mobile'];							
											}
		return $ref_array;
							  			  }


/*engine to check number headers against predefined*/
function num_range($ops_query,$ref_num,$ref_query,$con){
		$filtered = array();
		$numcounted = mysqli_num_rows($ref_query);
		$array_of_msisdns = pl_array($ref_query,$numcounted); //array of msisdns
		// fetch array of operators
		while($operators = mysqli_fetch_array($ops_query)){
			  $tablename = $operators['operator_table_name']; //the destination table
			  $operatorname = $operators['operator_name']; //the destination operator
			  $opr_code = $operators['operator_code'];
			  $sql_seg = "SELECT header_segment FROM num_head_segment WHERE operator_code = $opr_code"; //fetch number segment based on op_code
			  $query_seg = mysqli_query($con,$sql_seg);
			  $op_head_count = mysqli_num_rows($query_seg);
			  //start count from 1 in other to skip first array element which contains the table name.
			  echo "\n This operator is : $operatorname and has operator header type count of : $op_head_count \n";
			  echo "\n Operator $operatorname has table Name: $tablename | operator code : $opr_code \n";
					while($array_seg = mysqli_fetch_array($query_seg)){
					  $head_seg = $array_seg['header_segment']; //collect a  segment
					  //resetting key parameters
					  		$i = 0;
					  		$succ_count = 0;
							$fail_count = 0;
							$good = 0; 
							$processed = 0;
							$datetime = tdate(); /*generate the date time data for the log time stamping*/
					// 		$xy = 0;
					  		$numcounted = mysqli_num_rows($ref_query);//update count from pitch_list table after every loop
					  	for($y=0;$y<$numcounted;$y++){
					  		 $msisdn = $array_of_msisdns[$y]; //pull the msisdn to compare
				 		     $j = substr_compare($msisdn, $head_seg, 0, strlen($head_seg),FALSE);
				 		     $len = strlen($head_seg);
				 	//	   $xy = $xy+1;
				 	//	echo "<p><b>$xy</b>| values of compare parameters at this time are: <b>j: $j , msisdn: $msisdn , header: $head_seg , header length: $len</b></p>";
				 	         if($j == 0){
				 		 	    if(sort_mobile($msisdn,$tablename,$con) == true)/*call sort_mobile to move records to destination*/
									{	$succ_count = $succ_count+1;	}  //successful inserts
								else {	$fail_count = $fail_count+1;	} //failed inserts
				 			    $good = $good+1; //number of matching records processed
				 	 			    	   }
				 	 		 $processed = $processed+1; //total number of initiated records for processing
				  							 			}//for loop ends here
					  $thread = 1; //process log type condition					  
					// echo "<p>number of $tablename msisdns with header $head_seg found in this loop is $succ_count</p>"; 
					  if($succ_count < 1){
						$payload_empty[$i][0] = $good;
						$payload_empty[$i][1] = $succ_count; //successful inserts
						$payload_empty[$i][2] = $fail_count; //failed inserts
						$payload_empty[$i][3] = 'zero loop this time';
						$payload_empty[$i][4] = "no msisdns with header $head_seg moved to $tablename"; //log type
						$payload_empty[$i][5] = $numcounted; //total number of available numbers in pitch_list table
						$payload_empty[$i][6] = $processed; //processed
						log_writer($con,$datetime,$payload_empty,$thread);//write the log for each empty iteration
										   }
					  else if($succ_count > 0){
							$payload[$i][0] = $good; //number of matching records found
							$payload[$i][1] = $succ_count;
							$payload[$i][2] = $fail_count;
		 					$payload[$i][3] = 'no bad counts';
							$payload[$i][4] = "msisdns with header $head_seg moved to $tablename successfully"; //log type
							$payload[$i][5] = $numcounted; //total number of available numbers in pitch_list table
							$payload[$i][6] = $processed; //total number of initiated records for processing
							log_writer($con,$datetime,$payload,$thread);//write the log for each right iteration

							/*-----------------------------------try catch block--------------------------------------*/
							echo "\n ---------END of SEGMENT: $numcounted MSISDNs were tested for $head_seg ---------- \n";
								
										   		}
					  $i = $i++; //increament i with every iteration	
															    			}
														   }		    
		return true;
										 					}



/*this funtion performs a number analysis on the mobile number provided to ensure it meets the requirement.*/
function numfilter($ref_query,$ref_num,$con){
	/*each NETWORK array will be passed in sequence to the filter function. 
	If a match isn't found, the next array will be called in to the search.
	The headers will be passed with the variable name $vallidheaders and the query numbers with $op_number*/
	$sql = "SELECT * FROM operator";
	$ops_query = mysqli_query($con,$sql);
	$status = num_range($ops_query,$ref_num,$ref_query,$con);	
	return $status;
								 			} //function ends here



/*main code*/
$sql = "SELECT mobile FROM pitch_list"; //collect details of all subs
$query = mysqli_query($con,$sql);
$num = mysqli_num_rows($query);

if($num > 0){
$payload = num_cleaner($query,$num,$con);
$thread = 0;
if(log_writer($con,$datetime,$payload,$thread) == true){
	/*refresh pitch_list array data*/
	$ref_query = mysqli_query($con,$sql);
	$ref_num = mysqli_num_rows($ref_query);
	if($ref_num > 0){
	/*call number range validation process*/
	if(numfilter($ref_query,$ref_num,$con) == true){
		echo "\n process completed successfully \n";
		$sql6 = "SELECT mobile FROM pitch_list"; //collect details of all subs
		$query6 = mysqli_query($con,$sql6);
		$num6 = mysqli_num_rows($query6);
echo "\n $num6 MSISDNs are left unprocessed due to unmatched conditions. Consider moving these MSISDNs to the international numbers or another location. \n";
			 											}
					  }
				 	 									}
				}

else{
	$payload = array();
	$pay_load[0] = '';
	$pay_load[1] = '';
	$pay_load[2] = '';
	$pay_load[3] = '';
	$pay_load[4] = 'No PL data found in general PL tb';
	$pay_load[5] = '';
	$pay_load[6] = '';
	$payload[0] = $pay_load; //inserting array into array
	$thread=2;
	log_writer($con,$datetime,$payload,$thread);
	echo 'process completed with failed cases';
	}
?>