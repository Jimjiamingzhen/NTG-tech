<?php 
    header("Content-Type:text/html;charset=utf-8");
    $evaluationsInJson = htmlspecialchars_decode(isset($_POST['evaluationData']) ? htmlspecialchars($_POST['evaluationData']) : '');
    $course = htmlspecialchars_decode(isset($_POST['course']) ? htmlspecialchars($_POST['course']) : '');
    $evaluations = json_decode($evaluationsInJson);
    include ('dbinfo.php');
    
    /*
    $servername = "39.102.54.216";
    $username = "root";
    $password = "2788098";
    $port = 3306;
    */

    $dbname = $course;
    $conn = connectToServer($servername, $DBusername, $DBpassword, $dbname, $port);
    $evaluationAlreadyExist = alreadySubmitted($conn, $evaluations[0]);
    if($evaluationAlreadyExist){
        echo 'Evaluation on week '.$evaluations[0] ->{'week'}.' Already Exist';
    }
    else{
        for ($evaluationNumber=0; $evaluationNumber < count($evaluations); $evaluationNumber++){
            addEvaluation($conn, $evaluations[$evaluationNumber]);
        }
        addSubmitRecord($conn, $evaluations[0]);
        echo 'Submit sucess';
    }
    $conn -> close();

    
    function connectToServer($servername, $username, $password, $dbname, $port){
        $conn = new mysqli($servername, $username, $password, $dbname, $port);
        $conn -> query("SET NAMES utf8");
        // 检测连接
        if ($conn->connect_error) {
            die("连接失败: " . $conn->connect_error);
    
        } 
        return $conn;
    }
    

    function getPerson($conn, $name){
        $sqlGetId = "SELECT id , PersonRole FROM Persons WHERE personName = '$name'";
        $result = $conn -> query($sqlGetId);
        $row = $result ->fetch_assoc();
        $result->free();
        return $row;
    }


    function addEvaluation($conn, $evaluation){
        $week = $evaluation -> {'week'};
        $evaluatorName = $evaluation ->{'evaluator'};
        $evaluator = getPerson($conn, $evaluatorName);
        $evaluatorId = $evaluator['id'];
        $evaluateeId = getPerson($conn, $evaluation ->{'evaluatee'})['id'];
        $inputDate = $evaluation -> {'InputDate'};
        $dataSource = $evaluator['PersonRole'];
        $scores = $evaluation -> {'scores'};
        $comment = $evaluation ->{'comment'};
        $getRubricsNumbersql = "SELECT count(*) FROM Rubrics;";
        $result = $conn-> query ($getRubricsNumbersql);
        $rubricsNumber = $result -> fetch_assoc()['count(*)'];
        $sql = "";
        if(!empty($comment)){
            $commentsql = "INSERT INTO `Comments` VALUES (NULL, $week, $evaluateeId, $evaluatorId, '$comment', $dataSource, '$inputDate');";
            $conn -> query($commentsql);
        }
        for ($rubrics = 0; $rubrics < $rubricsNumber; $rubrics++ ){
            $rubricsItem = $rubrics + 1;
            $score = $scores[$rubrics];
            $sql .= "INSERT INTO `Evaluation` VALUES (NULL, $week, $evaluateeId, $evaluatorId, $rubricsItem, '$score', '$inputDate', $dataSource);";
        }
        if ($conn->multi_query($sql) === TRUE) {            
            while ($conn->more_results() && $conn->next_result())
            {
                //什么也不做
            }
        }

    }
    function addSubmitRecord($conn, $evaluation){
        $week = $evaluation -> {'week'};
        $evaluatorName = $evaluation ->{'evaluator'};
        $inputDate = $evaluation -> {'InputDate'};
        $sql ="INSERT INTO `SubmitRecord` VALUES (NULL, '$evaluatorName', $week, '$inputDate');";
        $conn->query($sql); 
    }
    function alreadySubmitted($conn, $evaluation){
        $week = $evaluation -> {'week'};
        $evaluatorName = $evaluation ->{'evaluator'};
        $sql = "SELECT * FROM `SubmitRecord` WHERE Evaluator = '$evaluatorName' and Week = $week;";
        $result = $conn->query($sql);
        
        return mysqli_num_rows($result);
    }
?>