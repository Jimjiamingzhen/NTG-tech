<?php
// $Id:$ //声明变量
$username = isset($_POST['username']) ? $_POST['username'] : "";
$password = isset($_POST['password']) ? $_POST['password'] : "";
$remember = isset($_POST['remember']) ? $_POST['remember'] : ""; //判断用户名和密码是否为空
$function = isset($_POST['submitButtom']) ? $_POST['submitButtom'] : "";
$newPassword = isset($_POST['newPassword']) ? $_POST['newPassword'] : "";
$confirm = isset($_POST['confirm']) ? $_POST['confirm'] : "";
$personID = isset($_POST['personID']) ? $_POST['personID'] : "";
$loginRole = isset($_POST['role']) ? $_POST['role'] : "";

$servername = "39.102.54.216";
$DBusername = "root";
$DBpassword = "2788098";
$DBname = "SDIM";
$port = 3306;

 //建立连接
$conn = new mysqli($servername, $DBusername, $DBpassword, $DBname, $port);
$conn -> query("SET NAMES utf8");
$sql_select = "SELECT PersonName,Password,PersonID FROM Persons WHERE PersonName = '$username' AND Password = '$password'"; //执行SQL语句
$ret = $conn->query($sql_select);
$row = $ret->fetch_array(); //判断用户名或密码是否正确
if ($username == $row['PersonName'] && $password == $row['Password']) 
{  
    //若为登录
    if($function == "登录"){
        //判断当前登录模式下是否有此用户
        if($loginRole == 'student'){
            $sql_findRole = "SELECT CourseName FROM ElectiveLog WHERE PersonName = '$username' AND PersonRole = 1;";
        }
        else{
            $sql_findRole = "SELECT CourseName FROM ElectiveLog WHERE PersonName = '$username' AND PersonRole !=1 ;";
        }
        $courseRet = $conn -> query($sql_findRole);
        $courseList = array();
        while($courseRow = $courseRet -> fetch_assoc()){
            array_push($courseList, $courseRow['CourseName']);
        }
        if(empty($courseList)){
            header("Location:login.php?err=5");
        }
        else{
        //选中“记住我”
            if ($remember == "on") 
            { //创建cookie
                setcookie("", $username, time() + 7 * 24 * 3600);
            } //开启session
            session_start(); //创建session
            $_SESSION['user'] = $username; //写入日志
            $_SESSION['role'] = $loginRole;
            $_SESSION['courseList'] = $courseList;
            $ip = $_SERVER['REMOTE_ADDR'];
            $date = date('Y-m-d');
            $info = sprintf("当前访问用户：%s,IP地址：%s,时间：%s /n", $username, $ip, $date);
            $sql_logs = "INSERT INTO Loginlogs(Username,Ip,InputDate) VALUES('$username','$ip','$date')";
            //日志写入文件，如实现此功能，需要创建文件目录logs
            $f = fopen('./logs/' . date('Ymd') . '.log', 'a+');
            fwrite($f, $info);
            fclose($f); //跳转到loginsucc.php页面
            $conn->query($sql_logs);
            if($loginRole == 'student'){
                header("Location:scoringSheet.php"); 
            }
            else{
                header("Location:administratorScoringSheet.php"); 
            }
            $conn->close();
        }

    }
    //若为修改密码
    else{
        if($personID == $row['PersonID']){
            if($newPassword == $confirm){  
                $sql_reset = "UPDATE Persons SET Password = '$newPassword' WHERE PersonName = '$username' and PersonID = $personID;";
                $conn->query($sql_reset);
                $sql_getCourse = "SELECT CourseName FROM ElectiveLog WHERE PersonName = '$username';";
                $courseRet = $conn -> query($sql_getCourse);
                $courseList = array();
                while($courseRow = $courseRet -> fetch_assoc()){
                    array_push($courseList, $courseRow['CourseName']);
                }
                $conn->close();

                foreach($courseList as $course){
                    $conn = new mysqli($servername, $DBusername, $DBpassword, $course, $port);
                    $conn -> query("SET NAMES utf8");
                    $sql_reset_in_course = "UPDATE Persons SET Password = '$newPassword' WHERE PersonName = '$username' and PersonID = $personID;";
                    $conn->query($sql_reset_in_course);
                    $conn->close();
                }
                //修改成功，请重新登录,err=4
                header("Location:login.php?err=4");

            }
            else{
                //新密码与密码确认不匹配,err-3
                header("Location:login.php?err=3");

            }
        }
        else{
            //用户名与ID不匹配， 赋值err=2
            header("Location:login.php?err=2");

        }
    }   

}

else 
{ 
    //用户名或密码错误，赋值err为1
    header("Location:login.php?err=1");
}
 ?> 