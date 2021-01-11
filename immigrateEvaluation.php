<?php
        function getNameList($week,$conn){
            // 创建数组统计已提交的评估数，未提交的评估和人员名单
            $nameList = array();
            $sqlGetRecord = "SELECT * FROM SubmitRecord WHERE `Week` = $week;";
            $resultGetRecord = $conn->query($sqlGetRecord);
            while($row = $resultGetRecord -> fetch_assoc()){
                array_push($nameList,$row['Evaluator']);
            }
            return $nameList;
        }
        $fromWeek = isset($_POST['fromWeek']) ? $_POST['fromWeek'] : "?";
        $course = isset($_POST['course']) ? $_POST['course'] : "?";
        $action = isset($_POST['action']) ? $_POST['action'] : "?";
        include ('dbinfo.php');
        $dbname = $course;
        $conn = new mysqli($servername, $DBusername, $DBpassword, $dbname, $port);
        $conn -> query("SET NAMES utf8");

        // 检测连接
        if ($conn->connect_error) {
            die("连接失败: " . $conn->connect_error);
        }

        if ($action == "requestNameList") {
            $nameList = getNameList($fromWeek,$conn);
            $nameListJson = json_encode($nameList, JSON_UNESCAPED_UNICODE);
            echo $nameListJson;
            $conn->close();
        }

        else if($action == "immigrate"){
            $state = 0;
            $toWeek = isset($_POST['toWeek']) ? $_POST['toWeek'] : "?";
            $name = isset($_POST['name']) ? $_POST['name'] : "?";
            if($name == '*'){
                $sqlImmigrateEvaluation = "UPDATE evaluation SET Week = $toWeek WHERE Week = $fromWeek;";
                $sqlImmigrateRecord = "UPDATE submitrecord SET Week = $toWeek WHERE Week = $fromWeek;";
                $immigratingMembers = getNameList($fromWeek,$conn);
                $exisitingMembers = getNameList($toWeek,$conn);
                $repeatMembers = array_intersect($immigratingMembers,$exisitingMembers);
                if(!empty($repeatMembers)){
                    $state = 1;
                }
            }
            else{
                $sqlGetID = "SELECT id FROM persons WHERE PersonName = '$name';";
                $result = $conn -> query($sqlGetID);
                $ID = $result->fetch_array()['id'];
                $sqlImmigrateEvaluation = "UPDATE evaluation SET Week = $toWeek WHERE Week = $fromWeek AND EvaluatorID = $ID;";
                $sqlImmigrateRecord = "UPDATE submitrecord SET Week = $toWeek WHERE Week = $fromWeek AND Evaluator = '$name';";
                $exisitingMembers = getNameList($toWeek,$conn);
                $repeatMembers = array_intersect(array($name),$exisitingMembers);
                if(!empty($repeatMembers)){
                    $state = 1;
                }
            }
            if($state == 1){
                echo '第'.$toWeek.'周已存在';
                foreach ($repeatMembers as $person){
                    echo ' '.$person;
                }
                echo ' 的评价记录。迁移失败。';
            }
            else{
                $conn -> query($sqlImmigrateEvaluation);
                $conn -> query($sqlImmigrateRecord);
                echo 'Done';
            }   
        }


?>