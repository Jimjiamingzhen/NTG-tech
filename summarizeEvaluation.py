import sqlalchemy
from sqlalchemy.orm import sessionmaker
import db_classes
import dbinfo
import constants
import time
import sys
def summarizeEvaluations(session, student, role, course, week):
    scores = []
    new_grade = db_classes.Grade()
    new_grade.Course = (session.query(db_classes.Courses).filter(
        db_classes.Courses.CourseID == course).first()).id
    new_grade.Week = week
    evaluatee = session.query(db_classes.Persons).filter(
        db_classes.Persons.id == student).first()
    new_grade.EvaluateeID = evaluatee.id
    new_grade.EvaluateeName = evaluatee.PersonName
    new_grade.DataSource = session.query(db_classes.Roles.id).filter(
        db_classes.Roles.id == role).first()[0]
    new_grade.InputDate = time.strftime("%Y-%m-%d")
    evaluatorNumber = 0
    for rubricsItem in range(1, len(constants.RUBRICS) + 1):
        score = session.query(sqlalchemy.func.avg(db_classes.Evaluation.Score)).filter(
            sqlalchemy.and_(db_classes.Evaluation.EvaluateeID == student,
            db_classes.Evaluation.RubricsItem == rubricsItem,
            db_classes.Evaluation.Week == new_grade.Week,
            db_classes.Evaluation.Source == role)).all() # add a new filter condition of role to distingish evaluations from different roles.
        itemEvaluatorNumber = session.query(sqlalchemy.func.count(db_classes.Evaluation.Score)).filter(
            sqlalchemy.and_(db_classes.Evaluation.EvaluateeID == student,
            db_classes.Evaluation.RubricsItem == rubricsItem,
            db_classes.Evaluation.Week == new_grade.Week,
            db_classes.Evaluation.Source == role)).all()
        if itemEvaluatorNumber[0][0] > evaluatorNumber: evaluatorNumber = itemEvaluatorNumber[0][0]
        scores.append(score[0][0])
    new_grade.EvaluatorNumber = evaluatorNumber
    new_grade.KnowledgeAcquisition = round(scores[0], 2) if scores[0] is not None else scores[0]
    new_grade.Motivation = round(scores[1], 2) if scores[1] is not None else scores[1]
    new_grade.Communication = round(scores[2], 2) if scores[2] is not None else scores[2]
    new_grade.HandsOnSkills = round(scores[3], 2) if scores[3] is not None else scores[3]
    new_grade.ThinkingSkills = round(scores[4], 2) if scores[4] is not None else scores[4]
    new_grade.Responsibility = round(scores[5], 2) if scores[5] is not None else scores[5]
    new_grade.ProjectExecution = round(scores[6], 2) if scores[6] is not None else scores[6]
    session.add(new_grade)
    session.commit()

if __name__=='__main__':
    SQLALCHEMY_DATABASE_URI = \
        'mysql+pymysql://' + dbinfo.user + ':' + dbinfo.password + '@' + dbinfo.host + '/' + dbinfo.database
    engine = sqlalchemy.create_engine(SQLALCHEMY_DATABASE_URI, echo=True)
    Session = sessionmaker(bind=engine)
    session = Session()
    args = sys.argv #[filename, course, week]
    course = args[1]
    week = args[2]
    personNumber = session.query(sqlalchemy.func.count(db_classes.Persons.id)).all()[0][0]
    studentNumber = session.query(sqlalchemy.func.count(db_classes.Persons.id)).filter(
        db_classes.Persons.PersonRole == 1).all()[0][0]
    for role in range(1, len(constants.ROLES) + 1):
        for student in range(personNumber - studentNumber + 1, personNumber + 1):
            summarizeEvaluations(session, student, role, course, week)