<?php

include_once "db.php";

/**
 * User class
 */
class User{
    
    var $id;
    var $con;
    
    /**
     * creates user object, sets up connection for the instance, and sets defaults
     * @return
     */
    function __construct(){
        $this->con = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
        if (!$this->con) {
            die('Could not connect: ' . mysqli_error($this->con));
        }
        //set defaults
        $this->id = -1;
    }
    
    function set_id($new_id) {
        $this->id = $new_id;
    }
    function get_id() {
        return $this->id;
    }
    
    /**
     * retrieves users from database
     * @return
     */
    public function select(){
        $sql = "SELECT id, concat(first_name,' ',last_name) AS name FROM users;";
        $arr = array();
        if($result = mysqli_query($this->con, $sql)){
            while($row = mysqli_fetch_assoc($result)) {
                $arr[] = $row;
            }
            echo json_encode($arr);
        }

        mysqli_close($this->con);
    }
    
    /**
     * notifies user of change to the plan they own
     * @return
     */
    public function notify_user(){
        $sql = "SELECT email FROM users WHERE id=" .$this->id. ";";

        if($result = mysqli_query($this->con, $sql)){
            $row = mysqli_fetch_assoc($result);
            $msg = "Your workout plan has been added! Thanks for using the site.\n -Virtuagym";
            mail($row["email"], "New Workout Plan", $msg);
            echo "An email has been sent to " . $row["email"];
        }else{
            echo "Error occurred.";   
        }

        mysqli_close($this->con);
    }
}

?>