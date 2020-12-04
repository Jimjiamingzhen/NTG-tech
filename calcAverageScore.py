import sqlalchemy
from sqlalchemy.orm import sessionmaker
import db_classes
import dbinfo
import constants
import time
import sys

def groupAverage(session, week, course):
    for group in range(1, len(session.query(db_classes.Groups.id).all())):
        new_average = db_classes.AverageGrade()
        new_average.Week = week
        new_average.Course = (session.query(db_classes.Courses).filter(
            db_classes.Courses.CourseID == course).first()).id
        new_average.StudentGroup = group
        groupMembersQuery = session.query(db_classes.Persons.id).filter(db_classes.Persons.StudentGroup == group).all()
        groupMembers = []
        for i in range(len(groupMembersQuery)):
            groupMembers.append(groupMembersQuery[i][0])
        print(groupMembers)
        new_average.KnowledgeAcquisition = round(session.query(sqlalchemy.func.avg(db_classes.Grade.KnowledgeAcquisition)).filter(sqlalchemy.and_(db_classes.Grade.EvaluateeID.in_(groupMembers), db_classes.Grade.DataSource == 1, db_classes.Grade.Week == new_average.Week, db_classes.Grade.Course == new_average.Course)).all()[0][0], 2)
        new_average.Motivation = round(session.query(sqlalchemy.func.avg(db_classes.Grade.Motivation)).filter(sqlalchemy.and_(db_classes.Grade.EvaluateeID.in_(groupMembers), db_classes.Grade.DataSource == 1, db_classes.Grade.Week == new_average.Week, db_classes.Grade.Course == new_average.Course)).all()[0][0], 2)
        new_average.Communication = round(session.query(sqlalchemy.func.avg(db_classes.Grade.Communication)).filter(sqlalchemy.and_(db_classes.Grade.EvaluateeID.in_(groupMembers), db_classes.Grade.DataSource == 1, db_classes.Grade.Week == new_average.Week, db_classes.Grade.Course == new_average.Course)).all()[0][0], 2)
        new_average.HandsOnSkills = round(session.query(sqlalchemy.func.avg(db_classes.Grade.HandsOnSkills)).filter(sqlalchemy.and_(db_classes.Grade.EvaluateeID.in_(groupMembers), db_classes.Grade.DataSource == 1, db_classes.Grade.Week == new_average.Week, db_classes.Grade.Course == new_average.Course)).all()[0][0], 2)
        new_average.ThinkingSkills = round(session.query(sqlalchemy.func.avg(db_classes.Grade.ThinkingSkills)).filter(sqlalchemy.and_(db_classes.Grade.EvaluateeID.in_(groupMembers), db_classes.Grade.DataSource == 1, db_classes.Grade.Week == new_average.Week, db_classes.Grade.Course == new_average.Course)).all()[0][0], 2)
        new_average.Responsibility = round(session.query(sqlalchemy.func.avg(db_classes.Grade.Responsibility)).filter(sqlalchemy.and_(db_classes.Grade.EvaluateeID.in_(groupMembers), db_classes.Grade.DataSource == 1, db_classes.Grade.Week == new_average.Week, db_classes.Grade.Course == new_average.Course)).all()[0][0], 2)
        new_average.ProjectExecution = round(session.query(sqlalchemy.func.avg(db_classes.Grade.ProjectExecution)).filter(sqlalchemy.and_(db_classes.Grade.EvaluateeID.in_(groupMembers), db_classes.Grade.DataSource == 1, db_classes.Grade.Week == new_average.Week, db_classes.Grade.Course == new_average.Course)).all()[0][0], 2)
        new_average.InputDate = time.strftime("%Y-%m-%d")
        session.add(new_average)
        session.commit()

if __name__ == '__main__':
    SQLALCHEMY_DATABASE_URI = \
        'mysql+pymysql://' + dbinfo.user + ':' + dbinfo.password + '@' + dbinfo.host + '/' + dbinfo.database
    engine = sqlalchemy.create_engine(SQLALCHEMY_DATABASE_URI, echo=True)
    Session = sessionmaker(bind=engine)
    session = Session()

    args = sys.argv #[filename, course, week]
    course = args[1]
    week = args[2]
    courseId = (session.query(db_classes.Courses).filter(
        db_classes.Courses.CourseID == course).first()).id
    session.query(db_classes.AverageGrade).filter(sqlalchemy.and_(
        db_classes.AverageGrade.Week == week,
        db_classes.AverageGrade.Course ==courseId)).delete()
    groupAverage(session, week, course)