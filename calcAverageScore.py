import sqlalchemy
from sqlalchemy.orm import sessionmaker
import db_classes
import dbinfo
import constants
import time
import sys
from sqlalchemy import Column, ForeignKey, Integer, String, DateTime

def groupAverage(session, week):
    #查询课程采纳的rubrics
    rubrics = session.query(db_classes.Rubrics.RubricsName).all()
    #向定义好的TotalGrade，Grade类中加入评价项目属性
    for i in range(len(rubrics)):
        if not hasattr(db_classes.Grade, rubrics[i][0]):
            setattr(db_classes.Grade, rubrics[i][0], Column(String(100)))
        if not hasattr(db_classes.TotalGrade, rubrics[i][0]):
            setattr(db_classes.TotalGrade, rubrics[i][0], Column(String(100)))

    for group in range(1, len(session.query(db_classes.Groups.id).all())):
        new_average = db_classes.AverageGrade()
        new_average.Week = week
        new_average.StudentGroup = group
        groupMembersQuery = session.query(db_classes.Persons.id).filter(db_classes.Persons.StudentGroup == group).all()
        groupMembers = []
        for i in range(len(groupMembersQuery)):
            groupMembers.append(groupMembersQuery[i][0])
        print(groupMembers)
        print(session.query(sqlalchemy.func.avg(db_classes.Grade.KnowledgeAcquisition)).filter(sqlalchemy.and_(db_classes.Grade.EvaluateeID.in_(groupMembers), db_classes.Grade.DataSource == 1, db_classes.Grade.Week == new_average.Week)).all())
        K = session.query(sqlalchemy.func.avg(db_classes.Grade.KnowledgeAcquisition)).filter(sqlalchemy.and_(db_classes.Grade.EvaluateeID.in_(groupMembers), db_classes.Grade.DataSource == 1, db_classes.Grade.Week == new_average.Week)).all()[0][0]
        M = session.query(sqlalchemy.func.avg(db_classes.Grade.Motivation)).filter(sqlalchemy.and_(db_classes.Grade.EvaluateeID.in_(groupMembers), db_classes.Grade.DataSource == 1, db_classes.Grade.Week == new_average.Week)).all()[0][0]
        C = session.query(sqlalchemy.func.avg(db_classes.Grade.Communication)).filter(sqlalchemy.and_(db_classes.Grade.EvaluateeID.in_(groupMembers), db_classes.Grade.DataSource == 1, db_classes.Grade.Week == new_average.Week)).all()[0][0]
        H = session.query(sqlalchemy.func.avg(db_classes.Grade.HandsOnSkills)).filter(sqlalchemy.and_(db_classes.Grade.EvaluateeID.in_(groupMembers), db_classes.Grade.DataSource == 1, db_classes.Grade.Week == new_average.Week)).all()[0][0]
        T = session.query(sqlalchemy.func.avg(db_classes.Grade.ThinkingSkills)).filter(sqlalchemy.and_(db_classes.Grade.EvaluateeID.in_(groupMembers), db_classes.Grade.DataSource == 1, db_classes.Grade.Week == new_average.Week)).all()[0][0]
        R = session.query(sqlalchemy.func.avg(db_classes.Grade.Responsibility)).filter(sqlalchemy.and_(db_classes.Grade.EvaluateeID.in_(groupMembers), db_classes.Grade.DataSource == 1, db_classes.Grade.Week == new_average.Week)).all()[0][0]
        P = session.query(sqlalchemy.func.avg(db_classes.Grade.ProjectExecution)).filter(sqlalchemy.and_(db_classes.Grade.EvaluateeID.in_(groupMembers), db_classes.Grade.DataSource == 1, db_classes.Grade.Week == new_average.Week)).all()[0][0]
        new_average.KnowledgeAcquisition = round(K, 2) if K is not None else 0
        new_average.Motivation = round(M, 2) if M is not None else 0
        new_average.Communication = round(C, 2) if C is not None else 0
        new_average.HandsOnSkills = round(H, 2) if H is not None else 0
        new_average.ThinkingSkills = round(T, 2) if T is not None else 0
        new_average.Responsibility = round(R, 2) if R is not None else 0
        new_average.ProjectExecution = round(P, 2) if P is not None else 0
        new_average.InputDate = time.strftime("%Y-%m-%d")
        session.add(new_average)
        session.commit()

if __name__ == '__main__':
    args = sys.argv #[filename, course, week]
    course = args[1]
    week = args[2]
    SQLALCHEMY_DATABASE_URI = \
        'mysql+pymysql://' + dbinfo.user + ':' + dbinfo.password + '@' + dbinfo.host + '/' + course
    engine = sqlalchemy.create_engine(SQLALCHEMY_DATABASE_URI, echo=True)
    print(engine)
    Session = sessionmaker(bind=engine)
    session = Session()

    session.query(db_classes.AverageGrade).filter(db_classes.AverageGrade.Week == week).delete()
    groupAverage(session, week)