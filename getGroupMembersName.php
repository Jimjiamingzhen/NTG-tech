<?php
$evaluator = isset($_POST['evaluator']) ? htmlspecialchars($_POST['evaluator']) : '';
$servername = "39.102.86.62";
$username = "root";
$password = "2788098";
$dbname = "SDM202";
$port = 3306; 

$conn = new mysqli($servername, $username, $password, $dbname, $port);
$conn -> query("SET NAMES utf8");
// 检测连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);

} 
$getUserGroupsql = "SELECT StudentGroup From Persons WHERE PersonName = '$evaluator'";
$result = $conn -> query($getUserGroupsql);
$studentGroup = $result -> fetch_assoc()['StudentGroup'];
$getGroupNumbersql = "SELECT count(*) FROM Groups;";
$result = $conn-> query ($getGroupNumbersql);
$groupNumbers = $result -> fetch_assoc()['count(*)'];
if($studentGroup == $groupNumbers){
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
    echo json_encode($evaluatees, JSON_UNESCAPED_UNICODE);
}
else{
    $groupMembers = array();
    $getGroupMembersql = "SELECT PersonName From Persons WHERE StudentGroup = '$studentGroup';";
    $result = $conn -> query($getGroupMembersql);
    while($row = $result -> fetch_assoc()){
        if($row['PersonName'] != $evaluator){
            array_push($groupMembers, $row['PersonName']);
        }
    }
    echo json_encode($groupMembers, JSON_UNESCAPED_UNICODE);
}


?>