<?php

include_once "db.php";

/**
 * Exercise class
 */
class Exercise{
    
    var $id;
    var $name;
    var $muscle_group;
    var $con;
    
    /**
     * creates exercise object, sets up connection for the instance, and sets defaults
     * @return
     */
    function __construct(){
        $this->con = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
        if (!$this->con) {
            die('Could not connect: ' . mysqli_error($this->con));
        }
        //set defaults
        $this->name = "";
        $this->muscle_group = -1;
    }
    
    function set_name($new_name) {
        $this->name = $new_name;
    }
    function get_name() {
        return $this->name;
    }
    function set_muscle_group($new_muscle_group){
        $this->muscle_group = $new_muscle_group;
    }
    function get_muscle_group(){
        return $this->muscle_group;   
    }
    
    /**
     * inserts new exercise into database
     * @return
     */
    public function insert(){
        if($this->muscle_group == -1 || $this->name == ""){
            echo "Error: exercise not set correctly. Please make sure the form has been filled out correctly and try again."; 
        }else{
            $sql = "INSERT INTO exercises (muscle_group_id, exercise_name) VALUES (" . $this->muscle_group . ", '" . $this->name ."');";

            if(mysqli_query($this->con, $sql)){
                echo $this->name . " added to exercise options!";
            }else{
                echo "Error: " . $sql . "<br>" . mysqli_error($this->con);  
            }
        }
        mysqli_close($this->con);
    }
    
    /**
     * retrieves exercises from database
     * @return
     */
    public function select(){
        $sql = "SELECT id, exercise_name FROM exercises;";
    
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