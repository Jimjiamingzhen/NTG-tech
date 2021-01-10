import sqlalchemy
from sqlalchemy.orm import sessionmaker
import db_classes
import dbinfo
import constants
import time
import sys
from sqlalchemy import Column, ForeignKey, Integer, String, DateTime

def calcTotalGrade(session, student, week, weightST, weightTA, weightIN):
    #查询课程采纳的rubrics
    rubrics = session.query(db_classes.Rubrics.RubricsName).all()
    #向定义好的TotalGrade，Grade类中加入评价项目属性
    for i in range(len(rubrics)):
        if not hasattr(db_classes.Grade, rubrics[i][0]):
            setattr(db_classes.Grade, rubrics[i][0], Column(String(100)))
        if not hasattr(db_classes.TotalGrade, rubrics[i][0]):
            setattr(db_classes.TotalGrade, rubrics[i][0], Column(String(100)))

    #对于某一位学生，查询教授、TA、同学对他在该周的评价记录
    scoreST = session.query(db_classes.Grade).filter(
        sqlalchemy.and_(db_classes.Grade.EvaluateeID == student,
                        db_classes.Grade.DataSource == 1,
                        db_classes.Grade.Week == week)).first()
    scoreTA = session.query(db_classes.Grade).filter(
        sqlalchemy.and_(db_classes.Grade.EvaluateeID == student,
                        db_classes.Grade.DataSource == 2,
                        db_classes.Grade.Week == week)).first()
    scoreIN = session.query(db_classes.Grade).filter(
        sqlalchemy.and_(db_classes.Grade.EvaluateeID == student,
                        db_classes.Grade.DataSource == 3,
                        db_classes.Grade.Week == week)).first()


    #缓存总分
    totalScore = []

    for item in rubrics:
        itemScoreST = getattr(scoreST, item[0]) if getattr(scoreST, item[0]) is not None else 0
        itemScoreTA = getattr(scoreTA, item[0]) if getattr(scoreTA, item[0]) is not None else 0
        itemScoreIN = getattr(scoreIN, item[0]) if getattr(scoreIN, item[0]) is not None else 0
        weightedScore = weightST * float(itemScoreST) + weightTA * float(itemScoreTA) + weightIN * float(itemScoreIN)
        totalScore.append(weightedScore)

    #建立新的总分对象
    new_totalGrade = db_classes.TotalGrade()
    new_totalGrade.Week = week
    evaluatee = session.query(db_classes.Persons).filter(
        db_classes.Persons.id == student).first()
    new_totalGrade.EvaluateeID = evaluatee.id
    new_totalGrade.EvaluateeName = evaluatee.PersonName

    #将缓存中的分数赋予new_totalGrade的属性中
    for i in range(len(rubrics)):
        setattr(new_totalGrade, rubrics[i][0], str(round(totalScore[i], 2)) if totalScore[i] is not None else totalScore[i])

    '''
    已弃用
    new_totalGrade.KnowledgeAcquisition = round(totalScore[0], 2) if totalScore[0] is not None else totalScore[0]
    new_totalGrade.Motivation = round(totalScore[1], 2) if totalScore[1] is not None else totalScore[1]
    new_totalGrade.Communication = round(totalScore[2], 2) if totalScore[2] is not None else totalScore[2]
    new_totalGrade.HandsOnSkills = round(totalScore[3], 2) if totalScore[3] is not None else totalScore[3]
    new_totalGrade.ThinkingSkills = round(totalScore[4], 2) if totalScore[4] is not None else totalScore[4]
    new_totalGrade.Responsibility = round(totalScore[5], 2) if totalScore[5] is not None else totalScore[5]
    new_totalGrade.ProjectExecution = round(totalScore[6], 2) if totalScore[6] is not None else totalScore[6]
    '''
    #记录采用的权重
    new_totalGrade.weightST = weightST
    new_totalGrade.weightTA = weightTA
    new_totalGrade.weightIN = weightIN
    #统计日期
    new_totalGrade.InputDate = time.strftime("%Y-%m-%d")

    session.add(new_totalGrade)
    session.commit()

def calcTotalAvg(session, week):
    #查询课程采纳的rubrics
    rubrics = session.query(db_classes.Rubrics.RubricsName).all()

    #向定义好的AverageGrade类中加入评价项目属性
    for i in range(len(rubrics)):
        if not hasattr(db_classes.AverageGrade, rubrics[i][0]):
            setattr(db_classes.AverageGrade, rubrics[i][0], Column(String(100)))

    #建立新的平均分对象
    new_average = db_classes.AverageGrade()
    new_average.Week = week

    for i in range(len(rubrics)):
        print(db_classes.TotalGrade.__dict__[rubrics[i][0]])
        setattr(new_average, rubrics[i][0], str(round(
        session.query(sqlalchemy.func.avg(db_classes.TotalGrade.__dict__[rubrics[i][0]])).filter(db_classes.TotalGrade.Week == new_average.Week).all()[0][0], 2)))

    '''
    new_average.KnowledgeAcquisition = round(
        session.query(sqlalchemy.func.avg(db_classes.TotalGrade.KnowledgeAcquisition)).filter(db_classes.TotalGrade.Week == new_average.Week).all()[0][0], 2)
    new_average.Motivation = round(
        session.query(sqlalchemy.func.avg(db_classes.TotalGrade.Motivation)).filter(db_classes.TotalGrade.Week == new_average.Week).all()[0][0], 2)
    new_average.Communication = round(
        session.query(sqlalchemy.func.avg(db_classes.TotalGrade.Communication)).filter(db_classes.TotalGrade.Week == new_average.Week).all()[0][0], 2)
    new_average.HandsOnSkills = round(
        session.query(sqlalchemy.func.avg(db_classes.TotalGrade.HandsOnSkills)).filter(db_classes.TotalGrade.Week == new_average.Week).all()[0][0], 2)
    new_average.ThinkingSkills = round(
        session.query(sqlalchemy.func.avg(db_classes.TotalGrade.ThinkingSkills)).filter(db_classes.TotalGrade.Week == new_average.Week).all()[0][0], 2)
    new_average.Responsibility = round(
        session.query(sqlalchemy.func.avg(db_classes.TotalGrade.Responsibility)).filter(db_classes.TotalGrade.Week == new_average.Week).all()[0][0], 2)
    new_average.ProjectExecution = round(
        session.query(sqlalchemy.func.avg(db_classes.TotalGrade.ProjectExecution)).filter(db_classes.TotalGrade.Week == new_average.Week).all()[0][0], 2)
    '''
    new_average.InputDate = time.strftime("%Y-%m-%d")
    session.add(new_average)
    session.commit()

if __name__=='__main__':
    args = sys.argv #[filename, course, week, weighIN, weightTA, weightST]
    week = args[2]
    course = args[1]
    SQLALCHEMY_DATABASE_URI = \
        'mysql+pymysql://' + dbinfo.user + ':' + dbinfo.password + '@' + dbinfo.host + '/' + course
    engine = sqlalchemy.create_engine(SQLALCHEMY_DATABASE_URI, echo=True)
    Session = sessionmaker(bind=engine)
    session = Session()
    students = session.query(db_classes.Persons.id).filter(db_classes.Persons.PersonRole == 1).all()
    weightIN = float(args[3])
    weightTA = float(args[4])
    weightST = float(args[5])


    session.query(db_classes.TotalGrade).filter(db_classes.TotalGrade.Week == week).delete()
    session.query(db_classes.AverageGrade).filter(sqlalchemy.and_(db_classes.AverageGrade.Week == week, db_classes.AverageGrade.StudentGroup == None)).delete()

    for student in students:
        studentid = student[0]
        calcTotalGrade(session, studentid, week, weightST, weightTA, weightIN)

    calcTotalAvg(session, week)


