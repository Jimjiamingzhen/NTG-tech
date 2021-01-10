<?php
$evaluator = isset($_POST['evaluator']) ? htmlspecialchars($_POST['evaluator']) : '';
$course = isset($_POST['course']) ? htmlspecialchars($_POST['course']) : '';
include ('dbinfo.php');
/*
$servername = "39.102.54.216";
$username = "root";
$password = "2788098";
$port = 3306; 
*/
$dbname = $course;
$conn = new mysqli($servername, $DBusername, $DBpassword, $dbname, $port);
$conn -> query("SET NAMES utf8");
// 检测连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);

} 
//查询登录用户所在的小组
$getUserGroupsql = "SELECT StudentGroup From Persons WHERE PersonName = '$evaluator'";
$result = $conn -> query($getUserGroupsql);
$studentGroup = $result -> fetch_assoc()['StudentGroup'];
//查询小组总数量
$getGroupNumbersql = "SELECT count(*) FROM Groups;";
$result = $conn-> query ($getGroupNumbersql);
$groupNumbers = $result -> fetch_assoc()['count(*)'];
//查询Rubrics数量
$getRubricssql = "SELECT RubricsName FROM rubrics;";
$result = $conn->query($getRubricssql);
$rubrics = array();
while($row = $result-> fetch_assoc()){
    array_push($rubrics,$row['RubricsName']);
}
if($studentGroup == $groupNumbers){
    //管理员，返回所有学生名字
    $evaluatees = array();
    for($group = 1 ; $group < $groupNumbers ; $group++){
        $groupMembers = array();
        $getGroupMembersql = "SELECT PersonName From Persons WHERE StudentGroup = '$group';";
        $result = $conn -> query($getGroupMembersql);
        while($row = $result -> fetch_assoc()){
                array_push($groupMembers, $row['PersonName']);
        }
        array_push($evaluatees, $groupMembers);
    }
    $evaluateesJson =  json_encode($evaluatees, JSON_UNESCAPED_UNICODE);
}
else{
    //学生，返回除自己外的所有组员
    $groupMembers = array();
    $getGroupMembersql = "SELECT PersonName From Persons WHERE StudentGroup = '$studentGroup';";
    $result = $conn -> query($getGroupMembersql);
    while($row = $result -> fetch_assoc()){
        if($row['PersonName'] != $evaluator){
            array_push($groupMembers, $row['PersonName']);
        }
    }
    $evaluateesJson = json_encode($groupMembers, JSON_UNESCAPED_UNICODE);
}
//将rubrics和evaluatee打包传回
$rubricsJson = json_encode($rubrics, JSON_UNESCAPED_UNICODE);
$totalJson = '{"evaluatee":'.$evaluateesJson.',"rubrics":'.$rubricsJson."}";
echo $totalJson;

?>