<?php
    
$q = $_REQUEST["q"];
    
$con = mysqli_connect('localhost','root','root','Virtuagym');
if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}

switch($q){
    case "getPlans":
        GetWorkoutPlans($con);
        break;
    case "getUsers":
        GetUsers($con);
        break;
    case "getExercises":
        GetExercises($con);
        break;
    case "getMuscleGroups":
        GetMuscleGroups($con);
        break;
    case "submitWorkout":
        $workout = $_POST['workout'];
        SubmitWorkout($con, $workout);
        break;
    case "submitExercise":
        $muscle_id = $_POST['muscle_id'];
        $exercise = $_POST['exercise'];
        SubmitExercise($con, $muscle_id, $exercise);
        break;
    case "deleteWorkout":
        $plan_id = $_POST['plan_id'];
        DeleteWorkout($con, $plan_id);
        break;
    case "sendUserEmail":
        $user_id = $_POST['user_id'];
        NotifyUser($con, $user_id);
        break;
    case "getPlanDetails":
        $plan_id = $_GET['plan_id'];
        GetPlanDetails($con, $plan_id);
        break;
    default:
        echo "INVALID PARAMS";
}

/**
 * retrieves the basic workout information for the home page
 * @param {String} sql connection 
 * @return {json} json string of workouts
 */
function GetWorkoutPlans($con){
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
    if($result = mysqli_query($con, $sql)){
        while($row = mysqli_fetch_assoc($result)) {
            $arr[] = $row;
        }
        echo json_encode($arr);
    }
    
    mysqli_close($con);
}

/**
 * retrieves the users
 * @param {String} sql connection 
 * @return {json} json string of users
 */
function GetUsers($con){
    $sql = "SELECT id, concat(first_name,' ',last_name) AS name FROM users;";
    $arr = array();
    if($result = mysqli_query($con, $sql)){
        while($row = mysqli_fetch_assoc($result)) {
            $arr[] = $row;
        }
        echo json_encode($arr);
    }

    mysqli_close($con);
}

/**
 * retrieves the list of exercises
 * @param {String} sql connection 
 * @return {json} json string of exercises
 */
function GetExercises($con){
    $sql = "SELECT id, exercise_name FROM exercises;";
    
    $arr = array();
    if($result = mysqli_query($con, $sql)){
        while($row = mysqli_fetch_assoc($result)) {
            $arr[] = $row;
        }
        echo json_encode($arr);
    }
    
    mysqli_close($con);
}

/**
 * retrieves the list of muscle groups
 * @param {String} sql connection 
 * @return {json} json string of muscle groups
 */
function GetMuscleGroups($con){
    $sql = "SELECT id, name FROM muscle_groups;";
    
    $arr = array();
    if($result = mysqli_query($con, $sql)){
        while($row = mysqli_fetch_assoc($result)) {
            $arr[] = $row;
        }
        echo json_encode($arr);
    }
    
    mysqli_close($con);
}

/**
 * submits workout
 * @param {String} sql connection 
 * @param {json} json string
 * @return
 */
function SubmitWorkout($con, $workout_json){
    $workout = json_decode($workout_json, true);
    $sql = "INSERT INTO workouts (name) VALUES ('" . $workout['plan_name'] . "');";
    $sql .= "INSERT INTO user_workouts (plan_id, user_id) VALUES (LAST_INSERT_ID(), " . $workout['user_id'] . ");";
    
    if(mysqli_multi_query($con, $sql)){
        $new_plan_id = mysqli_insert_id($con);
        //echo $new_plan_id;
        SubmitWorkoutDays($con, $workout['days'], $new_plan_id);
    }else{
        echo "Error: " . $sql . "<br>" . mysqli_error($con); 
        mysqli_close($con);
    }
  
}

/**
 * submits exercise
 * @param {String} sql connection 
 * @param {Integer} muscle group id
 * @param {String} name of exercise
 * @return
 */
function SubmitExercise($con, $muscle_id, $exercise){
    $sql = "INSERT INTO exercises (muscle_group_id, exercise_name) VALUES (" . $muscle_id . ", '" . $exercise ."');";
    
    if(mysqli_query($con, $sql)){
        echo "Success!";
    }else{
        echo "Error: " . $sql . "<br>" . mysqli_error($con);  
    }
    mysqli_close($con);
}

/**
 * submits the days and exercises from the workout
 * @param {String} sql connection 
 * @param {json} json string
 * @param {integer} plan id
 * @return
 */
function SubmitWorkoutDays($con, $days, $plan_id){
    //must reset connection as it was losing scope
    $con = mysqli_connect('localhost','root','root','Virtuagym');
    $exercises = array();
    
    foreach($days as $day){
        $name = $day['day_name']; 
        $exercises = $day['exercises'];
        
        $sql = "INSERT INTO days (day_name, plan_id) VALUES ('".$name."',".$plan_id.");";

        if(mysqli_query($con, $sql)){
            $day_id = mysqli_insert_id($con);
            $sql="INSERT INTO workout_exercises (exercise_id, plan_id, day_id) VALUES";
            foreach($exercises as $exercise){
                $sql.=" (".$exercise.",".$plan_id.",".$day_id."),";   
            }
            $sql = rtrim($sql,',') . ";";
            mysqli_multi_query($con, $sql);
        }
    }
    mysqli_close($con);
}

/**
 * deletes specified workout
 * @param {String} sql connection 
 * @param {integer} plan id
 * @return
 */
function DeleteWorkout($con, $plan_id){
    //delete all traces of the plan
    $sql = "DELETE FROM workout_exercises WHERE plan_id=".$plan_id.";";
    $sql .= "DELETE FROM days WHERE plan_id=".$plan_id.";";
    $sql .= "DELETE FROM user_workouts WHERE plan_id=".$plan_id.";";
    $sql .= "DELETE FROM workouts WHERE id=".$plan_id.";";
    
    if(mysqli_multi_query($con, $sql)){
        echo "Workout successfully removed.";
    }else{
        echo "Error: " . $sql . "<br>" . mysqli_error($con); 
    }
    
    mysqli_close($con);
}

/**
 * notifies user by email 
 * @param {String} sql connection 
 * @param {integer} user id
 * @return
 */
function NotifyUser($con, $user_id){
    $sql = "SELECT email FROM users WHERE id=".$user_id.";";
    
    if($result = mysqli_query($con, $sql)){
        $row = mysqli_fetch_assoc($result);
        $msg = "Your workout plan has been added! Thanks for using the site.\n -Virtuagym";
        mail($row["email"], "New Workout Plan", $msg);
        echo "An email has been sent to " . $row["email"];
    }else{
        echo "Error occurred.";   
    }

    mysqli_close($con);
}

/**
 * gets specified plan details
 * @param {String} sql connection 
 * @param {integer} user id
 * @return
 */
function GetPlanDetails($con, $plan_id){
    $sql = "SELECT workouts.name, users.first_name, users.last_name, days.id AS 'day_id', days.day_name, exercises.exercise_name
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
ON workouts.id = days.plan_id
WHERE workouts.id=".$plan_id." 
ORDER BY days.id;";
    
    $arr = array();
    if($result = mysqli_query($con, $sql)){
        while($row = mysqli_fetch_assoc($result)) {
            $arr[] = $row;
        }
        echo json_encode($arr);
    }else{
        echo "Error: " . $sql . "<br>" . mysqli_error($con);    
    }
    
    mysqli_close($con);
}
?>