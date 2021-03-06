<!DOCTYPE HTML>
<html lan="en">
    <meta charset="utf-8">
    <head>
        <title>Virtuagym Assessment</title>
        <link href="style.css" rel="stylesheet" type="text/css"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div id="header"><h1>Workout Plans</h1></div>
        <div id="add_btn_container">
            <button class="form_btn" id="add_plan">(+) Add Plan</button>
            <button class="form_btn" id="add_exercise">(+) Add Exercise</button>
        </div>
        <div id="new_exercise_form">
            <form action="">
                <div>
                    <table id="exercise_options_tbl">
                        <tr>
                            <td>
                                <select class="option_select" id="muscle_select" required>
                                    <option value="-1" selected>select muscle group</option>
                                </select>
                            </td>
                            <td>
                                <input id="exercise_name" type="text" placeholder="Exercise Name" value=""/>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="form_btn" id="submit_exercise">Save</div>
            </form>
        </div>
        <div id="new_plan_form">
            <form action="">
                <div>
                    <table id="plan_info_tbl">
                        <tr>
                            <td>
                                <select class="option_select" id="user_select" name="user_select" required>
                                    <option value="-1" selected>select user</option>
                                </select>
                            </td>
                            <td>
                                <input id="plan_name" type="text" name="plan_name" placeholder="Workout Name" value=""/>
                            </td>
                        </tr>
                    </table>
                </div>
                <div id="directions"><p>Make your selection(s) from the list on the right, and press the arrow when you're ready to transfer your choices.</p></div>
                <div class="exercise_day_container">
                    <table class="exercise_tbl">
                        <tr>
                            <td>
                                <div class="exercise_day">
                                    <div class="day_input_container">
                                        <input class="day_name" type="text" name="day_name" placeholder="Leg Day" value=""/>
                                    </div>
                                    <textarea class="exercise_area" readonly name="exercises_list"  title="To select multiple exercises to add, either ctrl+click or cmd+click (for Mac) on your choices"></textarea>            
                                </div>
                            </td>
                            <td class="transfer_td">
                                <div class="transfer_selection"><=</div>
                            </td>
                            <td>
                                <div class="exercise_selection_container">
                                    <select  class="exercise_selection" size="6" multiple></select>
                                </div>

                            </td>
                        </tr>
                    </table>
                </div>  
                <div id="button_container">
                    <table>
                        <tr>
                            <td><div class="form_btn" id="add_day">(+) Add Day</div></td>
                            <td><div class="form_btn" id="reset_form">Reset</div></td>
                            <td><div class="form_btn" id="submit_workout">Save</div></td>
                        </tr>
                    </table>   
                </div>
            </form>
        </div>
        <div id="assignedPlans">
            <table id="userPlanTable"></table>
            <div id="modal" class="modal">
              <!-- Modal content -->
              <div class="modal_content">
                <span id="close_modal" class="close_modal">&times;</span>
                <div id="modal_exercises_display"></div>
              </div>
            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
        <script src="application.js"></script>
    </body>
</html>