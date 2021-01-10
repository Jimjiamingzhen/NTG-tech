<?php
    $weekNumber = isset($_POST['weekNumber']) ? $_POST['weekNumber'] : "?";
    $course = isset($_POST['course']) ? $_POST['course'] : "?";
    include ('dbinfo.php');

    /*
    $servername = "39.102.54.216";
    $username = "root";
    $password = "2788098";
    $port = '3306';
    */
    $dbname = $course;
    $conn = new mysqli($servername, $DBusername, $DBpassword, $dbname, $port);
    $conn -> query("SET NAMES utf8");
    // 检测连接
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }
    // 创建数组统计已提交的评估数，未提交的评估和人员名单
    $evaluationNumbers = array();
    $missingEvaluators = array();
    $allStudentEvaluators = array();

    //查询人员名单
    $sqlGetEvaluators = "SELECT PersonName FROM Persons;";
    $resultGetEvaluators = $conn->query($sqlGetEvaluators);
    while($row = $resultGetEvaluators-> fetch_assoc()){
        array_push($allStudentEvaluators,$row['PersonName']);
    }

    //对每一周进行统计
    for($week=1; $week<$weekNumber+1;$week++){
        //该周所有的提交记录
        $sqlGetRecord = "SELECT * FROM SubmitRecord WHERE `Week` = $week;";
        $resultGetRecord = $conn->query($sqlGetRecord);
        //创建数组储存已提交名单
        $submittedEvaluators = array();
        while($row = $resultGetRecord -> fetch_assoc()){
                array_push($submittedEvaluators,$row['Evaluator']);
            }
        //已提交人数
        $evaluationNumber = count($submittedEvaluators);

        //未提交人： 出现在人员名单中但未在提交记录中的人
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