<!DOCTYPE html>
<html>
    <head>
        <title>登录</title>
        <meta charset="UTF-8">
        <script src="https://cdn.staticfile.org/vue/2.2.2/vue.min.js"></script>
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
                        <td>姓名：</td> 
                        <td> 
                            <input type="text" id="name" name="username" required="required" value="<?php echo isset($_COOKIE[""]) ? $_COOKIE[""] : ""; ?>"> 
                        </td> 
                    </tr> 
                    <tr v-if=resetting>
                    <td>学   号：</td> 
                        <td> 
                            <input type="text" id="personID" name="personID" required="required"> 
                        </td> 
                    </tr> 

                    <tr> 
                        <td>密   码：  </td> 
                        <td><input type="password" id="password" name="password" required="required"></td> 
                    </tr> 

                    <tr v-if=resetting> 
                        <td>新密码：  </td> 
                        <td><input type="text" id="newPassword" name="newPassword" required="required"></td> 
                    </tr> 

                    <tr v-if=resetting> 
                        <td>确认密码：  </td> 
                        <td><input type="text" id="confirm" name="confirm" required="required"></td> 
                    </tr> 
                
                    <tr v-if=!resetting> 
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
                                        echo "用户名与ID不匹配！";
                                        break;
                                    
                                    case 3:
                                        echo "新密码与确认密码输入不一致，请重新输入";
                                        break;
                                    
                                    case 4:
                                        echo "密码修改成功，请重新登录";
                                        break;
                                        
                                    
                                    
                                } 
                            ?> 
                        </td> 
                    </tr> 
                    <tr> 
                        <td colspan="2" align="center"> 
                            <input type="submit" id="submitButtom" name="submitButtom" :value= "resetting ? '提交修改' : '登录'" > <button type="button" @click = changeFunction>{{!resetting?"修改密码":"返回登录"}}</button>
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
        </div> 
    </div>
    <script>
        new Vue({
            el:".middle",
            data:{
                resetting:false
            },
            methods:{
                changeFunction:function(){
                    this.resetting = !this.resetting;
                }
                
            }
        })
    </script>
    </body>
</html> 