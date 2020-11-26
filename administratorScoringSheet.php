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
                if($_SESSION['role'] == 1){
                    //header("Location:scoringSheet.php");
                }
            }
        }
        else{
            //header("Location:login.php");
        }
?>


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
    <script src="https://cdn.staticfile.org/vue/2.2.2/vue.min.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>

</head>
<body>
    <!-- HEAD -->   
    <div id = "titleOfWeb" style = "width:100%;height:200px;background-color:#FFA500;">
        <h1>SDIM</h1>
        <p>Current User: <?php echo $_SESSION['user'];?> </p>
        <p>Role: administrator</p>
        <button id ="logout" type="button" onclick="logout()">Logout</button>
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
                        <select v-model='course'>
                        <option disabled value="">请选择</option>
                        <option value="SDM232">SDM232</option>
                        <option value="SDM242">SDM242</option>
                        <option value="SDM262">SDM262</option>
                        <option value="SDM272">SDM272</option>
                        </select>
                    </td>
                    <th>Week</th>
                    <td v-Text="week" contenteditable @input = "week=$event.target.innerText;"colspan="2" :class = "{invalid: !weekValid}">3</td>
                </tr>
                <tbody v-for = "group in evaluations">
                    <tr>
                        <th>Item\Evaluatee</th>
                        <th v-for = "person in group">
                        {{person.evaluatee}}
                        </th>
                    </tr>
                    <tr v-for = "i in rubrics.length">       
                        <th>{{rubrics[i-1]}}</th>
                        <td v-for = "person in group" v-Text="person.score[i-1]" contenteditable @input = "person.score[i-1]=$event.target.innerText;" :class = "{invalid: !person.valid[i-1]}">
                        </td>
                        <!--
                            在每个人后添加valid属性，是数组。再加一个总的valid确定是否可以被提交。  
                            -->
                    </tr>
                    <tr>
                        <th>Comment</th>
                        <td v-for = "person in group" v-Text="person.comment" contenteditable @input = "person.comment=$event.target.innerText;">
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
            evaluator:"<?php echo "洪小平";?>",
            date:"",
            rubrics:['Knowledge Acquisition','Motivation','Communication','Hands-on Skills', 'Thinking Skills','Responsibility','Project Execution'],
            course:"",
            alertText:"啊哈",
            errorCount:0,
            submitAllowed:true
        },
        methods:{
            getEvaluatees:function(){
                var params = new URLSearchParams();
                params.append('evaluator',this.evaluator);
                var that = this;
                axios
                    .post('getGroupMembersName.php',params)
                    .then(
                        function(response){
                            console.log(response);
                            var evaluateeNames = response.data;
                            for (group in evaluateeNames){
                                groupEvaluations = new Array();
                                for(person in evaluateeNames[group]){
                                    groupEvaluations.push({evaluatee : evaluateeNames[group][person], score:[1,2,3,1,2,3,1], comment:"", valid:[true,true,true,true,true,true,true]});
                                }
                                that.evaluations.push(groupEvaluations);
                            } 
                            console.log(that.evaluations);
                        }

                    );
                var date = new Date();
                this.date = date.getFullYear() + "-" + (date.getMonth()+1) + "-" + date.getDate();

            },
            submitEvaluation:function(){
                function Evaluation(week, evaluator, evaluatee, course, K, M, C, H, T, R, P, date,comment){
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
                    this.InputDate = date;
                    this.comment = comment;
                }
                function validateScore(personRecord, rubricsNumber, that){
                    invalid=[];
                    for (var i = 0; i < rubricsNumber; i++){
                        if(personRecord.score[i]<0 || personRecord.score[i]>5 || isNaN(personRecord.score[i])){
                            that.submitAllowed = false;
                            invalid.push(i);
                            that.errorCount+=1;
                            alert = '<br>error' + that.errorCount + ': Score should be numbers between 0 and 5';
                            that.alertText+=alert;
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
                        alert = '<br>error' + that.errorCount + ': week should be a number between 0 and 16';
                        that.alertText+=alert;
                    }


                }

                var evaluationsToSubmit = [];
                var evaluations = this.evaluations;
                var that = this;
                this.errorCount = 0;
                this.alertText = "";
                this.submitAllowed = true;
                this.weekValid = true;
                validateWeek(this.week, that);
                for(group in evaluations){
                    for(person in evaluations[group]){
                        var personRecord = evaluations[group][person];
                        invalid = validateScore(personRecord,7,that);
                        for(i in invalid){
                            this.evaluations[group][person].valid[invalid[i]] = false;
                            this.$forceUpdate();
                        }
                        evaluationsToSubmit.push(
                            new Evaluation(
                                this.week, this.evaluator, personRecord.evaluatee, 
                                this.course, personRecord.score[0], personRecord.score[1], 
                                personRecord.score[2], personRecord.score[3], personRecord.score[4], 
                                personRecord.score[5], personRecord.score[6], this.date, personRecord.comment));
                    }
                }
                console.log(this.submitAllowed);
                if (this.submitAllowed == true){
                    var evaluationsInJson = JSON.stringify(evaluationsToSubmit)
                    $.post("handleEvaluations.php",{
                        evaluationData:evaluationsInJson
                    },
                    function(data, status){
                        alert(data);
                        this.alertText += data;
                    });
                }
                else{
                    this.alertText += '<br>Submit fail';
                }

            }
        },
        created(){
                this.getEvaluatees();
            }
        })
    </script>
</body>
</html>