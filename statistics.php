<?php
    $course = isset($_POST['course']) ? $_POST['course'] : "?";
    $week = isset($_POST['week']) ? $_POST['week'] : "?";
    $action = isset($_POST['action']) ? $_POST['action'] : "?";
    if($action == 'summarize'){
        exec("python summarizeEvaluation.py $course $week");
    }
    elseif($action == 'calcTotalGrade'){
        $INweight = isset($_POST['INweight']) ? $_POST['INweight'] : "?";
        $TAweight = isset($_POST['TAweight']) ? $_POST['TAweight'] : "?";
        $STweight = isset($_POST['STweight']) ? $_POST['STweight'] : "?";
        if($course == 'SDM242'){
            $STINweight = isset($_POST['STINweight']) ? $_POST['STINweight'] : "?";
            $STTAweight = isset($_POST['STTAweight']) ? $_POST['STTAweight'] : "?";
            exec("python calcAverageScore.py $course $week");
            echo"wuhu";
        }
    }
    else{
    }
?>