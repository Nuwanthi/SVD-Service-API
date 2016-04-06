<?php
require_once '../../model/commen/PassHash.php';
/**
 * Class to handle all the prefect possition details
 * This class will have CRUD methods for prefect_possitions
 *
 * @author Bagya
 *
 */

class PrefectPossitionManagement {

    private $conn;

    function __construct() {
        require_once '../../model/commen/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }
	
	
/*
 * ------------------------ TALANTS TABLE METHODS ------------------------
 */

    /**
     * Creating new prefect possitions
     *
     * @param String $pos_name possition name for the system
	 * @param String $recode_added_by 
     *
     * @return database transaction status
     */
    public function createPrefectPossition($pos_name,$recode_added_by ) {

		
        $response = array();
		
        // First check if possition already existed in db
        if (!$this->isPossitionExists($pos_name)) {
  
            // insert query
			 $stmt = $this->conn->prepare("INSERT INTO prefect_possition	 (pos_name, recode_added_by) values(?, ?)");
			 $stmt->bind_param("si", $pos_name, $recode_added_by );
			 $result = $stmt->execute();

			 $stmt->close();

        } else {
            // Possitions is not already existed in the db
            return ALREADY_EXISTED;
        }
		
         

        // Check for successful insertion
        if ($result) {
			// possition successfully inserted
            return CREATED_SUCCESSFULLY;
        } else {
            // Failed to create possition
            return CREATE_FAILED;
        }
        
		return $response;

    }
	
	
	
/**
     * Delete prefect possition
     *
     * @param String $tal_name Talant name for the system
	 * @param String $recode_added_by
     *
     * @return database transaction status
     */
    public function deletePrefectPossition($pos_name, $recode_added_by) {

		
        $response = array();
        // First check if possition already existed in db
        if ($this->isPossitionExists($pos_name)) {
           			
			//
			$stmt = $this->conn->prepare("UPDATE prefect_possition set status = 3, recode_modified_at = now() , recode_modified_by = ? where pos_name = ? and (status=1 or  status=2)");
			$stmt->bind_param("is",$recode_added_by, $pos_name);
			$result = $stmt->execute();
			
            $stmt->close();

        } else {
            // Possition is not already existed in the db
            return NOT_EXISTED;
        }
		
         

        // Check for successful insertion
        if ($result) {
			// possition successfully deleted
            return DELETE_SUCCESSFULLY;
        } else {
            // Failed to delete possition
            return DELETE_FAILED;
        }
        
		return $response;

    }
	  
	/**
     * Fetching talants by tal_name
	 *
     * @param String $tal_name tal name
	 *
	 *@return talant object only needed data
     */
    public function getTalantByTalantName($tal_name) {
        $stmt = $this->conn->prepare("SELECT tal_name, status, recode_added_at, recode_added_by FROM talants WHERE tal_name = ? and (status=1 or status=2)");
        $stmt->bind_param("s", $tal_name);
        if ($stmt->execute()) {
            $stmt->bind_result($tal_name,$status, $recode_added_at, $recode_added_by);
            $stmt->fetch();
            $talant = array();
            $talant["tal_name"] = $tal_name;
            $talant["status"] = $status;
            $talant["recode_added_at"] = $recode_added_at;
			$talant["recode_added_by"] = $recode_added_by;

            $stmt->close();
            return $talant;
        } else {
            return NULL;
        }
    }
  
  
	/**
     * Fetching all talants
	 *
     * @return $talant object set of all talants
     */
    public function getAllTalants() {
        $stmt = $this->conn->prepare("SELECT * FROM talants WHERE status = 1 or  status = 2");
        $stmt->execute();
        $talants = $stmt->get_result();
        $stmt->close();
        return $talants;
    }
	
  
  
  
  
  
/*
 * ------------------------ SUPPORTIVE METHODS ------------------------
 */

	/**
     * Checking for duplicate possitions by pos_name
     *
     * @param String $pos_name Possition name to check in db
     *
     * @return boolean
     */
    private function isPossitionExists($pos_name) {
		$stmt = $this->conn->prepare("SELECT pos_name from prefect_possition WHERE (status = 1 or status = 2)  and pos_name = ?  ");
        $stmt->bind_param("s",$pos_name);
        $stmt->execute();
		$stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return ($num_rows > 0); //if it has more than zero number of rows; then  it sends true
    }

}

?>
