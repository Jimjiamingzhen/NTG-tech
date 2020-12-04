# -*- coding: utf-8 -*-

import sqlalchemy
import dbinfo
import db_classes
from sqlalchemy.orm import sessionmaker
from pyecharts.charts import Radar, Page
from pyecharts.components import Table, Image
from pyecharts import options as opts
import numpy

def radar(student, studentName, course, week, studentScore, avgScore, commentTA, commentIN, dir):
    page = Page(page_title='%s %s WEEK%d 评分'%(studentName, course, week),layout=Page.SimplePageLayout)
    studentRawData = [studentScore]
    avgRawData = [avgScore]
    rubricsItem = [{"name": "KnowledgeAcquisition", "max": 5, "min": 0},
			    {"name": "Motivation", "max": 5, "min": 0},
			    {"name": "Communication", "max": 5, "min": 0},
			    {"name": "HandsOnSkills", "max": 5, "min": 0},
			    {"name": "ThinkingSkills", "max": 5, "min": 0},
			    {"name": "Responsibility", "max": 5, "min": 0},
			    {"name": "ProjectExecution", "max": 5, "min": 0}]
    radarChart = Radar()
    radarChart.add_schema(schema=rubricsItem,shape="polygon")
    radarChart.add('WEEK%d %s'%(week, studentName), studentRawData, color="#1F1C18")
    radarChart.add('WEEK%d Class Average'%week, avgRawData, color="#94060A", is_selected = False)
    table = Table()

    headers = ["Rubrics Item", "Score", "Class Average", "Instructor Comment", "TA Comment"]
    rows = [
        ["Knowledge Acquisition", studentScore[0], avgScore[0], commentIN[0],  commentTA[0]],
        ["Motivation", studentScore[1], avgScore[1], commentIN[1], commentTA[1]],
        ["Communication", studentScore[2], avgScore[2], commentIN[2], commentTA[2]],
        ["Hands-On Skills", studentScore[3], avgScore[3], commentIN[3], commentTA[3]],
        ["ThinkingSkills", studentScore[4], avgScore[4], commentIN[4], commentTA[4]],
        ["Responsibility", studentScore[5], avgScore[5], commentIN[5], commentTA[5]],
        ["Project Execution", studentScore[6], avgScore[6], commentIN[6], commentTA[6]],
    ]
    table.add(headers, rows).set_global_opts(
        title_opts=opts.ComponentTitleOpts(title="Comments")
    )

    page.add(radarChart)
    page.add(table)
    page.render("%s\%dWEEK%d.html"%(dir, student, week))

def generateRadarMap(session, student, week, course):
    courseid = session.query(db_classes.Courses).filter(
                            db_classes.Courses.CourseID == course).first().id
    gradeRecord = session.query(db_classes.TotalGrade).filter(
        sqlalchemy.and_(db_classes.TotalGrade.EvaluateeID == student,
                        db_classes.TotalGrade.Week == week,
                        db_classes.TotalGrade.Course == courseid)).first()
    studentName = gradeRecord.EvaluateeName
    K = gradeRecord.KnowledgeAcquisition
    M = gradeRecord.Motivation
    C = gradeRecord.Communication
    H = gradeRecord.HandsOnSkills
    T = gradeRecord.ThinkingSkills
    R = gradeRecord.Responsibility
    P = gradeRecord.ProjectExecution
    studentScore = [K, M, C, H, T, R, P]

    Average = session.query(db_classes.AverageGrade).filter(
        sqlalchemy.and_(db_classes.AverageGrade.Week == week,
                        db_classes.AverageGrade.Course == courseid,
                        db_classes.AverageGrade.StudentGroup == 6)).first()
    AK = Average.KnowledgeAcquisition
    AM = Average.Motivation
    AC = Average.Communication
    AH = Average.HandsOnSkills
    AT = Average.ThinkingSkills
    AR = Average.Responsibility
    AP = Average.ProjectExecution
    avgScore = [AK, AM, AC, AH, AT, AR, AP]

    commentTAQuery = session.query(db_classes.Evaluation.Comment).filter(
        sqlalchemy.and_(db_classes.Evaluation.EvaluateeID == student,
                        db_classes.Evaluation.Week == week,
                        db_classes.Evaluation.Course == courseid,
                        db_classes.Evaluation.Source == 2)).all()
    commentTA = []
    for comment in commentTAQuery:
        if comment[0] == 'nan':
            commentTA.append(" ")
        else:
            commentTA.append(comment[0])

    commentINQuery = session.query(db_classes.Evaluation.Comment).filter(
        sqlalchemy.and_(db_classes.Evaluation.EvaluateeID == student,
                        db_classes.Evaluation.Week == week,
                        db_classes.Evaluation.Course == courseid,
                        db_classes.Evaluation.Source == 3)).all()
    commentIN = []
    for comment in commentINQuery:
        if comment[0] == 'nan':
            commentIN.append(" ")
        else:
            commentIN.append(comment[0])
    radar(student, studentName, course, week, studentScore, avgScore, commentTA, commentIN, r'.\radarMaps\WEEK%d' % week)

if __name__ == '__main__':
    SQLALCHEMY_DATABASE_URI = 'mysql+pymysql://' + dbinfo.user + ':' + dbinfo.password + '@' + dbinfo.host + '/' + dbinfo.database
    engine = sqlalchemy.create_engine(SQLALCHEMY_DATABASE_URI, echo=True)
    Session = sessionmaker(bind=engine)
    session = Session()
    personNumber = session.query(sqlalchemy.func.count(db_classes.Persons.id)).all()[0][0]
    studentNumber = session.query(sqlalchemy.func.count(db_classes.Persons.id)).filter(db_classes.Persons.PersonRole == 1).all()[0][0]
    week = 4
    course = 'SDM242'
    for student in range(personNumber - studentNumber + 1, personNumber + 1):
        generateRadarMap(session, student, week, course)




