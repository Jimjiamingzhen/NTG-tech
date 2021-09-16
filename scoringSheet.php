<!DOCTYPE html>
<html lang="en">

<!-- Check current user -->

<?php 
        if (!session_id()){
            session_start();
        } 
        if(isset($_SESSION['user'])){
            $evaluator =  $_SESSION['user'];
            if(isset($_SESSION['role'])){
                if($_SESSION['role'] != 'student'){
                    header("Location:login.php");
                }
            }
        }
        else{
            header("Location:login.php");
        }
?>


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
        td{
            border-width: 1px;
            padding: 8px;
            border-style: solid;
            border-color: #666666;
            background-color: #ffffff;
            width:13%;
        }
        td.invalid {
            border-width: 1px;
            padding: 8px;
            border-style: solid;
            border-color: #666666;
            background-color: #FF9696;
            width:13%;
        }
        #course {
            width: 100%;
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
    <!-- HEAD -->   
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
            <p>Role: Student</p>
        </div>
        <div id = "headBlank6"  style ="width:10%;height:50px;float:left;position:relative;">
            <button id ="logout" type="button" onclick="logout()" style = "width:80%;height:50%;position:absolute;left:5%;top:25%;text-align:center;font-size:5px;">Logout</button>
        </div>
        <div id = "headBlank7" style ="width:10%;height:50px;float:left;position:relative;">
        </div>
    </div>

    <!-- BLANK -->  
    <div id = "blank" style = "width:100%;height:50px;">
        <p></p>
    </div>
    
    <!-- SCORING SHEET -->
    <div id = "scoringSheet">
            <table>
                <tr>
                    <th>Name</th>
                    <td>{{evaluator}}</td>
                    <th>Course</th>
                    <td colspan="2">
                        <select v-model='course' @change = "getScoringSheetData();">
                        <option v-for = 'coursename in courseList' :value='coursename'>{{coursename}}</option>
                        </select>
                    </td>
                    <th>Week</th>
                    <td v-Text="week" contenteditable @input = "week=$event.target.innerText;"colspan="2" :class = "{invalid: !weekValid}">3</td>
                </tr>
                <tbody>
                    <tr>
                        <th>Item\Evaluatee</th>
                        <th v-for = "person in evaluations">
                        {{person.evaluatee}}
                        </th>
                    </tr>
                    <tr v-for = "i in rubrics.length">       
                        <th>{{rubrics[i-1]}}</th>
                        <td v-for = "person in evaluations" v-Text="person.score[i-1]" contenteditable @input = "person.score[i-1]=$event.target.innerText;" :class = "{invalid: !person.valid[i-1]}">
                        </td>
                    </tr>
                    <tr>
                        <th>Comment</th>
                        <td v-for = "person in evaluations" v-Text="person.comment" contenteditable @input = "person.comment=$event.target.innerText;">
                    </tr>
                <tbody>
            </table>
            <button @click = "submitEvaluation">Submit</button>
            <p v-HTML = "alertText"></p>

    </div>

    <script>
        new Vue({
            el:"#scoringSheet",
            data:{
                evaluations:[],
                week:"",
                weekValid:true,
                evaluator:"<?php echo $_SESSION['user'];?>",
                date:"",
                rubrics:[],
                course:"",
                alertText:"Response：",
                errorCount:0,
                submitAllowed:true,
                courseList:<?php echo json_encode($_SESSION['courseList'])?>,
            },
            methods:{
                getScoringSheetData:function(){
                    this.evaluations = [];
                    if(this.course == ""){
                        this.course = this.courseList[0];
                    }
                    var params = new URLSearchParams();
                    params.append('evaluator',this.evaluator);
                    params.append('course',this.course);
                    var that = this;
                    axios
                        .post('getScoringSheetData.php',params)
                        .then(
                            function(response){
                                that.rubrics = response.data.rubrics;
                                var evaluateeNames = response.data.evaluatee;
                                for (person in evaluateeNames){
                                that.evaluations.push({evaluatee : evaluateeNames[person], score:new Array(that.rubrics.length).fill(""), comment:"", valid:new Array(that.rubrics.length).fill(true)});
                                }
                            }

                        );
                    var date = new Date();
                    this.date = date.getFullYear() + "-" + (date.getMonth()+1) + "-" + date.getDate();

                },
                submitEvaluation:function(){
                    function Evaluation(week, evaluator, evaluatee, course, scores, date,comment){
                        this.week = week;
                        this.evaluator = evaluator;
                        this.evaluatee = evaluatee;
                        this.course = course
                        /*
                        this.K = K;
                        this.M = M;
                        this.C = C;
                        this.H = H;
                        this.T = T;
                        this.R = R;
                        this.P = P;
                        */
                        this.scores = scores;
                        this.InputDate = date;
                        this.comment = comment;
                    }

                    function validateScore(personRecord, rubricsNumber, that){
                        invalid=[];
                        for (var i = 0; i < rubricsNumber; i++){
                            if(personRecord.score[i]<=0 || personRecord.score[i]>5 || isNaN(personRecord.score[i])){
                                that.submitAllowed = false;
                                invalid.push(i);
                                that.errorCount+=1;
                                alertContent = '<br>error' + that.errorCount + ': Score should be numbers between 0 and 5';
                                that.alertText+=alertContent;
                            }
                            else{
                                personRecord.valid[i]= true;
                            }
                        }
                        return invalid;
                    }

                    function validateWeek(week, that){
                        if(week<=0 || week>16 || isNaN(week) || Math.floor(week) != week){
                            that.submitAllowed = false;
                            that.weekValid = false;
                            that.errorCount+=1;
                            alertContent = '<br>error' + that.errorCount + ': week should be a number between 0 and 16';
                            that.alertText+=alertContent;
                        }

                    }

                    function validateCourse(that){
                        if(that.course == ""){
                            that.submitAllowed = false;
                            that.errorCount+=1;
                            alertContent = '<br>error' + that.errorCount + ':Please select a course';
                            that.alertText+=alertContent;
                        }

                    }

                    function validateComment(comment,that){
        
                        var sqlStr=sql_str().split(',');
                        
                        for(var i=0;i<sqlStr.length;i++){
                            if(comment.toLowerCase().indexOf(sqlStr[i])!=-1){
                                that.submitAllowed = false;
                                that.errorCount+=1;
                                alertContent = '<br>error' + that.errorCount + ':illegal words in comment: ' + sqlStr[i];
                                that.alertText+=alertContent;
                                that.commentValid = false;
                                break;
                            }
                        }
                    }

                    function sql_str(){
                        var str="and,delete,or,exec,insert,select,union,update,count,*,',join,>,<";
                        return str;
                    }

                    var evaluationsToSubmit = [];
                    var evaluations = this.evaluations;
                    var that = this;
                    this.errorCount = 0;
                    this.alertText = "";
                    this.submitAllowed = true;
                    this.weekValid = true;
                    
                    validateWeek(this.week, that);
                    validateCourse(that);
                    for(person in evaluations){
                        var personRecord = evaluations[person];
                        invalid = validateScore(personRecord,this.rubrics.length,that);
                        for(i in invalid){
                            this.evaluations[person].valid[invalid[i]] = false;
                            this.$forceUpdate();
                        }
                        validateComment(personRecord.comment,that);
                        evaluationsToSubmit.push(
                            new Evaluation(
                                this.week, this.evaluator, personRecord.evaluatee, 
                                this.course, personRecord.score, this.date, personRecord.comment));
                    }
                    if (this.submitAllowed == true){
                        var evaluationsInJson = JSON.stringify(evaluationsToSubmit)
                        $.post("handleEvaluations.php",{
                            evaluationData:evaluationsInJson,
                            'course':that.course
                        },
                        function(data, status){
                            alert(data);
                            console.log(data);
                            this.alertText += data;
                        });
                    }
                    else{
                        this.alertText += '<br>Submit fail';
                    }

                },
            },
            created(){
                    this.getScoringSheetData();
                }
            })
    </script>
</body>
</html>