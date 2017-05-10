<?php

include_once "db.php";

/**
 * workout class
 */
class Workout{
    
    var $id;
    var $name;
    var $user_id;
    var $days;
    var $con;
   
    /**
     * creates workout object, sets up connection for the instance, and sets defaults
     * @return
     */
    function __construct(){
        $this->set_connection();
        
        $this->id = -1;
        $this->user_id = -1;
        $this->name = "";
        $this->days = array();
    }
    
    /**
     * parameterized constructor for workouts
     * @param {String} workout name
     * @param {Integer} user id
     * @param {Object[]} array of day objects
     * @return instance of workout class
     */
    public static function create_workout($name, $user, $days){
        $new_workout = new self();
        $new_workout->set_name($name);
        $new_workout->set_user_id($user);
        $new_workout->set_days($days);
        
        return $new_workout;
    }
    
    function set_id($new_id) {
        $this->id = $new_id;
    }
    function get_id() {
        return $this->id;
    }
    function set_name($new_name) {
        $this->name = $new_name;
    }
    function get_name() {
        return $this->name;
    }
    function set_user_id($new_user_id) {
        $this->user_id = $new_user_id;
    }
    function get_user_id() {
        return $this->user_id;
    }
    function set_days($new_days) {
        $this->days = $new_days;
    }
    function get_days() {
        return $this->days;
    }
    
    /**
     * sets up the database connection since it is needed in other places besides constructor
     * @return
     */
    private function set_connection(){
        $this->con = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
        if (!$this->con) {
            die('Could not connect: ' . mysqli_error($this->con));
        }
    }
    
    /**
     * inserts new workout into database
     * @return
     */
    public function insert(){
        $sql = "INSERT INTO workouts (name) VALUES ('" . $this->name . "');";
        $sql .= "INSERT INTO user_workouts (plan_id, user_id) VALUES (LAST_INSERT_ID(), " . $this->user_id . ");";

        if(mysqli_multi_query($this->con, $sql)){
            $new_plan_id = mysqli_insert_id($this->con);
            $this->insert_workout_days($new_plan_id);
        }else{
            echo "Error: " . $sql . "<br>" . mysqli_error($this->con); 
            mysqli_close($this->con);
        }
    }
    
    /**
     * deletes workout from database
     * @return
     */
    public function delete(){
        //delete all traces of the plan
        $sql = "DELETE FROM workout_exercises WHERE plan_id=".$this->id.";";
        $sql .= "DELETE FROM days WHERE plan_id=".$this->id.";";
        $sql .= "DELETE FROM user_workouts WHERE plan_id=".$this->id.";";
        $sql .= "DELETE FROM workouts WHERE id=".$this->id.";";

        if(mysqli_multi_query($this->con, $sql)){
            echo "Workout successfully removed.";
        }else{
            echo "Error: " . $sql . "<br>" . mysqli_error($this->con); 
        }

        mysqli_close($this->con);
    }
    
    /**
     * inserts workout days into database
     * {Integer} plan id
     * @return
     */
    public function insert_workout_days($plan_id){
        //must reset connection as it was losing scope
        $this->set_connection();
        $exercises = array();

        foreach($this->days as $day){
            $name = $day['day_name']; 
            $exercises = $day['exercises'];

            $sql = "INSERT INTO days (day_name, plan_id) VALUES ('".$name."',".$plan_id.");";

            if(mysqli_query($this->con, $sql)){
                $day_id = mysqli_insert_id($this->con);
                
                $sql="INSERT INTO workout_exercises (exercise_id, plan_id, day_id) VALUES";
                foreach($exercises as $exercise){
                    $sql.=" (".$exercise.",".$plan_id.",".$day_id."),";   
                }
                $sql = rtrim($sql,',') . ";";
                mysqli_multi_query($this->con, $sql);
            }else{
                echo "connection lost";   
            }
        }
        mysqli_close($this->con);       
    }
    
    /**
     * retrieves workout plans for display on main page
     * @return
     */
    public function select_plans(){
        $sql = "SELECT workouts.name AS 'plan_name', workouts.id AS 'plan_id', GROUP_CONCAT(DISTINCT muscles.name) AS 'muscles' 
                FROM workout_exercises wk_exercise
                LEFT JOIN workouts 
                ON wk_exercise.plan_id = workouts.id
                LEFT JOIN exercises
                ON exercises.id = wk_exercise.exercise_id
                LEFT JOIN muscle_groups muscles
                ON muscles.id = exercises.muscle_group_id
                GROUP BY workouts.name, workouts.id";
    
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
     * retrieves workout plan details for specific workout
     * @return
     */
    public function select_plan_details(){
        $sql = "SELECT workouts.name, wk_exercise.exercise_id, wk_exercise.day_id, users.first_name, 
             users.last_name, days.day_name, exercises.exercise_name
            FROM workout_exercises wk_exercise
            LEFT JOIN workouts 
             ON wk_exercise.plan_id = workouts.id
            LEFT JOIN user_workouts 
             ON user_workouts.plan_id = workouts.id
            LEFT JOIN users
             ON users.id = user_workouts.user_id
            LEFT JOIN exercises
             ON exercises.id = wk_exercise.exercise_id
            LEFT JOIN days
             ON wk_exercise.day_id = days.id
            WHERE workouts.id=" . $this->id . "
            ORDER BY days.id;"; 

        $arr = array();
        if($result = mysqli_query($this->con, $sql)){
            while($row = mysqli_fetch_assoc($result)) {
                $arr[] = $row;
            }
            echo json_encode($arr);
        }else{
            echo "Error: " . $sql . "<br>" . mysqli_error($this->con);    
        }

        mysqli_close($this->con);
    }
}

?>