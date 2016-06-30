<?php 
	class Picnvideos {
		// database connection and table name 
		private $conn; 
		//private $table_prefix = ''; //Not In Use

		private $core_table_name = "photosnvideos";
		private $photos_table_name = "photos";
		private $videos_table_name = "videos";
		private $location_table_name = "location";
		// object properties 

		private $_status = 0;
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
		function dumpAll() {
			try{

				$dir = $_SERVER['DOCUMENT_ROOT'].'/events/images/nagpur/semifinals/';
				$file_arr = array();
				if (is_dir($dir)){
					//Die('this');
				  if ($dh = opendir($dir)){
				    while (($file = readdir($dh)) !== false){
				    	 if (!in_array($file,array(".",".."))) {
				      		//$file_arr[] = array('custom'=>$file,'thumbnail'=>$file);
				      		$this->insertintotable('1',$file);
				    	 }
				    }
				    closedir($dh);
				  }
				}
				Die('done.');
		    } catch(PDOException $e) {
		    	$response["status"] = "error";
	            $response["message"] = 'Insert Failed: ' .$e->getMessage();
	            $response["data"] = 0;
			}
			return $response;
		}

		function insertintotable($under, $file_path) {
			/*echo $under;
			echo $file_path;die;*/
			try{
				$query = "INSERT INTO
		                " . $this->photos_table_name . "
		            SET 
		                under = :under, full=:full, thumbnail=:thumbnail";
		     
		    // prepare query
		    $stmt = $this->conn->prepare($query);
		 
		    // posted values
		    $full = $thumbnail = $file_path;
		    //echo $content;die;
		 
		    // bind values
		    $stmt->bindParam(":under", $under);
		    $stmt->bindParam(":full", $full);
		    $stmt->bindParam(":thumbnail", $thumbnail);

		    if(!$stmt->execute()){
		     	echo $stmt->errorInfo();die;
		    }
			} catch(PDOException $e) {
	            echo 'Insert Failed: ' .$e->getMessage();die;
			}
		}
		//For Admin Select to show records
		function getAllPics($params) {
			$lcn = $params['location']; //ex-Nagpur
			$forEvent = $params['forEvent']; //ex-Auditions
			try {
				$locations = array('nagpur'=>1,'mohali'=>2,'dehradun'=>3);
				$events = array('auditions','semifinals','finals');
				//echo $forEvent;
				//echo in_array($forEvent, $events);
				//echo $locations[$lcn];die;
				if(($locations[$lcn]>0 && $locations[$lcn]<4) && in_array($forEvent, $events)) {
					$loc_id = $locations[$lcn];

					$query = "SELECT scl_ph.full AS full_path, scl_ph.thumbnail AS thumbnail \n"
					. "FROM ".$this->photos_table_name." AS scl_ph \n"
					. "JOIN ".$this->core_table_name." AS scl_pnv ON scl_ph.under = scl_pnv.id \n"
				    . "WHERE scl_pnv.location_id = ".$loc_id." AND scl_pnv.type = '".$forEvent."' \n"
				    . "";
					/*$query = "SELECT scl_pnv.id \n"
				    . "FROM ".$this->core_table_name." AS scl_pnv \n"
				    . "WHERE scl_pnv.location_id = ".$loc_id." AND scl_pnv.type = '".$forEvent."' \n"
				    . "";*/
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
				} else {
					$response["status"] = "error";
	            	$response["message"] = 'Invalid Arguments';
	            	$response["data"] = null;
				}
				//Die('done.');
		    // select all query
				/*$query = "SELECT scl_ph.content AS imagePaths \n"
				    . "FROM ".$this->photos_table_name." AS scl_ph \n"
				    . "JOIN ".$this->location_table_name." AS scl_lc ON scl_ph.location_id = scl_lc.id \n"
				    . "";*/
				    /*$query = "SELECT scl_ph.full AS full_path, scl_ph.thumbnail AS thumbnail \n"
				    . "FROM ".$this->photos_table_name." AS scl_ph \n"*/
				    //. "JOIN ".$this->location_table_name." AS scl_lc ON scl_ph.location_id = scl_lc.id \n"
				    //. "";
	            /*$query = "SELECT scl_ph.id AS id, scl_ph.title AS title, scl_lc.name AS location, scl_ph.content AS content \n"
				    . "FROM ".$this->photos_table_name." AS scl_ph \n"
				    . "JOIN ".$this->location_table_name." AS scl_lc ON scl_ph.location_id = scl_lc.id \n"
				    . "GROUP BY scl_ph.id ORDER BY id ASC\n"
				    . "";*/
				//echo $query;die;
	            /*$stmt = $this->conn->prepare( $query );
	            $stmt->execute();
	            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	            
	            if(count($rows)<=0){
	                $response["status"] = "warning";
	                $response["message"] = "No data found.";
	            }else{
	                $response["status"] = "success";
	                $response["message"] = "Data selected from database";
	            }
	            	
                $response["data"] = $rows;*/
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

		    $query = "SELECT scl_gr.id, scl_gr.name, COUNT(scl_pr.id) as ttl_pr, ".$json_params." \n"
		    		. "FROM ".$this->group_table_name." AS scl_gr \n"
				    . "JOIN ".$this->participants_table_name." AS scl_pr ON scl_gr.id = scl_pr.group_id \n"
				    . "JOIN ".$this->location_table_name." AS scl_lc ON scl_pr.location_id = scl_lc.id \n"
				    . "JOIN ".$this->status_table_name." AS scl_st ON scl_pr.status_id = scl_st.id \n"
				    . "GROUP BY scl_gr.id ORDER BY name ASC\n"
				    . "";

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