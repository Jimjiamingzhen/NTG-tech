# NTG分数统计系统  
本系统文件说明如下:  
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
├── summarizeEvaluation.py              // python脚本，从数据库中的打分记录计算出学生被某一身份打分的平均分，记录到Evaluation表  
├── totalGrade.py                       // python脚本，从Evaluation表读取数据，加入不同身份的权重，计算学生该周总分，记录到TotalGrade表  
├── totalGrade4SDM242.py                // 为2020年的242课程加入的总分计算特化逻辑  
├── work_db.py                          // 初始化课程DB  
└── work_db_SDIM.py                     // 初始化系统DB  
