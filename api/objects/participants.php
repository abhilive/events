<?php 
	class Participants {
		// database connection and table name 
		private $conn; 
		//private $table_prefix = ''; //Not In Use

		private $participants_table_name = "participants";
		private $group_table_name = "group_type";
		private $location_table_name = "location";
		private $status_table_name = "statuses";
		private $feedback_table_name = "feedback";
		private $user_table_name = "user";
		private $voting_table_name = "voting";
		// object properties 

		private $_order = array();
		private $_status = 0; //Set Default Status to garbage value
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
		function readAllActivities() {
			try{
		    // select all query
	            $query = "SELECT scl_gr.id AS id, scl_gr.name AS name \n"
				    . "FROM ".$this->group_table_name." AS scl_gr ORDER BY name ASC\n"
				    . "";
				    //echo $query;die;
	            $stmt = $this->conn->prepare( $query );
	            $stmt->execute();
	            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	            if(count($rows)<=0){
	                $response["status"] = "warning";
	                $response["message"] = "No data found.";
	            }else{
	                $response["status"] = "success";
	                $response["message"] = "Data selected from database";
	            }
	            	
                $response["data"] = $rows;
                //$response["data"]["items"] = json_decode($rows["items"]);
	        }catch(PDOException $e){
	            $response["status"] = "error";
	            $response["message"] = 'Select Failed: ' .$e->getMessage();
	            $response["data"] = null;
	        }
	        return $response;
		}

		// read statuses
		function readAllStatuses() {
			try{
		    // select all query
	            $query = "SELECT scl_st.id AS id, scl_st.title AS name \n"
				    . "FROM ".$this->status_table_name." AS scl_st ORDER BY id ASC\n"
				    . "";
				    //echo $query;die;
	            $stmt = $this->conn->prepare( $query );
	            $stmt->execute();
	            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	            if(count($rows)<=0){
	                $response["status"] = "warning";
	                $response["message"] = "No data found.";
	            }else{
	                $response["status"] = "success";
	                $response["message"] = "Data selected from database";
	            }
	            	
                $response["data"] = $rows;
                //$response["data"]["items"] = json_decode($rows["items"]);
	        }catch(PDOException $e){
	            $response["status"] = "error";
	            $response["message"] = 'Select Failed: ' .$e->getMessage();
	            $response["data"] = null;
	        }
	        return $response;
		}
		// read orders
		function load($part_id) {
			try{
		    // select all query
	            $query = "SELECT scl_pr.id AS id, scl_pr.name AS participant_name, scl_pr.email AS email, scl_pr.description AS description, scl_gr.name AS group_name, scl_lc.name AS location, scl_st.title AS status, COUNT(scl_vt.id) AS vc \n"
				    . "FROM ".$this->participants_table_name." AS scl_pr \n"
					. "JOIN ".$this->group_table_name." AS scl_gr ON scl_pr.group_id = scl_gr.id \n"
				    . "JOIN ".$this->location_table_name." AS scl_lc ON scl_pr.location_id = scl_lc.id \n"
				    . "JOIN ".$this->status_table_name." AS scl_st ON scl_pr.status_id = scl_st.id \n"
				    . "JOIN ".$this->voting_table_name." AS scl_vt ON scl_pr.id = scl_vt.part_id \n"
					. "WHERE scl_pr.id = ".$part_id." \n"
				    . "";
				//echo $query;die;
	            $stmt = $this->conn->prepare( $query );
	            $stmt->execute();
	            $rows = $stmt->fetch(PDO::FETCH_ASSOC);

	            if(count($rows)<=0){
	                $response["status"] = "warning";
	                $response["message"] = "No data found.";
	            }else{
	                $response["status"] = "success";
	                $response["message"] = "Data selected from database";
	            }
	            	
                $response["data"] = $rows;
                //$response["data"]["items"] = json_decode($rows["items"]);
	        }catch(PDOException $e){
	            $response["status"] = "error";
	            $response["message"] = 'Select Failed: ' .$e->getMessage();
	            $response["data"] = null;
	        }
	        return $response;
		}
		//For Admin Select to show records
		function readAll() {
			try{
		    // select all query
	            $query = "SELECT scl_pr.id AS id, scl_pr.name AS participant_name, scl_gr.name AS group_name, scl_lc.name AS location, scl_st.title AS status \n"
				    . "FROM ".$this->participants_table_name." AS scl_pr \n"
				    . "JOIN ".$this->group_table_name." AS scl_gr ON scl_pr.group_id = scl_gr.id \n"
				    . "JOIN ".$this->location_table_name." AS scl_lc ON scl_pr.location_id = scl_lc.id \n"
				    . "JOIN ".$this->status_table_name." AS scl_st ON scl_pr.status_id = scl_st.id \n"
				    . "GROUP BY scl_pr.id ORDER BY id DESC\n"
				    . "";
				    //echo $query;die;
	            $stmt = $this->conn->prepare( $query );
	            $stmt->execute();
	            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	            if(count($rows)<=0){
	                $response["status"] = "warning";
	                $response["message"] = "No data found.";
	            }else{
	                $response["status"] = "success";
	                $response["message"] = "Data selected from database";
	            }
	            	
                $response["data"] = $rows;
                //$response["data"]["items"] = json_decode($rows["items"]);
	        }catch(PDOException $e){
	            $response["status"] = "error";
	            $response["message"] = 'Select Failed: ' .$e->getMessage();
	            $response["data"] = null;
	        }
	        return $response;
		}

		// read All Group & Participants
		function readAllGroups() {
			try{
		    $response = '';
    		$json_params = $this->generateJson(array('id'=>'scl_pr.id','nm'=>'scl_pr.name','lc'=>'scl_lc.name','st'=>'scl_st.title','vc'=>'scl_pr.vc'),'prts');
		    //$json_params = $this->generateJson(array('id'=>'scl_pr.id','nm'=>'scl_pr.name','lc'=>'scl_lc.name','st'=>'scl_st.title', 'vc'=>'count(scl_vt.id)'),'prts');
		    // Ref Link : http://pentahointegra.blogspot.in/2013/01/how-to-increase-groupconcat-function.html
		    // Code to set max length for group concat
		    $query_to_set = "SET SESSION group_concat_max_len = 1000000;";
		    $stmt = $this->conn->prepare( $query_to_set );
		     
		    // execute query
		    $stmt->execute();
		    //Add condition to exclude (Garbage, Disqualified & WildCard Entry);
		    $query = "SELECT scl_gr.id, scl_gr.name, COUNT(scl_pr.id) as ttl_pr, ".$json_params." \n"
		    		. "FROM ".$this->group_table_name." AS scl_gr \n"
				    . "JOIN ".$this->participants_table_name." AS scl_pr ON scl_gr.id = scl_pr.group_id AND scl_pr.status_id NOT IN (0,1,2) \n"
				    . "JOIN ".$this->location_table_name." AS scl_lc ON scl_pr.location_id = scl_lc.id \n"
				    . "JOIN ".$this->status_table_name." AS scl_st ON scl_pr.status_id = scl_st.id \n"
				    /*. "JOIN ".$this->voting_table_name." AS scl_vt ON scl_pr.id = scl_vt.part_id \n"*/
				    . "GROUP BY scl_gr.id ORDER BY name ASC\n"
				    . "";
			// echo $query;die;
		    // prepare query statement
		    $stmt = $this->conn->prepare( $query );
		     
		    // execute query
		    $stmt->execute();

		    $num = $stmt->rowCount();

		    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	        $result = array();
			// check if more than 0 record found
				if($num>0){
			     
			    $data = array();

			    foreach ($rows as $_row) {

			        $result[] = array(
			        		'id' => $_row['id'],
			        		'name' => $_row['name'],
			        		'ttl_pr' => $_row['ttl_pr'],
			        		'prts' => json_decode($_row['prts']) // decode json data getting for items
			        	); 
						
			    	} //End Foreach
			    } // End IF

			    if(count($rows)<=0){
	                $response["status"] = "warning";
	                $response["message"] = "No data found.";
	            }else{
	                $response["status"] = "success";
	                $response["message"] = "Data selected from database";
	            }

                $response["data"] = $result;
                //$response["data"]["items"] = json_decode($rows["items"]);
	        }catch(PDOException $e){
	            $response["status"] = "error";
	            $response["message"] = 'Select Failed: ' .$e->getMessage();
	            $response["data"] = null;
	        }
	        return $response;
		}

		function addFeedback($columnsArray, $requiredColumnsArray) {
			$this->verifyRequiredParams($columnsArray, $requiredColumnsArray);
			try{
				$query = "INSERT INTO
		                " . $this->feedback_table_name . "
		            SET 
		                name = :name, email=:email, location=:location, description=:description";
		   	//print_r($columnsArray);die;
		    // prepare query
		    $stmt = $this->conn->prepare($query);
		 
		    // bind values
		    $stmt->bindParam(":name", $columnsArray->name);
		    $stmt->bindParam(":email", $columnsArray->email);
		    $stmt->bindParam(":location", $columnsArray->location);
		    $stmt->bindParam(":description", $columnsArray->description);

		    if($stmt->execute()){
	     		$affected_rows = $stmt->rowCount();
	     		$response["status"] = "success";
	            $response["message"] = $affected_rows." row inserted into database";
	            $response["data"] = 0;
	     	} else {
	     		$response["status"] = "success";
	            $response["message"] = $stmt->errorInfo();
	            $response["data"] = 0;
	     	}
		    	
		    	
		    } catch(PDOException $e) {
		    	$response["status"] = "error";
	            $response["message"] = 'Insert Failed: ' .$e->getMessage();
	            $response["data"] = 0;
			}
			return $response;
		}

		function generateJson($fields, $as) {
			$str = "CONCAT('[',GROUP_CONCAT(CONCAT('{\"";
			$cnt = count($fields);
			$i = 0;
			foreach ($fields as $k=>$v) {
				$str.= $k . "\":','\"'," . $v;

				if ($i != $cnt - 1) {
					$str.= ",'\",\"";
				}
				$i++;
			}
			$str.= ",'\"}')),']') AS ".$as;
			return $str;
		}

		// create participants
		function add($columnsArray, $requiredColumnsArray){
			$this->verifyRequiredParams($columnsArray, $requiredColumnsArray);
		    try {
		    // query to insert record
		    $query = "INSERT INTO 
		                " . $this->participants_table_name . "
		            SET 
		                name = :name, description = :description, email=:email, group_id=:group_id, location_id=:location_id, status_id=:status_id";
		     
		    // prepare query
		    $stmt = $this->conn->prepare($query);
		 
		    // posted values
		    $part_names = strip_tags($columnsArray->name);
		    $description = strip_tags($columnsArray->description);
		    $part_emails = strip_tags($columnsArray->email);
		    $part_grp = htmlspecialchars($columnsArray->activity);
		    $part_lcn = htmlspecialchars($columnsArray->location);
		    $part_status_id = ($columnsArray->status)?$columnsArray->status:$this->getStatus();
		 
		    // bind values
		    $stmt->bindParam(":name", $part_names, PDO::PARAM_STR, 200);
		    $stmt->bindParam(":description", $description, PDO::PARAM_STR, 200);
			$stmt->bindParam(":email", $part_emails, PDO::PARAM_STR, 200);
		    $stmt->bindParam(":group_id", $part_grp);
		    $stmt->bindParam(":location_id", $part_lcn);
		    $stmt->bindParam(":status_id", $part_status_id);
		    	
	    	//$this->orderId = $this->conn->lastInsertId(); // Get last Inserted Id

	     	if($stmt->execute()){
	     		$affected_rows = $stmt->rowCount();
	     		$response["status"] = "success";
	            $response["message"] = $affected_rows." row inserted into database";
	            $response["data"] = 0;
	     	} else {
	     		$response["status"] = "success";
	            $response["message"] = $stmt->errorInfo();
	            $response["data"] = 0;
	     	}
		    	
		    	
		    } catch(PDOException $e) {
		    	$response["status"] = "error";
	            $response["message"] = 'Insert Failed: ' .$e->getMessage();
	            $response["data"] = 0;
			}
			return $response;
		}

		/*For Admin Actions*/
		// read orders
		function get($part_id) {
			try{
		    // select all query
	            $query = "SELECT scl_pr.id AS id, scl_pr.name AS name, scl_pr.email AS email, scl_pr.description AS description, scl_pr.group_id AS group_id, scl_pr.location_id AS location_id, scl_pr.status_id AS status_id \n"
				    . "FROM ".$this->participants_table_name." AS scl_pr \n"
					. "WHERE scl_pr.id = ".$part_id." \n"
				    . "";
				//echo $query;die;
	            $stmt = $this->conn->prepare( $query );
	            $stmt->execute();
	            $rows = $stmt->fetch(PDO::FETCH_ASSOC);

	            if(count($rows)<=0){
	                $response["status"] = "warning";
	                $response["message"] = "No data found.";
	            }else{
	                $response["status"] = "success";
	                $response["message"] = "Data selected from database";
	            }
	            	
                $response["data"] = $rows;
                //$response["data"]["items"] = json_decode($rows["items"]);
	        }catch(PDOException $e){
	            $response["status"] = "error";
	            $response["message"] = 'Select Failed: ' .$e->getMessage();
	            $response["data"] = null;
	        }
	        return $response;
		}

		// create participants
		function update($columnsArray, $requiredColumnsArray){
			$this->verifyRequiredParams($columnsArray, $requiredColumnsArray);
		    try {
		    // query to insert record
		    $query = "UPDATE 
		                " . $this->participants_table_name . "
		            SET 
		                name = :name, description = :description, email=:email, group_id=:group_id, location_id=:location_id, status_id=:status_id
		            WHERE id = :id";

		     
		    // prepare query
		    $stmt = $this->conn->prepare($query);
		 
		    // posted values
		    $part_names = strip_tags($columnsArray->name);
		    $description = strip_tags($columnsArray->description);
		    $part_emails = strip_tags($columnsArray->email);
		    $part_grp = htmlspecialchars($columnsArray->group_id);
		    $part_lcn = htmlspecialchars($columnsArray->location_id);
		    $part_status_id = ($columnsArray->status_id)?$columnsArray->status_id:$this->getStatus();
		 
		    // bind values
		    $stmt->bindParam(":id", $columnsArray->id);
		    $stmt->bindParam(":name", $part_names, PDO::PARAM_STR, 200);
		    $stmt->bindParam(":description", $description, PDO::PARAM_STR, 200);
			$stmt->bindParam(":email", $part_emails, PDO::PARAM_STR, 200);
		    $stmt->bindParam(":group_id", $part_grp);
		    $stmt->bindParam(":location_id", $part_lcn);
		    $stmt->bindParam(":status_id", $part_status_id);
		    	
	    	//$this->orderId = $this->conn->lastInsertId(); // Get last Inserted Id

	     	if($stmt->execute()){
	     		$affected_rows = $stmt->rowCount();
	     		$response["status"] = "success";
	            $response["message"] = $affected_rows." row inserted into database";
	            $response["data"] = 0;
	     	} else {
	     		$response["status"] = "success";
	            $response["message"] = $stmt->errorInfo();
	            $response["data"] = 0;
	     	}
		    	
		    	
		    } catch(PDOException $e) {
		    	$response["status"] = "error";
	            $response["message"] = 'Insert Failed: ' .$e->getMessage();
	            $response["data"] = 0;
			}
			return $response;
		}

		function delete($table, $where){
	        if(count($where)<=0){
	            $response["status"] = "warning";
	            $response["message"] = "Delete Failed: At least one condition is required";
	        }else{
	            try{
	                $a = array();
	                $w = "";
	                foreach ($where as $key => $value) {
	                    $w .= " and " .$key. " = :".$key;
	                    $a[":".$key] = $value;
	                }
	                $stmt =  $this->conn->prepare("DELETE FROM $table WHERE 1=1 ".$w);
	                $stmt->execute($a);
	                $affected_rows = $stmt->rowCount();
	                if($affected_rows<=0){
	                    $response["status"] = "warning";
	                    $response["message"] = "No row deleted";
	                }else{
	                    $response["status"] = "success";
	                    $response["message"] = $affected_rows." row(s) deleted from database";
	                }
	            }catch(PDOException $e){
	                $response["status"] = "error";
	                $response["message"] = 'Delete Failed: ' .$e->getMessage();
	            }
	        }
	        return $response;
	    }
	    /*Start : For Voting Functionality*/
	    function searchEmails($email) {
			try {
			$query = "SELECT email FROM ".$this->user_table_name." WHERE email LIKE '%$email%'";
			$stmt = $this->conn->prepare( $query );
	            $stmt->execute();
	            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	            if(count($rows)<=0){
	                $response["status"] = "warning";
	                $response["message"] = "No data found.";
	            }else{
	                $response["status"] = "success";
	                $response["message"] = "Data selected from database";
	            }
	            	
                $response["data"] = $rows;
	        }catch(PDOException $e){
	            $response["status"] = "error";
	            $response["message"] = 'Select Failed: ' .$e->getMessage();
	            $response["data"] = null;
	        }
	        return $response;
		} //searchEmails

		function verifyuser($email,$emp_id) {
			try {
			$query = "SELECT * FROM ".$this->user_table_name." WHERE email = '$email' AND emp_id='$emp_id'";
			$stmt = $this->conn->prepare( $query );
	            $stmt->execute();
				$num = $stmt->rowCount();
	            $rows = $stmt->fetch(PDO::FETCH_ASSOC);
	            if($num==0){
	                $response["status"] = false;
	                $response["message"] = "No Matches Found. Please contact to HR.";
	            }else{
	                $response["status"] = true;
	                $response["message"] = "You'r a verified user. Please select you candidate from performance category.";
	            }
	            	
                $response["data"] = $rows;
	        }catch(PDOException $e){
	            $response["status"] = false;
	            $response["message"] = 'Select Failed: ' .$e->getMessage();
	            $response["data"] = null;
	        }
	        return $response;
		}

		function verifyparticipant($group_id,$part_id) {
			try {
			$query = "SELECT * FROM ".$this->participants_table_name." WHERE id = '$part_id' AND group_id='$group_id'";
			$stmt = $this->conn->prepare( $query );
	            $stmt->execute();
				$num = $stmt->rowCount();
	            $rows = $stmt->fetch(PDO::FETCH_ASSOC);
	            if($num==0){
	                $response["status"] = "error";
	                $response["message"] = "No Matches Found. Please contact to HR.";
	            }else{
	                $response["status"] = "success";
	                $response["message"] = "Verified Participant.";
	            }
	            	
                $response["data"] = $rows;
	        }catch(PDOException $e){
	            $response["status"] = "error";
	            $response["message"] = 'Select Failed: ' .$e->getMessage();
	            $response["data"] = null;
	        }
	        return $response;
		}

		function getparticipants($group_id) {
			try {
			$query =  $query = "SELECT scl_pr.id AS id, scl_pr.name AS pname, scl_lc.name AS ln \n"
				    . "FROM ".$this->participants_table_name." AS scl_pr \n"
					. "JOIN ".$this->group_table_name." AS scl_gr ON scl_pr.group_id = scl_gr.id \n"
				    . "JOIN ".$this->location_table_name." AS scl_lc ON scl_pr.location_id = scl_lc.id \n"
					. "WHERE scl_gr.id = '$group_id'";
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
                //$response["data"]["items"] = json_decode($rows["items"]);
	        }catch(PDOException $e){
	            $response["status"] = "error";
	            $response["message"] = 'Select Failed: ' .$e->getMessage();
	            $response["data"] = null;
	        }
	        return $response;
		}
		function verifyEntryForVoting($user_id, $email, $group_id) {
			try {
			$query = "SELECT * FROM ".$this->voting_table_name." WHERE user_id='$user_id' AND email='$email' AND group_id='$group_id'";
			$stmt = $this->conn->prepare( $query );
	            $stmt->execute();
				$num = $stmt->rowCount();
	            $rows = $stmt->fetch(PDO::FETCH_ASSOC);
	            if($num==0){
	                $response["status"] = "error";
	                $response["message"] = "No Match Found.";
	                $response["data"] = null;
	            } else {
	                $response["status"] = "success";
	                $response["message"] = "Entry exists.";
	                $response["data"] = $rows;
	            }
	        }catch(PDOException $e){
	            $response["status"] = "exception";
	            $response["message"] = 'Select Failed: ' .$e->getMessage();
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
		                $response["message"] = "No voting found under respective participant.";
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
		function castvote($columnsArray, $requiredColumnsArray) {
			$this->verifyRequiredParams($columnsArray, $requiredColumnsArray);
			try {
		    // First to verify user identity
				$user_response = $this->verifyuser($columnsArray->email, $columnsArray->emp_id);
				if($user_response['status']=='success') {
					$user_id = $user_response['data']['id'];
					$email = $user_response['data']['email'];
				} else {
					return $user_response;
				}
			// Second to verify participant identity
				$part_response = $this->verifyparticipant($columnsArray->group_id, $columnsArray->part_id);
				if($part_response['status']=='success') {
					$part_id = $part_response['data']['id'];
					$group_id = $part_response['data']['group_id'];
				} else {
					return $part_response;
				}
			// Final : To cast vote
				/*
					If the user already voted for particular group then update the entries otherwise add new entry
				*/
				$entry_response = $this->verifyEntryForVoting($user_id, $email, $group_id);
				if($entry_response['status']=='success') { //Entry already into table
					//print_r();die;
					$participant_id = $entry_response['data']['part_id'];
					$res_up_part_vc = $this->updateVotingUnderParticipantTable($participant_id,'minusone');
					if($res_up_part_vc['status']=='success') {
						$query = "UPDATE
		                " . $this->voting_table_name . "
			            SET part_id=:part_id WHERE 
			                user_id=:user_id AND email=:email AND group_id=:group_id";
			            } else {
			            	return $$res_up_part_vc;
			            }
				} else {
					if($entry_response['status']=='error') {
						$query = "INSERT INTO 
		                " . $this->voting_table_name . "
			            SET 
			                user_id=:user_id, email=:email, part_id=:part_id, group_id=:group_id";
					} else {
						return $entry_response;
					}
				}
		    //echo $query;die;
		    // prepare query
		    $stmt = $this->conn->prepare($query);

		    // bind values
		    $stmt->bindParam(":user_id", $user_id);
		    $stmt->bindParam(":email", $email);
		    $stmt->bindParam(":part_id", $part_id);
		    $stmt->bindParam(":group_id", $group_id);
		    	
	    	//$this->orderId = $this->conn->lastInsertId(); // Get last Inserted Id

	     	if($stmt->execute()){
	     		$affected_rows = $stmt->rowCount();
	     		/*Update Voting count to participant table*/
	     		$query = "SELECT COUNT(*) AS vc FROM ".$this->voting_table_name." WHERE part_id='$part_id'";
	     		$stmt = $this->conn->prepare( $query );
	            $stmt->execute();
	            $rows = $stmt->fetch(PDO::FETCH_ASSOC);
	            $voting_count = $rows['vc'];
	            //Update voting count to participants table
	            $query_to_update_vc = "UPDATE ".$this->participants_table_name." SET vc='$voting_count' WHERE id='$part_id'";
	            $stmt_to_update_vc = $this->conn->prepare( $query_to_update_vc );
	     		
	     		if($stmt_to_update_vc->execute()){
	               	$response["status"] = "success";
		            $response["message"] = "Voting updated successfully.";
	            } else {
		            $response["status"] = "error";
	                $response["message"] = "Voting count not updated under participant table.";
	            }
	            /*End Of code to update count*/
	            $response["status"] = "success";
	            $response["message"] = $affected_rows." row(s) updated to database";
	     	} else {
	     		$response["status"] = "success";
	            $response["message"] = $stmt->errorInfo();
	     	}
		    } catch(PDOException $e) {
		    	$response["status"] = "error";
	            $response["message"] = 'Something Went Wrong: ' .$e->getMessage();
			}
			$response["data"] = null;
			return $response;
		}
		/*End : For Voting Functionality*/

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