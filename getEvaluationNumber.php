<?php
    $weekNumber = isset($_POST['weekNumber']) ? $_POST['weekNumber'] : "?";
    $course = isset($_POST['course']) ? $_POST['course'] : "?";
    $servername = "39.102.86.62";
    $username = "root";
    $password = "2788098";
    $dbname = $course;
    $port = '3306';
    $conn = new mysqli($servername, $username, $password, $dbname, $port);
    $conn -> query("SET NAMES utf8");
    // 检测连接
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }
    $evaluationNumbers = array();
    $missingEvaluators = array();
    $allStudentEvaluators = array();
    $sqlGetEvaluators = "SELECT PersonName FROM Persons WHERE `PersonRole` = 1;";
    $resultGetEvaluators = $conn->query($sqlGetEvaluators);
    while($row = $resultGetEvaluators-> fetch_assoc()){
        array_push($allStudentEvaluators,$row['PersonName']);
    }
    for($week=1; $week<$weekNumber+1;$week++){
        $sqlGetRecord = "SELECT * FROM SubmitRecord WHERE `Week` = $week;";
        $resultGetRecord = $conn->query($sqlGetRecord);
        $submittedEvaluators = array();
        while($row = $resultGetRecord -> fetch_assoc()){
                array_push($submittedEvaluators,$row['Evaluator']);
            }
        $evaluationNumber = count($submittedEvaluators);
        $missingEvaluator = array_diff($allStudentEvaluators,$submittedEvaluators);
        array_push($evaluationNumbers,$evaluationNumber);
        array_push($missingEvaluators,$missingEvaluator);
    }
    $evaluationNumbersInJson = json_encode($evaluationNumbers, JSON_UNESCAPED_UNICODE);
    $missingEvaluatorsInJson = json_encode($missingEvaluators, JSON_UNESCAPED_UNICODE);
    $totalJson = '{"evaluationNumber":'.$evaluationNumbersInJson.',"missingEvaluators":'.$missingEvaluatorsInJson."}";
    echo $totalJson;
    $conn->close();
?>