<?php
// $Id:$ //声明变量
$username = isset($_POST['username']) ? $_POST['username'] : "";
$password = isset($_POST['password']) ? $_POST['password'] : "";
$remember = isset($_POST['remember']) ? $_POST['remember'] : ""; //判断用户名和密码是否为空

$servername = "39.102.86.62";
$DBusername = "root";
$DBpassword = "2788098";
$DBname = "SDM202";
$port = 3306;

if (!empty($username) && !empty($password)) { //建立连接
    $conn = new mysqli($servername, $DBusername, $DBpassword, $DBname, $port);
    $conn -> query("SET NAMES utf8");
    $sql_select = "SELECT PersonName,Password,PersonRole FROM Persons WHERE PersonName = '$username' AND Password = '$password'"; //执行SQL语句
    $ret = mysqli_query($conn, $sql_select);
    echo $sql_select;
    $row = mysqli_fetch_array($ret); //判断用户名或密码是否正确
    echo $conn->error;
    echo var_dump($row);
    if ($username == $row['PersonName'] && $password == $row['Password']) 
    { //选中“记住我”
        if ($remember == "on") 
        { //创建cookie
            setcookie("", $username, time() + 7 * 24 * 3600);
        } //开启session
        session_start(); //创建session
        $personRole = $row['PersonRole'];
        $_SESSION['user'] = $username; //写入日志
        $_SESSION['role'] = $personRole;
        $ip = $_SERVER['REMOTE_ADDR'];
        $date = date('Y-m-d');
        $info = sprintf("当前访问用户：%s,IP地址：%s,时间：%s /n", $username, $ip, $date);
        $sql_logs = "INSERT INTO Loginlogs(Username,Ip,InputDate) VALUES('$username','$ip','$date')";
        //日志写入文件，如实现此功能，需要创建文件目录logs
        $f = fopen('./logs/' . date('Ymd') . '.log', 'a+');
        fwrite($f, $info);
        fclose($f); //跳转到loginsucc.php页面
        $conn->query($sql_logs);
        if($personRole == 1){
            header("Location:scoringSheet.php"); 
        }
        else{
            header("Location:management.php"); 
        }
        mysqli_close($conn);
    }
    /*
    else 
    { 
        //用户名或密码错误，赋值err为1
        header("Location:login.php?err=1");
    }
} else { //用户名或密码为空，赋值err为2
    header("Location:login.php?err=2");
      */  
} ?> 
