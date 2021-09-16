# NTG分数统计系统  

## 目录结构  
├── RESULT                              // 存放统计成绩生成的zip，包含成绩表格和雷达图  
├── .gitignore                          // 不需要记入git的文件  
├── administratorScoringSheet.php       // 管理端打分界面前端  
├── calcAverageScore.py                 // python脚本，计算各个小组内同学的平均分  
├── constants.py                        // 初始化课程DB的元数据  
├── constants_SDIM.py                   // 初始化系统DB的元数据  
├── db_class.py                         // 课程DB数据结构定义  
├── db_class_SDIM.py                    // 系统DB数据结构定义  
├── dbinfo.php                          // php连接数据库信息  
├── dbinfo.py                           // python脚本连接数据库信息  
├── generateCSV.py                      // python脚本生成csv成绩单，已废弃  
├── generateZip.py                      // python脚本生成统计结果zip，存储在RESULT文件夹内  
├── getEvaluationNumber.php             // 管理后台页面，统计每周评价提交情况后端逻辑  
├── getScoringSheetData.php             // 打分界面，统计用户需要给哪些同学打分后端逻辑  
├── handleEvaluations.php               // 打分界面，提交分数落库后端逻辑  
├── immigrateEvaluation.php             // 管理后台页面，将某次评价移动至其他周的后端逻辑。用于处理学生填错周数的情况  
├── login.php                           // 登陆界面前端  
├── loginaction.php                     // 登陆界面后端逻辑  
├── logout.php                          // 登出后端逻辑  
├── management.php                      // 管理后台页面前端  
├── radarMap.py                         // python脚本，根据成绩生成雷达图  
├── READ_EVALUATION.py                  // python脚本，数据迁移？  
├── Readme.md                           // help  
├── scoringSheet.php                    // 学生端打分界面前端  
├── statistic.php                       // 管理后台页面，统计分数后端逻辑  
├── summarizeEvaluation.py              // python脚本，从数据库中的打分记录计算出学生被某一身份打分的平均分，记录到Grade表  
├── totalGrade.py                       // python脚本，从Grade表读取数据，加入不同身份的权重，计算学生该周总分，记录到TotalGrade表  
├── totalGrade4SDM242.py                // 为2020年的242课程加入的总分计算特化逻辑  
├── work_db.py                          // 初始化课程DB  
└── work_db_SDIM.py                     // 初始化系统DB  
## 库表设定
数据库由一个系统数据库和多个课程数据库构成。  
系统数据库记录所有课程下的成员，使用系统的课程，成员选课记录和登录记录。  
课程数据库存储该门课程下的成员，分组情况和课程分数。  
分数以以下的方式记录：  
Evaluation表：学生每次的提交会产生多条evaluation记录，每条记录记录【第N周A同学给B同学在R项目上打了X分】  
Grade表：通过summarizeEvaluation.py，由evaluation表的打分记录，分别计算出某个学生从TA，学生，教师三种来源得到的平均分  
TotalGrade表：通过totalGrade.py，由Grade表的数据加上三种身份占总分的权重计算学生某一周的总分。  
AverageGrade表：组号不为空的记录为该周某一组学生来自学生的评价平均值，用于242课程的成绩算法。组号为空的记录为某一周全班同学总成绩的平均值。用于生成雷达图。  

