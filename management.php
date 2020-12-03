<?php
    if (!session_id()){
        session_start();
    } 
    if(isset($_SESSION['user'])){
        $evaluator =  $_SESSION['user'];
        if(isset($_SESSION['role'])){
            if($_SESSION['role'] == 1){
                header("Location:scoringSheet.php");
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
    
    <div id = "titleOfWeb" style = "width:100%;height:200px;background-color:#FFA500;">
        <h1>SDIM</h1>
        <p>Current User: <?php echo $_SESSION['user'];?> </p>
        <p>Role: administrator</p>
        <button id ="logout" type="button" onclick="logout()">Logout</button>
        <button id ="headToScoringSheet" type="button" onclick="self.location = 'administratorScoringSheet.php';">Scoring Sheet</button>
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

    <script>

        new Vue({
            el:'#manageEvaluation',
            data:{
                weekNumber:16,
                evaluationNumbers:[],
                missingEvaluators:[]
            },
            methods:{
                getEvaluationNumber:function(){
                    var params = new URLSearchParams();
                    params.append('weekNumber',this.weekNumber);
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
    </script>
</body>
</html>