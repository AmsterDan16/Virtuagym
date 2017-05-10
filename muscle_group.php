<?php

include_once "db.php";

/**
 * Muscle Group class
 */
class Muscle_group{
    
    var $id;
    var $name;
    var $con;
    
    /**
     * creates muscle group object and sets up connection for the instance
     * @return
     */
    function __construct(){
        $this->con = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
        if (!$this->con) {
            die('Could not connect: ' . mysqli_error($this->con));
        }
    }
    
    function get_name() {
        return $this->name;
    }

    function get_id(){
        return $this->id;   
    }
    
    /**
     * retrieves muscle groups from database
     * @return
     */
    public function select(){
        $sql = "SELECT id, name FROM muscle_groups;";
    
        $arr = array();
        if($result = mysqli_query($this->con, $sql)){
            while($row = mysqli_fetch_assoc($result)) {
                $arr[] = $row;
            }
            echo json_encode($arr);
        }

        mysqli_close($this->con);
    }
}

?>