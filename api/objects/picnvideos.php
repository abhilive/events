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
		function dumpAll($columnsArray, $requiredColumnsArray) {
			//var_dump($columnsArray);
			$this->verifyRequiredParams($columnsArray, $requiredColumnsArray);
			try{
				$event_id = $this->getEventId($columnsArray->location, $columnsArray->forEvent);
				echo 'Event Id :'.$event_id.'<br />';
				$dir = $_SERVER['DOCUMENT_ROOT'].'/sclapp/images/'.$columnsArray->location.'/'.$columnsArray->forEvent.'/';
				$file_arr = array();
				//echo $dir;die;
				if (is_dir($dir)){
				  if ($dh = opendir($dir)){
				  	//first empty table using event_id
				  	$this->emptyTable($this->photos_table_name, $event_id);
				    while (($file = readdir($dh)) !== false){
				    	 if (!in_array($file,array(".",".."))) {
				      		//$file_arr[] = array('custom'=>$file,'thumbnail'=>$file);
				      		$file_path = 'images/'.$columnsArray->location.'/'.$columnsArray->forEvent.'/'.$file;
				      		$this->insertintotable($event_id,$file_path);
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

		function getEventId($location_name, $event_name) {
			try {
				//first get the valid location id
				$query = "SELECT id \n"
					. "FROM ".$this->location_table_name." \n"
				    . "WHERE name = '".$location_name."' \n"
				    . "";
				//echo $query;die;
				$stmt = $this->conn->prepare( $query );
            	$stmt->execute();
            	$row = $stmt->fetch(PDO::FETCH_ASSOC);
            	if(count($row)<=0){
	                echo "No location found.";die;
	            }else{
	                $loc_id = $row['id'];
	            }

	            $query = "SELECT id \n"
					. "FROM ".$this->core_table_name." \n"
				    . "WHERE location_id = ".$loc_id." AND type = '".$event_name."' \n"
				    . "";
				//echo $query;die;
				$stmt = $this->conn->prepare( $query );
            	$stmt->execute();
            	$row = $stmt->fetch(PDO::FETCH_ASSOC);
            	//var_dump($row);die;
            	if(count($row)<=0){
	                echo "No event found.";die;
	            }else{
	                $event_id = $row['id'];
	            }
			} catch(PDOException $e) {
	            echo 'Insert Failed: ' .$e->getMessage();die;
			}
			return $event_id;
		}

		function emptyTable($_table, $event_id) {
			try{
				$query = "DELETE FROM ".$_table." WHERE under = ".$event_id;
				//echo $query;die;
				$stmt = $this->conn->prepare( $query );
			    if(!$stmt->execute()){
			     	echo $stmt->errorInfo();die;
			    }
			} catch(PDOException $e) {
	            echo 'Deletion Failed: ' .$e->getMessage();die;
			}
			echo "Deletion Success".'<br />';
			return true;
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
		function getAllPics($columnsArray, $requiredColumnsArray) {
			$this->verifyRequiredParams($columnsArray, $requiredColumnsArray);
			//print_r($columnsArray);die;
			$lcn = $columnsArray->location; //ex-Nagpur
			$forEvent = $columnsArray->forEvent; //ex-Auditions
			try {
				$locations = array('Nagpur'=>1,'Mohali'=>2,'Dehradun'=>3);
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