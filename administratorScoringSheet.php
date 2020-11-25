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
    <script src="https://cdn.staticfile.org/vue/2.2.2/vue.min.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
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
        <button @click = "console.log(evaluations);">ye</button>
        <div v-for = "group in evaluations">
            <table>
                <tr>
                    <th v-for = "person in group">
                    {{person.evaluatee}}
                    </th>
                </tr>
                <tr v-for = "i in 7">       <!-- rubricsnumber = 7, need to be modified later-->
                    <td v-for = "person in group" v-Text="person.score[i-1]" contenteditable @input = "person.score[i-1]=$event.target.innerText;">
                    </td>
                    <!--
                        在每个人后添加valid属性，是数组。再加一个总的valid确定是否可以被提交。  
                        -->
                </tr>
            </table>
        </div>
    </div>

    <script>
    new Vue({
        el:"#scoringSheet",
        data:{
            evaluations:[],
        },
        methods:{
            getEvaluatees:function(){
                var evaluator = "<?php echo "贾明臻";?>"
                var params = new URLSearchParams();
                params.append('evaluator',evaluator);
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
                                    groupEvaluations.push({evaluatee : evaluateeNames[group][person], score:[1,2,3,1,2,1,2], comment:""});
                                }
                                that.evaluations.push(groupEvaluations);
                            } 
                            console.log(that.evaluations);
                        }

                    );

            }
        },
        created(){
                this.getEvaluatees();
            }
        })
    </script>
</body>
</html>