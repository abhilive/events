<?php 
	class Participants {
		// database connection and table name 
		private $conn; 
		//private $table_prefix = ''; //Not In Use

		private $participants_table_name = "participants";
		private $group_table_name = "group_type";
		private $location_table_name = "location";
		private $status_table_name = "statuses";
		private $user_table_name = "user";
		// object properties 

		private $_order = array();
		private $_status = 5; //Set Default Status to garbage value
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
                //$response["data"]["items"] = json_decode($rows["items"]);
	        }catch(PDOException $e){
	            $response["status"] = "error";
	            $response["message"] = 'Select Failed: ' .$e->getMessage();
	            $response["data"] = null;
	        }
	        return $response;
		} //verifyuser
		function verifyuser($email,$emp_id) {
			try {
			$query = "SELECT * FROM ".$this->user_table_name." WHERE email = '$email' AND emp_id='$emp_id'";
			$stmt = $this->conn->prepare( $query );
	            $stmt->execute();
				$num = $stmt->rowCount();
	            $rows = $stmt->fetch(PDO::FETCH_ASSOC);
	            if($num==0){
	                $response["status"] = "error";
	                $response["message"] = "No Matches Found. Please contact HR.";
	            }else{
	                $response["status"] = "success";
	                $response["message"] = "You'r a verified user. Please select you candidate from performance category.";
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
		// read orders
		function load($part_id) {
			try{
		    // select all query
	            $query = "SELECT scl_pr.id AS id, scl_pr.name AS participant_name, scl_pr.name AS group_name, scl_lc.name AS location, scl_st.title AS status \n"
				    . "FROM ".$this->participants_table_name." AS scl_pr \n"
					. "JOIN ".$this->group_table_name." AS scl_gr ON scl_pr.group_id = scl_gr.id \n"
				    . "JOIN ".$this->location_table_name." AS scl_lc ON scl_pr.location_id = scl_lc.id \n"
				    . "JOIN ".$this->status_table_name." AS scl_st ON scl_pr.status_id = scl_st.id \n"
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
    		$json_params = $this->generateJson(array('id'=>'scl_pr.id','nm'=>'scl_pr.name','lc'=>'scl_lc.name','st'=>'scl_st.title'),'prts');
		    
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
				    . "GROUP BY scl_gr.id ORDER BY name ASC\n"
				    . "";
			//echo $query;die;
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
		function add($params){
		    try {
		    // query to insert record
		    $query = "INSERT INTO 
		                " . $this->participants_table_name . "
		            SET 
		                name = :name, email=:email, group_id=:group_id, location_id=:location_id, status_id=:status_id";
		     
		    // prepare query
		    $stmt = $this->conn->prepare($query);
		 
		    // posted values
		    $part_names = htmlspecialchars(strip_tags($params['name']));
		    $part_emails = strip_tags($params['email']);
		    $part_grp = htmlspecialchars($params['activity']);
		    $part_lcn = htmlspecialchars($params['location']);
		    $part_status_id = $this->getStatus();
		 
		    // bind values
		    $stmt->bindParam(":name", $part_names, PDO::PARAM_STR, 200);
			$stmt->bindParam(":email", $part_emails, PDO::PARAM_STR, 200);
		    $stmt->bindParam(":group_id", $part_grp);
		    $stmt->bindParam(":location_id", $part_lcn);
		    $stmt->bindParam(":status_id", $part_status_id);

		     	if($stmt->execute()){
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

	}