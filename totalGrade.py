import sqlalchemy
from sqlalchemy.orm import sessionmaker
import db_classes
import dbinfo
import constants
import time
def calcTotalGrade(session, student, week, course, weightST, weightTA, weightIN, weightSTTA, weightSTIN):
        print('11111111111111111sa',student, week, course, weightST, weightTA, weightIN, weightSTTA, weightSTIN)
    ScoreST = session.query(db_classes.Grade).filter(
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
    ScoreSTAvg = session.query(db_classes.AverageGrade).filter(
        sqlalchemy.and_(db_classes.AverageGrade.StudentGroup == studentGroup,
                        db_classes.AverageGrade.Week == week)).first()
    rubricsNumber = session.query(sqlalchemy.func.count(db_classes.Rubrics.id)).first()[0]
    totalScore = []
    for rubricsItem in range(1, rubricsNumber + 1):
        rubricsName = session.query(db_classes.Rubrics.RubricsName).filter(db_classes.Rubrics.id == rubricsItem).first()[0]
        itemScoreST = getattr(ScoreST, rubricsName)
        print("Score ST" + itemScoreST)
        itemScoreSTAvg = getattr(ScoreSTAvg, rubricsName)
        print("Score STAvg" + itemScoreSTAvg)
        itemScoreTA = getattr(scoreTA, rubricsName)
        print("Score TA" + itemScoreTA)
        itemScoreIN = getattr(scoreIN, rubricsName)
        print("Score IN" + itemScoreIN)
        itemScoreSTFinal = weightSTTA * float(itemScoreTA) + weightSTIN * float(itemScoreIN) + float(itemScoreST) - float(itemScoreSTAvg) if ((itemScoreST and itemScoreTA and itemScoreIN) is not None) else None
        print(itemScoreSTFinal)
        weightedScore = weightST * float(itemScoreSTFinal) + weightTA * float(itemScoreTA) + weightIN * float(itemScoreIN) if ((itemScoreST and itemScoreTA and itemScoreIN) is not None) else None
        print(weightedScore)

        totalScore.append(weightedScore)
        print(totalScore)
    new_totalGrade = db_classes.TotalGrade()
    new_totalGrade.Course = (session.query(db_classes.Courses).filter(
        db_classes.Courses.CourseID == course).first()).id
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

def calcTotalAvg(session, week, course):
    new_average = db_classes.AverageGrade()
    new_average.Week = week
    new_average.Course = (session.query(db_classes.Courses).filter(
        db_classes.Courses.CourseID == course).first()).id
    new_average.StudentGroup = 6
    new_average.KnowledgeAcquisition = round(
        session.query(sqlalchemy.func.avg(db_classes.TotalGrade.KnowledgeAcquisition)).filter(
            sqlalchemy.and_(db_classes.TotalGrade.Week == new_average.Week,
                            db_classes.TotalGrade.Course == new_average.Course)).all()[0][0], 2)
    new_average.Motivation = round(
        session.query(sqlalchemy.func.avg(db_classes.TotalGrade.Motivation)).filter(
            sqlalchemy.and_(db_classes.TotalGrade.Week == new_average.Week,
                            db_classes.TotalGrade.Course == new_average.Course)).all()[0][0], 2)
    new_average.Communication = round(
        session.query(sqlalchemy.func.avg(db_classes.TotalGrade.Communication)).filter(
            sqlalchemy.and_(db_classes.TotalGrade.Week == new_average.Week,
                            db_classes.TotalGrade.Course == new_average.Course)).all()[0][0], 2)
    new_average.HandsOnSkills = round(
        session.query(sqlalchemy.func.avg(db_classes.TotalGrade.HandsOnSkills)).filter(
            sqlalchemy.and_(db_classes.TotalGrade.Week == new_average.Week,
                            db_classes.TotalGrade.Course == new_average.Course)).all()[0][0], 2)
    new_average.ThinkingSkills = round(
        session.query(sqlalchemy.func.avg(db_classes.TotalGrade.ThinkingSkills)).filter(
            sqlalchemy.and_(db_classes.TotalGrade.Week == new_average.Week,
                            db_classes.TotalGrade.Course == new_average.Course)).all()[0][0], 2)
    new_average.Responsibility = round(
        session.query(sqlalchemy.func.avg(db_classes.TotalGrade.Responsibility)).filter(
            sqlalchemy.and_(db_classes.TotalGrade.Week == new_average.Week,
                            db_classes.TotalGrade.Course == new_average.Course)).all()[0][0], 2)
    new_average.ProjectExecution = round(
        session.query(sqlalchemy.func.avg(db_classes.TotalGrade.ProjectExecution)).filter(
            sqlalchemy.and_(db_classes.TotalGrade.Week == new_average.Week,
                            db_classes.TotalGrade.Course == new_average.Course)).all()[0][0], 2)
    new_average.InputDate = time.strftime("%Y-%m-%d")
    session.add(new_average)
    session.commit()

if __name__=='__main__':
    SQLALCHEMY_DATABASE_URI = \
        'mysql+pymysql://' + dbinfo.user + ':' + dbinfo.password + '@' + dbinfo.host + '/' + dbinfo.database
    engine = sqlalchemy.create_engine(SQLALCHEMY_DATABASE_URI, echo=True)
    Session = sessionmaker(bind=engine)
    session = Session()
    week = 10
    course = 'SDM242'
    personNumber = session.query(sqlalchemy.func.count(db_classes.Persons.id)).all()[0][0]
    studentNumber = session.query(sqlalchemy.func.count(db_classes.Persons.id)).filter(
        db_classes.Persons.PersonRole == 1).all()[0][0]
    weightST = 0.2
    weightTA = 0.3
    weightIN = 0.5
    weightSTTA = 0.375
    weightSTIN = 0.625

    for student in range(personNumber - studentNumber + 1, personNumber + 1):
        print(student)
        calcTotalGrade(session, student, week, course, weightST, weightTA, weightIN, weightSTTA, weightSTIN)

    calcTotalAvg(session, week, course)


