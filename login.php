<!DOCTYPE html>
<html>
    <head>
        <title>登录</title>
        <meta charset="UTF-8">
    </head>
    <body> 
    <?php

        session_start();
        if (isset($_SESSION['user'])){
            header("Location:scoringSheet.php");
        }

    ?>
    <div class="content" align="center"> 
        <!--头部-->
        <div class="header"> 
            <h1>登录页面</h1> 
        </div> 
        <!--中部--> 
        <div class="middle">
            <form id="loginform" action="loginaction.php" method="post"> 
                <table border="0"> 
                    <tr> 
                        <td>用户名：</td> 
                        <td> 
                            <input type="text" id="name" name="username" required="required" value="<?php echo isset($_COOKIE[""]) ? $_COOKIE[""] : ""; ?>"> 
                        </td> 
                    </tr> 
                    <tr> 
                        <td>密   码：  </td> 
                        <td><input type="password" id="password" name="password"></td> 
                    </tr> 
                    <tr> 
                        <td colspan="2"> 
                            <input type="checkbox" name="remember">
                            <small>
                                记住我
                            </small> 
                        </td> 
                    </tr> 
                    <tr> 
                        <td colspan="2" align="center" style="color:red;font-size:10px;"> 
                            <!--提示信息--> 
                            <?php
                                $err = isset($_GET["err"]) ? $_GET["err"] : "";
                                switch ($err) {
                                    case 1:
                                        echo "用户名或密码错误！";
                                        break;

                                    case 2:
                                        echo "用户名或密码不能为空！";
                                        break;
                                } 
                            ?> 
                        </td> 
                    </tr> 
                    <tr> 
                        <td colspan="2" align="center"> 
                            <input type="submit" id="login" name="login" value="登录"> <input type="reset" id="reset" name="reset" value="重置"> 
                        </td> 
                    </tr> 
                    <tr> 
                        <td colspan="2" align="center">SDIM NUMBER ONE</td>
                    </tr> 
                </table> 
            </form> 
        </div> 
        <!--脚部--> 
        <div class="footer"> 
            <small>Copyright &copy; Powered by 不咋地科技 
            <a href="register.php">注册</a>
        </div> 
    </div>
    </body>
</html> 