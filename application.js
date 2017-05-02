/**
 * constructor for workout plan
 * @param {Number} user ID 
 * @param {String} plan name
 * @param {Object[]} array of Day objects
 * @return
 */
function Workout(user, name, days = []) {
    this.user_id = user;
    this.plan_name = name;
    this.days = days;
}

/**
 * constructor for workout day object
 * @param {String} day name 
 * @param {Integer[]} array of exercise IDs
 * @return
 */
function Day(name, exercises){
    this.day_name = name;
    this.exercises = exercises;
}

/**
 * checks entry form for validity
 * @param 
 * @return true if valid, false otherwise
 */
function HasValidWorkoutEntries(){
    var currentExerciseDay = $('.exercise_tbl').last();
    var day_input = currentExerciseDay.find($('.day_name'));
    var area = currentExerciseDay.find($('.exercise_area'));
    //check to make sure there is content before adding new day
    if(day_input[0].value == "" || area[0].value == ""){
        return false;
    }else{
        return true;   
    }
}

/**
 * resets entry form
 * @param 
 * @return 
 */
function ResetForm(){
    //remove all but the first instance of the class
    $('.exercise_tbl').slice(1).remove();
    //clear rest of the inputs
    $('.exercise_selection').val([]);
    $('#user_select').val(-1); 
    $('.exercise_area').val("");
    $('.day_name').val("");
    $('#plan_name').val("");
    $('#new_plan_form').slideUp();
}

/**
 * formats and displays existing workout plans
 * @param {String} plans
 * @return
 */
function FormatPlanList(plans){
    var ob = JSON.stringify(plans);
    ob = JSON.parse(plans);
    var newPlan, row;
    var plans_added = [];
    var groups = [];
    for(var i = 0; i < ob.length; i++){
        row = ob[i];
        newPlan = "<tr class='plan_row'><td><div id='" + row.plan_id + "' class='userPlan'>";
        newPlan += "<div class='close_btn'>X</div>";
        newPlan += "<h2>" + row.plan_name + "</h2>";
        newPlan += "<p>Muscle Group(s) Targeted: " + row.muscles + "</p></div></td></tr>";
        $("#userPlanTable").append(newPlan);
    }
}

/**
 * formats and displays given workout plan details
 * @param {String} details
 * @return
 */
function ShowPlanDetails(details, plan_id){
    var ob = JSON.stringify(details);
    ob = JSON.parse(details);
    var modal = $('#myModal');
    //var span = $('.close_modal')[0];
    var content = $('.modal_content');
    var dom_list;
    var row;
    dom_list = "<ul>"
    for(var i = 0; i < ob.length; i++){
        row = ob[i];
        dom_list += "<li><div>";
        dom_list += "<h3>" + row.exercise_name + "</h3>";
        dom_list += "</div></li>";
        
    }
    dom_list += "</ul>";
    content.append(dom_list);
    modal.style.display = "block";
}

//var span = $('.close_modal')[0];
//span.click(function(event){
//    $('#myModal').style.display = "none";
//});
//
//window.click(function(event) {
//    if (event.target == $('#myModal')) {
//        $('#myModal').style.display = "none";
//    }
//});

/**
 * populates the user options in the select list
 * @param {String} data
 * @return
 */
function PopulateUserDropdown(data){
    var ob = JSON.stringify(data);
    ob = JSON.parse(data);
    var row;
    for(var i = 0; i < ob.length; i++){
        row = ob[i];
        $("#user_select").append($("<option value=" + row.id + ">" + row.name + "</option>"));
    }

}

/**
 * populates the exercise options in the select list
 * @param {String} data
 * @return
 */
function PopulateExercises(data){
    var ob = JSON.stringify(data);
    ob = JSON.parse(data);
    var row;
    for(var i = 0; i < ob.length; i++){
        row = ob[i];
        $(".exercise_selection").append($("<option value=" + row.id + ">" + row.exercise_name + "</option>"));
    }
}

/**
 * click event to show entry form
 * @return
 */
$('#add_plan').click(function(){
    var form = $('#new_plan_form');
    var ex_control = form.find($('.exercise_selection'));
    var user_control = $('#user_select');
    if(ex_control[0].options.length == 0 && user_control[0].options.length == 1){
        data.getPlanOptions();
    }
    $('#new_plan_form').slideToggle();
    $('.exercise_tbl').last().find($('.day_name')).focus();
});

/**
 * click event to show entry form
 * @return
 */
$('#userPlanTable').on('click', '.close_btn', function(){
    var plan_id = $(this).parent().prop("id");
    data.deleteWorkout(plan_id);
});

/**
 * click event to show entry form
 * @return
 */
$('#userPlanTable').on('click', '.userPlan', function(){
    var plan_id = $(this).prop("id");
    data.getPlanDetails(plan_id);
});


/**
 * click event to reset entry form
 * @return
 */
$('#reset_form').click(function(){
    ResetForm();
});

/**
 * click event to generate additional plan day
 * @return
 */
$('#add_day').click(function(){
    
    //check to make sure there is content before adding new day
    if(!HasValidWorkoutEntries()){
        alert("Please add a name, atleast 1 exercise, and pick a user!");
    }else{
        $('.exercise_tbl').first().clone(true, true).appendTo($('.exercise_day_container').last());
        $('.exercise_tbl').last().find($('.day_name')).focus();
        //clear the cloned fields
        currentExerciseDay = $('.exercise_tbl').last();
        currentExerciseDay.find($('.day_name'))[0].value = "";
        currentExerciseDay.find($('.exercise_area'))[0].value = "";
    }
});

/**
 * click event to transfer exercise selections to the corresponding textarea
 * @return
 */
$('.transfer_selection').click(function(){
    var control = $(this).parent().parent().find($('.exercise_selection'));
    var area = $(this).parent().parent().find($('.exercise_area'));
    area.val("");
    var selection = control[0].options;
    for(var i = 0; i < selection.length; i++){
        if(selection[i].selected){
            area.val(area.val() + '*' + selection[i].text + '\n');
        }
    }
});

/**
 * click event to submit created workout
 * @return
 */
$('#submit_exercise').click(function(){
    //check the last workout day for valid entries
    var user = $('#user_select');
    if(!HasValidWorkoutEntries()  || user.val() == -1){
        alert("Please pick a user, add a name and atleast 1 exercise to the final day.");
    }else{
        var user_id = $('#user_select').val();
        var plan_name = $('#plan_name').val();
        var day_name, exercises, exercises_control;
        var workout, current_day;
        var days = [];
        $('.exercise_tbl').each(function(){
            day_name = $(this).find($('.day_name')).val();
            exercises_control = $(this).find($('.exercise_selection'));
            exercises = exercises_control.val();
            current_day = new Day(day_name, exercises);
            days.push(current_day);
        });
        workout = new Workout(user_id, plan_name, days); 
        data.submitWorkout(workout);
    //on success, email user
    }
});

var data = {
     init: function(){
        
        $.get("api.php?q=getPlans", function(data, status){
            FormatPlanList(data); 
        });
    },
    getPlanOptions: function(){
        
        $.get("api.php?q=getUsers", function(data, status){
            PopulateUserDropdown(data);   
        });
        $.get("api.php?q=getExercises", function(data, status){
            PopulateExercises(data);   
        });
    },
    submitWorkout: function(workout){
        $.post('api.php?q=submitWorkout', { workout: JSON.stringify(workout) }, function(response) {
            console.log(response);
            ResetForm();
            data.notifyUser("Add", workout.user_id);
            alert("Success!");
            setTimeout(function(){
                location.reload();
            },1000);
        });
    },
    deleteWorkout: function(plan_id){
        $.post('api.php?q=deleteWorkout', { plan_id: plan_id }, function(response) {
            console.log(response);
            //data.notifyUser("Remove");
            alert("Success!");
            setTimeout(function(){
                location.reload();
            },1000);
        });
    },
    notifyUser: function(change, user_id){
        $.post("api.php?q=sendUserEmail", { user_id: user_id }, function(response){
           console.log(response);
        });
    },
    getPlanDetails: function(plan_id){
        $.get("api.php?q=getPlanDetails", { plan_id: plan_id }, function(data, status){
            ShowPlanDetails(data, plan_id);   
        });
    }
}

$(document).ready(function(){
    data.init();  
});