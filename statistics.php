<?php
    $course = isset($_POST['course']) ? $_POST['course'] : "?";
    $week = isset($_POST['week']) ? $_POST['week'] : "?";
    exec("python summarizeEvaluation.py $course $week");
?>