<?php 
	class Participants {
		// database connection and table name 
		private $conn; 
		//private $table_prefix = ''; //Not In Use

		private $participants_table_name = "participants";
		private $group_table_name = "group_type";
		private $location_table_name = "location";
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

		function readAll() {
			try{
		    // select all query
	            $query = "SELECT scl_pr.id AS id, scl_pr.name AS participant_name, scl_gr.name AS group_name, scl_lc.name AS location \n"
				    . "FROM ".$this->participants_table_name." AS scl_pr \n"
				    . "JOIN ".$this->group_table_name." AS scl_gr ON scl_pr.group_id = scl_gr.id \n"
				    . "JOIN ".$this->location_table_name." AS scl_lc ON scl_pr.location_id = scl_lc.id GROUP BY scl_pr.id ORDER BY id DESC\n"
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

		// read All Group & participants
		function readAllGroups() {
			try{
		    $response = '';
    		$json_params = $this->generateJson(array('id'=>'scl_pr.id','name'=>'scl_pr.name','location'=>'scl_lc.name'),'participants');
		    
		    $query = "SELECT scl_gr.id, scl_gr.name, COUNT(scl_pr.id) as total_participants, ".$json_params." \n"
		    		. "FROM ".$this->group_table_name." AS scl_gr \n"
				    . "JOIN ".$this->participants_table_name." AS scl_pr ON scl_gr.id = scl_pr.group_id \n"
				    . "JOIN ".$this->location_table_name." AS scl_lc ON scl_pr.location_id = scl_lc.id GROUP BY scl_gr.id ORDER BY name ASC\n"
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
			        		'total_participants' => $_row['total_participants'],
			        		'participants' => json_decode($_row['participants']) // decode json data getting for items
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
		                name = :name, email=:email, group_id=:group_id, location_id=:location_id";
		     
		    // prepare query
		    $stmt = $this->conn->prepare($query);
		 
		    // posted values
		    $part_names = htmlspecialchars(strip_tags($params['name']));
		    $part_emails = htmlspecialchars(strip_tags($params['email']));
		    $part_grp = htmlspecialchars($params['activity']);
		    $part_lcn = htmlspecialchars($params['location']);
		 
		    // bind values
		    $stmt->bindParam(":name", $part_names, PDO::PARAM_STR, 200);
			$stmt->bindParam(":email", $part_emails, PDO::PARAM_STR, 200);
		    $stmt->bindParam(":group_id", $part_grp);
		    $stmt->bindParam(":location_id", $part_lcn);

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