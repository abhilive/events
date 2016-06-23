<?php 
	class User {
		// database connection and table name 
		private $conn; 
		//private $table_prefix = ''; //Not In Use

		private $user_table_name = "admin_user";
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
		function login($where) {
			$data = array();
			if(count($where)<=0){
				$response["data"] = $data;
	            $response["status"] = "warning";
	            $response["message"] = "Select Failed: Argument missing";
	        }else{
	            try{
	                $a = array();
	                $w = "";
	                foreach ($where as $key => $value) {
	                    $w .= " and " .$key. " = :".$key;
	                    $a[":".$key] = $value;
	                }
	                $stmt =  $this->conn->prepare("SELECT * FROM $this->user_table_name WHERE 1=1 ".$w); //removed  after WHERE
	                //echo $stmt;die;
	                $stmt->execute($a);
	                //$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	                $affected_rows = $stmt->rowCount();
	                if($affected_rows<=0){
	                	$response["data"] = $data;
	                    $response["status"] = "error";
	                    $response["message"] = "Invalid Credentials";
	                }else{
	                	$data = array('access_token'=>'adminuser_login','userName'=>$where['username']);
	                	$response["data"] = $data;
	                    $response["status"] = "success";
	                    $response["message"] = "Logged In Successful";
	                }
	            }catch(PDOException $e){
	            	$response["data"] = $data;
	                $response["status"] = "error";
	                $response["message"] = 'Select Failed: ' .$e->getMessage();
	            }
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

	}