<?php
    if (!session_id()){
        session_start();
    } 
    if(isset($_SESSION['user'])){
        $evaluator =  $_SESSION['user'];
        if(isset($_SESSION['role'])){
            if($_SESSION['role'] == 'student'){
                header("Location:login.php");
            }
        }
    }
    else{
        header("Location:login.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SDIM</title>
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

        td {
            border-width: 1px;
            padding: 8px;
            border-style: solid;
            border-color: #666666;
            background-color: #ffffff;
            width:13%;
        }
    </style>
    <script src="https://cdn.staticfile.org/vue/2.2.2/vue.min.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <script>
        function logout(){
            $.post("logout.php")
            location.reload(true);
        }
    </script>

</head>

<body>
    <div id = "titleOfWeb" style = "width:100%;height:50px;background-color:#A79DA5;">
        <div id = "headBlank1" style ="width:10%;height:50px;float:left;">
        </div>
        <div id = "headBlank2" style ="width:20%;height:50px;float:left;">
            <img src="SDIM.svg" alt="SDIM LOGO" width="150">
        </div>
        <div id = "headBlank3" style ="width:10%;height:50px;float:left;">
        </div>
        <div id = "headBlank4" style ="width:20%;height:50px;float:left;">
            <h1 style = "text-align: center; font-size:20px;">SDIM分数管理</h1>
        </div>
        <div id = "headBlank5" style ="width:20%;height:50px;float:left;font-size:10px;line-height:10px;text-align: center;">
            <p>Current User: <?php echo $_SESSION['user'];?> </p>
            <p>Role: Administrator</p>
        </div>
        <div id = "headBlank6"  style ="width:10%;height:50px;float:left;position:relative;">
            <button id ="logout" type="button" onclick="logout()" style = "width:80%;height:50%;position:absolute;left:5%;top:25%;text-align:center;font-size:5px;">Logout</button>
        </div>
        <div id = "headBlank7" style ="width:10%;height:50px;float:left;position:relative;">
            <button id ="headToScoringSheet" type="button" onclick="self.location = 'administratorScoringSheet.php';"style = "width:90%;height:50%;position:absolute;left:5%;top:25%;text-align:center;font-size:5px;">Scoring</button>
        </div>
    </div>
    <div id = "blank" style = "width:100%;height:50px;">

    </div>

    <div id = "manageEvaluation">
        <p v-show=false v-once>           
            {{getEvaluationNumber()}}
        </p>
        <table>
            <tr>
                <th>Week</th>
                <th>EvaluationNumber</th>
                <th>Uncommitted</th>
            </tr>    
            <tr v-for = "week in weekNumber">
                <th>
                    {{week}}
                </th>
                <td>
                    {{evaluationNumbers[week-1]}}
                </td>
                <td>
                    <span v-for = "missingEvaluator in missingEvaluators[week-1]">
                        {{missingEvaluator}}
                    </span>
                </td>
                <!--
                <td v-if="evaluationNumbers[week-1]>0">
                    <span v-for = "missingEvaluator in missingEvaluators[week-1]">
                        {{missingEvaluator}}
                    </span>
                </td>
                <td v-else>
                    too many missing evaluators
                </td>
                 -->
            </tr>
        </table>

    </div>
    <div id = "blank" style = "width:100%;height:50px;">

    </div>
    <div id="handleEvaluation">
        <table>
            <tr>
                <td colspan = 4>
                HANDLE EVALUATIONS
                </td>
            </tr>
            <tr>
                <td >
                    Week:<input v-model = "week" placeholder = "week">
                </td>
                <td colspan = 2>
                    <select v-model='course'>
                        <option v-for = 'coursename in courseList' :value='coursename'>{{coursename}}</option>
                    </select>                
                </td>
            </tr>
            <!--
            <tr>
                <td colspan = 4>
                <button id ="summarizeEvaluation" type="button" @click="summarizeEvaluation">summarizeEvaluation</button>
                <span v-html = "summarizeWorkState"></span>
                </td>
            </tr>
            -->
            <tr>
                <td>
                    INweight:<input v-model = "INweight" placeholder = "Inweight">
                </td>
                <td>
                    TAweight:<input v-model = "TAweight" placeholder = "TAweight">
                </td>
                <td colspan = 2>
                    STweight:<input v-model = "STweight" placeholder = "STweight">
                </td>
            </tr>
            <tr v-if="course == 'SDM242'">
                <td >
                    STINweight:<input v-model = "STINweight" placeholder = "STINweight">
                </td>
                <td colspan = 2>
                    STTAweight:<input v-model = "STTAweight" placeholder = "STTAweight">
                </td>
            </tr>
            <tr>
                <td colspan = 4>
                <button id ="calcTotalGrade" type="button" @click="calcTotalGrade">Calculate Total Grade</button>
                <span v-html = "calcTotalGradeWorkState"></span>
                </td>
            </tr>
        </table>
    </div>
    <script>

        new Vue({
            el:'#manageEvaluation',
            data:{
                weekNumber:16,
                evaluationNumbers:[],
                missingEvaluators:[],
                courseList:<?php echo json_encode($_SESSION['courseList'])?>
            },
            methods:{
                getEvaluationNumber:function(){
                    var params = new URLSearchParams();
                    params.append('weekNumber',this.weekNumber);
                    params.append('course',this.courseList[0]);
                    var that = this;
                    axios
                    .post('getEvaluationNumber.php',params)
                    .then(
                        function(response){
                            console.log(response.data);
                            that.evaluationNumbers = response.data.evaluationNumber; 
                            that.missingEvaluators = response.data.missingEvaluators;
                        }

                    )
                }
            }
        })
        new Vue({
            el:"#handleEvaluation",
            data:{
                week:10,
                course:'',
                INweight:0.5,
                TAweight:0.3,
                STweight:0.2,
                STINweight:0.625,
                STTAweight:0.375,
                summarizeWorkState:"",
                calcTotalGradeWorkState:"",
                courseList:<?php echo json_encode($_SESSION['courseList'])?>
            },
            methods:{
                /*
                summarizeEvaluation:function(){
                    var params = new URLSearchParams();
                    params.append('week',this.week);
                    params.append('course',this.course);
                    params.append('action','summarize');
                    this.summarizeWorkState = "Working,please wait for a moment";
                    var that = this;
                    axios.post('statistics.php',params).then(function(response){
                        that.summarizeWorkState = "Done";
                        console.log(response.data);

                    });
                },
                */
                calcTotalGrade:function(){
                    var params = new URLSearchParams();
                    params.append('week',this.week);
                    params.append('course',this.course);
                    params.append('action','calcTotalGrade');
                    params.append('INweight',this.INweight);
                    params.append('TAweight',this.TAweight);
                    params.append('STweight',this.STweight);
                    params.append('STTAweight',this.STTAweight);
                    params.append('STINweight',this.STINweight);
                    this.calcTotalGradeWorkState = "Working,please wait for a moment";
                    var that = this;
                    axios.post('statistics.php',params).then(function(response){
                        that.calcTotalGradeWorkState = "Done";
                        window.open("RESULT/" + that.course + "WEEK" + that.week + ".zip");
                        console.log(response.data);
                    });
                }
            },
            created(){
                    this.course = this.courseList[0];
                }
        })
    </script>
</body>
</html>