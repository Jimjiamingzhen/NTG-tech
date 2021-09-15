import sqlalchemy
from sqlalchemy.orm import sessionmaker
import db_classes
import dbinfo
import constants
import time
import sys
from sqlalchemy import Column, ForeignKey, Integer, String, DateTime

def calcTotalGrade(session, student, week, weightST, weightTA, weightIN, weightSTTA, weightSTIN):
    #查询课程采纳的rubrics
    rubrics = session.query(db_classes.Rubrics.RubricsName).all()
    #向定义好的TotalGrade，Grade类中加入评价项目属性
    for i in range(len(rubrics)):
        if not hasattr(db_classes.Grade, rubrics[i][0]):
            setattr(db_classes.Grade, rubrics[i][0], Column(String(100)))
        if not hasattr(db_classes.TotalGrade, rubrics[i][0]):
            setattr(db_classes.TotalGrade, rubrics[i][0], Column(String(100)))
        if not hasattr(db_classes.AverageGrade, rubrics[i][0]):
            setattr(db_classes.AverageGrade, rubrics[i][0], Column(String(100)))

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
    studentGroup = session.query(db_classes.Persons.StudentGroup).filter(db_classes.Persons.id == student).first()[0]
    scoreSTAvg = session.query(db_classes.AverageGrade).filter(
        sqlalchemy.and_(db_classes.AverageGrade.StudentGroup == studentGroup,
                        db_classes.AverageGrade.Week == week)).first()
    rubricsNumber = session.query(sqlalchemy.func.count(db_classes.Rubrics.id)).first()[0]
    totalScore = []
    for rubricsItem in range(1, rubricsNumber + 1):
        rubricsName = session.query(db_classes.Rubrics.RubricsName).filter(db_classes.Rubrics.id == rubricsItem).first()[0]
        itemScoreST = getattr(scoreST, rubricsName) if getattr(scoreST, rubricsName) is not None else 0
        itemScoreSTAvg = getattr(scoreSTAvg, rubricsName) if getattr(scoreSTAvg, rubricsName) is not None else 0
        itemScoreTA = getattr(scoreTA, rubricsName) if getattr(scoreTA, rubricsName) is not None else 0
        itemScoreIN = getattr(scoreIN, rubricsName) if getattr(scoreIN, rubricsName) is not None else 0
        itemScoreSTFinal = weightSTTA * float(itemScoreTA) + weightSTIN * float(itemScoreIN) + float(itemScoreST) - float(itemScoreSTAvg)
        weightedScore = weightST * float(itemScoreSTFinal) + weightTA * float(itemScoreTA) + weightIN * float(itemScoreIN)
        totalScore.append(weightedScore)
    new_totalGrade = db_classes.TotalGrade()
    new_totalGrade.Week = week
    evaluatee = session.query(db_classes.Persons).filter(
        db_classes.Persons.id == student).first()
    new_totalGrade.EvaluateeID = evaluatee.id
    new_totalGrade.EvaluateeName = evaluatee.PersonName
    new_totalGrade.KnowledgeAcquisition = round(totalScore[0], 2) if totalScore[0] is not None else totalScore[0]
    new_totalGrade.Motivation = round(totalScore[1], 2) if totalScore[1] is not None else totalScore[1]
    new_totalGrade.Communication = round(totalScore[2], 2) if totalScore[2] is not None else totalScore[2]
    new_totalGrade.HandsOnSkills = round(totalScore[3], 2) if totalScore[3] is not None else totalScore[3]
    new_totalGrade.ThinkingSkills = round(totalScore[4], 2) if totalScore[4] is not None else totalScore[4]
    new_totalGrade.Responsibility = round(totalScore[5], 2) if totalScore[5] is not None else totalScore[5]
    new_totalGrade.ProjectExecution = round(totalScore[6], 2) if totalScore[6] is not None else totalScore[6]
    new_totalGrade.weightST = weightST
    new_totalGrade.weightTA = weightTA
    new_totalGrade.weightIN = weightIN
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

    args = sys.argv #[filename, course, week, weighIN, weightTA, weightST, weightSTIN, weightSTTA]
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
    weightSTIN = float(args[6])
    weightSTTA = float(args[7])

    session.query(db_classes.TotalGrade).filter(db_classes.TotalGrade.Week == week).delete()
    session.query(db_classes.AverageGrade).filter(sqlalchemy.and_(db_classes.AverageGrade.Week == week, db_classes.AverageGrade.StudentGroup == 6)).delete()

    for student in students:
        studentid = student[0]
        calcTotalGrade(session, studentid, week, weightST, weightTA, weightIN, weightSTTA, weightSTIN)

    calcTotalAvg(session, week)


