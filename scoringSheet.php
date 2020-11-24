<!DOCTYPE html>

<?php 
    if (!session_id()){
        session_start();
    } 
    if(isset($_SESSION['user'])){
        $evaluator =  $_SESSION['user'];
        if(isset($_SESSION['role'])){
            if($_SESSION['role'] != 1){
                header("Location:management.php");
            }
        }
    }
    else{
        header("Location:login.php");
    }
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        table{
            font-family: verdana,arial,sans-serif;
            font-size:11px;         
            color:#333333;
            border-width: 1px;
            border-color: #666666;
            border-collapse: collapse;
            width: 100%;
        }
        th {
            border-width: 1px;
            padding: 8px;
            border-style: solid;
            border-color: #666666;
            background-color: #dedede;
            width:13%;
        }
        th.firstColumn {
            border-width: 1px;
            padding: 8px;
            border-style: solid;
            border-color: #666666;
            background-color: #dedede;
            width:22%;
        }
        td {
            border-width: 1px;
            padding: 8px;
            border-style: solid;
            border-color: #666666;
            background-color: #ffffff;
            width:13%;
        }
        #course {
            width: 100%;
        }

    </style>
    <script src="https://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js">
    </script>
        <script>
            function Evaluation(week, evaluator, evaluatee, course, K, M, C, H, T, R, P){
                this.week = week;
                this.evaluator = evaluator;
                this.evaluatee = evaluatee;
                this.course = course
                this.K = K;
                this.M = M;
                this.C = C;
                this.H = H;
                this.T = T;
                this.R = R;
                this.P = P;
                var date = new Date();
                this.InputDate = date.getFullYear() + "-" + (date.getMonth()+1) + "-" + date.getDate();
            }
            function submitData(){
                var evaluations = new Array();
                var evaluator = document.getElementById('name').innerHTML;
                var evaluatees = document.getElementById('evaluatees').children;
                var weekCell = document.getElementById('week');
                var week = weekCell.innerHTML;
                var course = document.getElementById('course').value;
                var Ks = document.getElementsByClassName('knowledge');
                var Ms = document.getElementsByClassName('motivation');
                var Cs = document.getElementsByClassName('communication');
                var Hs = document.getElementsByClassName('hand');
                var Ts = document.getElementsByClassName('think');
                var Rs = document.getElementsByClassName('responsibility');
                var Ps = document.getElementsByClassName('project');
                this.validate = true; 
                this.errorCount = 0;
                var alertText = "";
                var errorNumber = 0;
                validateWeek(weekCell);
                for (var i = 0; i < groupMembers.length; i++){
                    validateScore(Ks[i]);
                    validateScore(Ms[i]);
                    validateScore(Cs[i]);
                    validateScore(Hs[i]);
                    validateScore(Ts[i]);
                    validateScore(Rs[i]);
                    validateScore(Ps[i]);
                    evaluations.push(new Evaluation(week, evaluator, evaluatees[i+1].innerHTML, course, Ks[i].innerHTML, Ms[i].innerHTML, Cs[i].innerHTML, Hs[i].innerHTML, Ts[i].innerHTML, Rs[i].innerHTML, Ps[i].innerHTML));
                }
                if (validate == true){
                    evaluationsInJson = JSON.stringify(evaluations)
                    $.post("handleEvaluations.php",{
                        evaluationData:evaluationsInJson
                    },
                    function(data, status){
                        $('#alertText').append('<br>' + data);
                    });
                }
                else{
                    $('#alertText').append('<br>Submit fail');
                }


            }
            function validateScore(cell){
                if(cell.innerHTML <= 0 || cell.innerHTML > 5){
                    validate = false;
                    errorCount++;
                    cell.style.cssText = "background-color:#FF9696";
                    $('#alertText').append('<br>error ' +  this.errorCount + ': Score should be numbers between 0 and 5');
                }
                else if(isNaN(cell.innerHTML)){
                    validate = false;
                    errorCount++;
                    cell.style.cssText = "background-color:#FF9696";
                    $('#alertText').append('<br>error ' +  this.errorCount + ': Score should be numbers between 0 and 5');
                }
                else{
                    cell.style.cssText = "background-color:#FFFFFF";
                }
            }

            function validateWeek(cell){
                if(cell.innerHTML <= 0 || cell.innerHTML > 16){
                    validate = false;
                    errorCount++;
                    cell.style.cssText = "background-color:#FF9696";
                    $('#alertText').append('<br>error ' +  this.errorCount + ': Week should be numbers between 1 and 16');
                }
                else if(isNaN(cell.innerHTML)){
                    validate = false;
                    errorCount++;
                    cell.style.cssText = "background-color:#FF9696";
                    $('#alertText').append('<br>error ' +  this.errorCount + ': Week should be numbers between 1 and 16');
                }
                else{
                    cell.style.cssText = "background-color:#FFFFFF";
                }
            }
            function logout(){
                $.post("logout.php")
                location.reload(true);
            }
    
        </script>
</head>
<body>
    <div id = "titleOfWeb" style = "width:100%;height:200px;background-color:#FFA500;">
            <h1>SDIM</h1>
            <p>Current User: <?php echo $_SESSION['user'];?> </p>
            <p>Role: Student</p>
            <button id ="logout" type="button" onclick="logout()">Logout</button>
    </div>

    <div id = "blank" style = "width:100%;height:50px;">

    </div>

    <table>
        <tr>
            <th class = "firstColumn">Evaluator</th>
            <td class = "" id = "name" >老同志</td>
            <th>course</th>
            <td>
                <select id="course" >
                    <option value="SDM232">SDM232</option>
                    <option value="SDM242">SDM242</option>
                    <option value="SDM262">SDM262</option>
                    <option value="SDM272">SDM272</option>
                    </select>
            </td>
            <th>Week</th>
            <td class = "editable" id = "week" colspan="2" >3</td>
        </tr>
        <tr id = "evaluatees">
            <th class = "firstColumn">Item\Evaluatee</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>

        <tr>
            <th class = "firstColumn">Knowledge Acquisition</th>
            <td class = "editable knowledge student1" >1.1</td>
            <td class = "editable knowledge student2" >2</td>
            <td class = "editable knowledge student3" >3</td>
            <td class = "editable knowledge student4" >4</td>
            <td class = "editable knowledge student5" >5</td>
            <td class = "editable knowledge student6" >1</td>
        </tr>

        <tr>
            <th class = "firstColumn">Motivation</th>
            <td class = "editable motivation student1" >5</td>
            <td class = "editable motivation student2" >4</td>
            <td class = "editable motivation student3" >2</td>
            <td class = "editable motivation student4" >""</td>
            <td class = "editable motivation student5" >3</td>
            <td class = "editable motivation student6" >2</td>
        </tr>

        <tr>
            <th class = "firstColumn">Communication</th>
            <td class = "editable communication student1" >1</td>
            <td class = "editable communication student2" >2</td>
            <td class = "editable communication student3" >sdim</td>
            <td class = "editable communication student4" >5</td>
            <td class = "editable communication student5" >好</td>
            <td class = "editable communication student6" >1</td>
        </tr>


        <tr>
            <th class = "firstColumn">Hands-on Skills</th>
            <td class = "editable hand student1" >3</td>
            <td class = "editable hand student2" >4</td>
            <td class = "editable hand student3" >5</td>
            <td class = "editable hand student4" >2</td>
            <td class = "editable hand student5" >1</td>
            <td class = "editable hand student6" >2</td>
        </tr>

        <tr>
            <th class = "firstColumn">Thinking Skills</th>
            <td class = "editable think student1" >3</td>
            <td class = "editable think student2" >4</td>
            <td class = "editable think student3" >5</td>
            <td class = "editable think student4" >6</td>
            <td class = "editable think student5" >2</td>
            <td class = "editable think student6" >2</td>
        </tr>
        
        <tr>
            <th class = "firstColumn">Responsibility</th>
            <td class = "editable responsibility student1" >2</td>
            <td class = "editable responsibility student2" >3</td>
            <td class = "editable responsibility student3" >1</td>
            <td class = "editable responsibility student4" >2</td>
            <td class = "editable responsibility student5" >3</td>
            <td class = "editable responsibility student6" >5</td>
        </tr>

        <tr>
            <th class = "firstColumn">Project Execution</th>
            <td class = "editable project student1" >1</td>
            <td class = "editable project student2" >2</td>
            <td class = "editable project student3" >4</td>
            <td class = "editable project student4" >5</td>
            <td class = "editable project student5" >2</td>
            <td class = "editable project student6" >3</td>
        </tr>
    </table>
    <button id ="submit" type="button" onclick="submitData()">Submit</button>
    <p id="alertText">Response：</p>
    <script>
        $('#name').text("<?php echo $_SESSION['user'];?>");
        editableElement = document.getElementsByClassName("editable");
        for (var i = 0; i < editableElement.length; i++){
            editableElement[i].setAttribute("contenteditable", "true")
        }
        $.post("getGroupMembersName.php",{
            evaluator: $('#name').text()
        },
        function(data,status){
            console.log(data);
            groupMembers = JSON.parse(data);
            var nameTags = $('#evaluatees').children();
            for (var i = 1; i < groupMembers.length+1; i++){
                nameTags.eq(i).text(groupMembers[i-1]);
            }
        })

    </script>


</body>
</html>
