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
    <script src="vue.min.js"></script>
    <script src="axios.min.js"></script>
    <script src="jquery.min.js"></script>
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
            <button id ="logout" type="button" onclick="logout()" style = "width:80%;height:50%;position:absolute;left:5%;top:25%;text-align:center;font-size:15px;">Logout</button>
        </div>
        <div id = "headBlank7" style ="width:10%;height:50px;float:left;position:relative;">
            <button id ="headToScoringSheet" type="button" onclick="self.location = 'administratorScoringSheet.php';"style = "width:90%;height:50%;position:absolute;left:5%;top:25%;text-align:center;font-size:15px;">Scoring</button>
        </div>
    </div>
    <div id = "blank1" style = "width:100%;height:50px;">
        <p></p>
    </div>

    <div id = "manageEvaluation">
        <p v-show=false v-once>           
            {{getEvaluationNumber()}}
        </p>
        <table>
            <tr>
                <th>Course</th>
                <td colspan = 2>
                    <select v-model='course' @change = "getEvaluationNumber();">
                        <option v-for = 'coursename in courseList' :value='coursename'>{{coursename}}</option>
                    </select>
                </td>
            </tr>
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
    <div id = "blank3" style = "width:100%;height:50px;">
        <p></p>
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
                <span v-html = 'calcTotalGradeWorkState'></span>
                </td>
            </tr>
        </table>
    </div>
    <div id = "blank4" style = "width:100%;height:50px;">
        <p></p>
    </div>
    <div id="immigrateEvaluation">
        <table>
            <tr>
                <td colspan = 4>
                IMMIGRATE EVALUATIONS
                </td>
            </tr>
            <tr>
                <td >
                    FromWeek:<input v-model = "fromWeek" placeholder = "fromWeek" @change="getNameList">
                </td>
                <td colspan = 2>
                    Course:
                    <select v-model='course' @change="getNameList">
                        <option v-for = 'coursename in courseList' :value='coursename' >{{coursename}}</option>
                    </select>                
                </td>
            </tr>
            <tr>
                <td>
                   ToWeek:<input v-model = "toWeek" placeholder = "toWeek">
                </td>
                <td>
                    Evaluator:
                    <select v-model='selectedName'>
                        <option v-for = 'name in nameList' :value='name'>{{name}}</option>
                        <option v-if = 'this.nameList[0]!="NULL"' :value = '"*"'>所有人</option>
                    </select>  
                </td>
            </tr>
            <tr>
                <td colspan = 4>
                <button id ="immigrate" type="button" @click="submitImmigrateRequest">Immigrate Evaluation</button>
                <span v-html = 'response'></span>
                </td>
            </tr>
        </table>
    </div>




    <script>

        var manager = new Vue({
            el:'#manageEvaluation',
            data:{
                weekNumber:16,
                evaluationNumbers:[],
                missingEvaluators:[],
                courseList:<?php echo json_encode($_SESSION['courseList'])?>,
                course:""
            },
            methods:{
                getEvaluationNumber:function(){
                    if(this.course == ""){
                        this.course = this.courseList[0];
                    }
                    var params = new URLSearchParams();
                    params.append('weekNumber',this.weekNumber);
                    params.append('course',this.course);
                    var that = this;
                    axios
                    .post('getEvaluationNumber.php',params)
                    .then(
                        function(response){
                            that.evaluationNumbers = response.data.evaluationNumber; 
                            that.missingEvaluators = response.data.missingEvaluators;
                        }
                    )
                }
            }
        })
        var handler = new Vue({
            el:"#handleEvaluation",
            data:{
                week:10,
                course:'',
                INweight:0.5,
                TAweight:0.3,
                STweight:0.2,
                STINweight:0.625,
                STTAweight:0.375,
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
                    });
                }
            },
            created(){
                    this.course = this.courseList[0];
                }
        })

        var immigrator = new Vue({
            el:"#immigrateEvaluation",
            data:{
                fromWeek:10,
                toWeek:10,
                course:'',
                selectedName:'',
                nameList:['NULL'],
                response:"",
                courseList:<?php echo json_encode($_SESSION['courseList'])?>
            },
            methods:{
                getNameList:function(){
                    var params = new URLSearchParams();
                    params.append('fromWeek',this.fromWeek);
                    params.append('course',this.course);
                    params.append('action','requestNameList');
                    var that = this;
                    axios.post('immigrateEvaluation.php',params).then(function(response){
                        that.nameList = response.data;
                        that.$forceUpdate;
                        if(that.nameList.length == 0){
                            that.nameList = ['NULL']
                        }
                        that.selectedName = that.nameList[0];
                    });
                },
                submitImmigrateRequest:function(){
                    if(this.selectedName == 'NULL'){
                        this.response = 'FAILED! Evaluator is null';
                    }
                    else{
                        var params = new URLSearchParams();
                        params.append('fromWeek',this.fromWeek);
                        params.append('course',this.course);
                        params.append('toWeek',this.toWeek);
                        params.append('name',this.selectedName);
                        params.append('action','immigrate');
                        var that = this;
                        axios.post('immigrateEvaluation.php',params).then(function(response){
                            that.response = response.data;
                            that.getNameList();
                            manager.getEvaluationNumber();
                    });                       

                    }

                    

                }
            },
            created(){
                    this.course = this.courseList[0];
                    this.getNameList();
                }
        })
    </script>
</body>
</html>
