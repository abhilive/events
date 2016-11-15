<?php 
	class User {
		// database connection and table name 
		private $conn; 
		//private $table_prefix = ''; //Not In Use

		private $user_table_name = "admin_user";
		private $site_users_table_name = "user";
		private $voting_table_name = "voting";
		private $participants_table_name = "participants";
		// object properties 

		private $_order = array();
		private $_status = true;
		private $_message = 'Information has been saved successfully.';

		// constructor with $db as database connection 
		public function __construct($db) { 
			$this->conn = $db;
    	}

    	public function getStatus() {
    		return $this->_status;
    	}

    	public function getMessage() {
    		return $this->_message;
    	}

		// read orders
		function login($columnsArray, $requiredColumnsArray) {
			$this->verifyRequiredParams($columnsArray, $requiredColumnsArray);
            try{
                //print_r($columnsArray);die;
                $query = "SELECT * FROM ". $this->user_table_name . " WHERE username=:username AND password=:password ";
                //echo $columnsArray->username;die;
                $stmt = $this->conn->prepare($query); //removed  after WHERE

                // bind values
		    	$stmt->bindParam(":username", $columnsArray->username);
		    	$stmt->bindParam(":password", $columnsArray->password);

                //var_dump($stmt);die;
                $stmt->execute();
                //$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $affected_rows = $stmt->rowCount();
                if($affected_rows<=0){
                	$response["data"] = null;
                    $response["status"] = "error";
                    $response["message"] = "Invalid Credentials";
                }else{
                	$data = array('access_token'=>'adminuser_login','userName'=>$columnsArray->username);
                	$response["data"] = $data;
                    $response["status"] = "success";
                    $response["message"] = "Logged In Successful";
                }
            }catch(PDOException $e){
            	$response["data"] = null;
                $response["status"] = "error";
                $response["message"] = 'Select Failed: ' .$e->getMessage();
            }

	        return $response;
		}

		function getUsers($columnsArray, $requiredColumnsArray) {
			$this->verifyRequiredParams($columnsArray, $requiredColumnsArray);
			try {
				$query = "SELECT * FROM ". $this->site_users_table_name . " GROUP BY id ASC ";
				$stmt = $this->conn->prepare( $query );
	            $stmt->execute();
	            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	            if(count($rows)<=0){
	                $response["status"] = "error";
	                $response["message"] = "No data found.";
	            }else{
	                $response["status"] = "success";
	                $response["message"] = "Data Selected.";
	            }
                $response["data"] = $rows;
	        }catch(PDOException $e){
	            $response["status"] = "error";
	            $response["message"] = 'Select Failed: ' .$e->getMessage();
	            $response["data"] = null;
	        }
	        return $response;
		}

		function addUser($columnsArray, $requiredColumnsArray) {
			$this->verifyRequiredParams($columnsArray, $requiredColumnsArray);
			try {
				//var_dump($columnsArray);die;
				$query = "INSERT INTO 
		                " . $this->site_users_table_name . "
			            SET 
			                name=:name, email=:email, emp_id=:emp_id";
				$stmt = $this->conn->prepare( $query );

				// bind values
			    $stmt->bindParam(":name", $columnsArray->name);
			    $stmt->bindParam(":email", $columnsArray->email);
			    $stmt->bindParam(":emp_id", $columnsArray->emp_id);

	            if($stmt->execute()){
	            	$affected_rows = $stmt->rowCount();
	                $response["status"] = "success";
	            	$response["message"] = $affected_rows." row(s) updated to database";
	            }else{
	                $response["status"] = "error";
	                $response["message"] = "No rows added.";
	            }
	        }catch(PDOException $e){
	            $response["status"] = "error";
	            $response["message"] = 'Select Failed: ' .$e->getMessage();
	        }
	        return $response;
		}

		function deleteUser($columnsArray, $requiredColumnsArray) {
			$this->verifyRequiredParams($columnsArray, $requiredColumnsArray);
			try {
				//First update participants vote from participants table
				//Step 1: Select Entry from voting table and check which participants are associated with
				$userId = $columnsArray->userId;
				$msg = '';
				$flag_part_vote = $flag_voting_user_entry = true;
				$query_select_voting_entry = "SELECT * FROM ".$this->voting_table_name." WHERE user_id='$userId'";
	     		$stmt_select_voting_entry = $this->conn->prepare( $query_select_voting_entry );

	            if($stmt_select_voting_entry->execute()){
	            	$rows = $stmt_select_voting_entry->fetchAll(PDO::FETCH_ASSOC);
	            	$selected_rows_cnt = $stmt_select_voting_entry->rowCount();

	            	if($selected_rows_cnt>0) { //If user votes for any participant then reduce entry from participants table & delete records from voting table.
	            		$cnt = 0;
		            	foreach ($rows as $_row) {
		            		$res_up_part_vc = $this->updateVotingUnderParticipantTable($_row["part_id"],'minusone');
		            		if($res_up_part_vc['status']=='success') {
		            			$cnt++;
		            		} else {
		            			$msg .= $res_up_part_vc['error'];
		            		}
		            	}
		            	$msg = "Reduce voting count from participants table for ".$selected_rows_cnt." participants.";

		            	if($selected_rows_cnt == $cnt) {
		            		$query_delete_voting_entry = "DELETE FROM ".$this->voting_table_name." WHERE user_id='$userId'";
		            		$stmt_delete_voting_entry = $this->conn->prepare( $query_delete_voting_entry );
		            		if($stmt_delete_voting_entry->execute()){
		            			$deleted_rows_cnt = $stmt_select_voting_entry->rowCount();
		            			$msg .= "\n Deleted ".$deleted_rows_cnt." entires from voting table.";
		            		}
		            	} else {
		            		$msg .= "\n Voting count mismatch under participants & voting table.";
		            		$flag_part_vote = false;
		            	}
	            	} else {
	            		$msg = "User didn't vote anyone.";
	            	}
	            	
	            } else {
	            	$msg .= $stmt_select_voting_entry->errorInfo();
	            	$flag_voting_user_entry = false;
	        	}
	            //End Step 1.
	            //Finally Delete entry from 'user' table.
	            if(($flag_part_vote) && ($flag_voting_user_entry)) {
	            	$query = "DELETE FROM 
		                " . $this->site_users_table_name . "
			            WHERE  
			                id='$userId'"; //$userId
					$stmt = $this->conn->prepare( $query );
					if($stmt->execute()){
		            	$affected_rows = $stmt->rowCount();
		                $response["status"] = "success";
		            	$response["message"] = $msg." \n Deleted ".$affected_rows." row(s) from user table.";
		            }else{
		                $response["status"] = "error";
		                $response["message"] = "No rows deleted.";
		            }
	            } else {
	            	$response["status"] = "error";
		            $response["message"] = $msg;
	            }
	        }catch(PDOException $e){
	            $response["status"] = "error";
	            $response["message"] = 'Error Occured: ' .$e->getMessage();
	        }
	        return $response;
		}

		function updateVotingUnderParticipantTable($part_id, $action) {
			if($action=='minusone') {
				try {
					$sel_query = "SELECT * FROM ".$this->participants_table_name." WHERE id='$part_id'";
					$stmt = $this->conn->prepare( $sel_query );
		            $stmt->execute();
					$num = $stmt->rowCount();
		            $rows = $stmt->fetch(PDO::FETCH_ASSOC);
		            if($num==0){
		                $response["status"] = "error";
		                $response["message"] = "No Participant found for id.".$part_id;
		            } else {
		            	//echo $rows['vc'];die;
		            	$new_cnt = $rows['vc']-1;
		            	$up_query = "UPDATE ".$this->participants_table_name." SET vc='$new_cnt' WHERE id='$part_id'";
						$stmt = $this->conn->prepare( $up_query );
			            if($stmt->execute()) {
			            	$num = $stmt->rowCount();
			            	$response["status"] = "success";
			                $response["message"] = $num." records updated successfully.";
			            }
		            }
		        }catch(PDOException $e){
		            $response["status"] = "exception";
		            $response["message"] = 'Select Failed: ' .$e->getMessage();
		        }
		        return $response;
			}
		}

		function verifyRequiredParams($inArray, $requiredColumns) {
	        $error = false;
	        $errorColumns = "";
	        foreach ($requiredColumns as $field) {
	        // strlen($inArray->$field);
	            if (!isset($inArray->$field) || strlen(trim($inArray->$field)) <= 0) {
	                $error = true;
	                $errorColumns .= $field . ', ';
	            }
	        }

	        if ($error) {
	            $response = array();
	            $response["status"] = "error";
	            $response["message"] = 'Required field(s) ' . rtrim($errorColumns, ', ') . ' is missing or empty';
	            echoResponse(200, $response);
	            exit;
	        }
	    }

	}