<?php
include_once "exercise.php";
include_once "workout.php";
include_once "muscle_group.php";
include_once "user.php";

$q = $_REQUEST["q"];

switch($q){
    case "getPlans":
        GetWorkoutPlans();
        break;
    case "getUsers":
        GetUsers();
        break;
    case "getExercises":
        GetExercises();
        break;
    case "getMuscleGroups":
        GetMuscleGroups();
        break;
    case "submitWorkout":
        $workout = $_POST['workout'];
        SubmitWorkout($workout);
        break;
    case "submitExercise":
        $muscle_id = $_POST['muscle_id'];
        $exercise = $_POST['exercise'];
        SubmitExercise($exercise, $muscle_id);
        break;
    case "deleteWorkout":
        $plan_id = $_POST['plan_id'];
        DeleteWorkout($plan_id);
        break;
    case "sendUserEmail":
        $user_id = $_POST['user_id'];
        NotifyUser($user_id);
        break;
    case "getPlanDetails":
        $plan_id = $_GET['plan_id'];
        GetPlanDetails($plan_id);
        break;
    default:
        echo "INVALID PARAMS";
}

/**
 * retrieves the basic workout information for the home page
 * @return {json} json string of workouts
 */
function GetWorkoutPlans(){
    $workout = new Workout();
    echo $workout->select_plans();
}

/**
 * retrieves the users
 * @return {json} json string of users
 */
function GetUsers(){
    $user = new User();
    echo $user->select();
}

/**
 * retrieves the list of exercises
 * @return {json} json string of exercises
 */
function GetExercises(){
    $exercise = new Exercise();
    echo $exercise->select();
}

/**
 * retrieves the list of muscle groups
 * @return {json} json string of muscle groups
 */
function GetMuscleGroups(){
    $muscles = new Muscle_group();
    echo $muscles->select();
}

/**
 * submits workout
 * @param {json} json string
 * @return
 */
function SubmitWorkout($workout_json){
    $new_workout = json_decode($workout_json, true);
    $workout = Workout::create_workout($new_workout['plan_name'], $new_workout['user_id'], $new_workout['days']);
    $workout->insert();
}

/**
 * submits exercise
 * @param {Integer} muscle group id
 * @param {String} name of exercise
 * @return
 */
function SubmitExercise($exercise_name, $muscle_id){
    $exercise = new Exercise();
    $exercise->set_name($exercise_name);
    $exercise->set_muscle_group($muscle_id);
    echo $exercise->insert();
}

/**
 * deletes specified workout
 * @param {integer} plan id
 * @return
 */
function DeleteWorkout($plan_id){
    $workout = new Workout();
    $workout->set_id($plan_id);
    echo $workout->delete();
}

/**
 * notifies user by email 
 * @param {integer} user id
 * @return
 */
function NotifyUser($user_id){
    $user = new User();
    $user->set_id($user_id);
    echo $user->notify_user();
}

/**
 * gets specified plan details
 * @param {integer} user id
 * @return
 */
function GetPlanDetails($plan_id){
    $workout = new Workout();
    $workout->set_id($plan_id);
    echo $workout->select_plan_details();
}
?>