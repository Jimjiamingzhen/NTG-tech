# -*- coding: utf-8 -*-

import sqlalchemy
import dbinfo
import db_classes
from sqlalchemy.orm import sessionmaker
from pyecharts.charts import Radar, Page
from pyecharts.components import Table, Image
from pyecharts import options as opts
import numpy
import sys
import os
from sqlalchemy import Column, ForeignKey, Integer, String, DateTime

def radar(student, studentName, course, week, rubrics, studentScore, avgScore, commentor, comment, dir):
    page = Page(page_title='%s %s WEEK%d 评分'%(studentName, course, week),layout=Page.SimplePageLayout)
    studentRawData = [studentScore]
    avgRawData = [avgScore]
    rubricsItem = []
    for i in range(len(rubrics)):
        rubricsItem.append({"name": "%s"%rubrics[i][0], "max": 5, "min": 0})
    '''
    弃用
    rubricsItem = [{"name": "KnowledgeAcquisition", "max": 5, "min": 0},
			    {"name": "Motivation", "max": 5, "min": 0},
			    {"name": "Communication", "max": 5, "min": 0},
			    {"name": "HandsOnSkills", "max": 5, "min": 0},
			    {"name": "ThinkingSkills", "max": 5, "min": 0},
			    {"name": "Responsibility", "max": 5, "min": 0},
			    {"name": "ProjectExecution", "max": 5, "min": 0}]
	'''
    radarChart = Radar()
    radarChart.add_schema(schema=rubricsItem,shape="polygon")
    radarChart.add('WEEK%d %s'%(week, studentName), studentRawData, color="#1F1C18")
    radarChart.add('WEEK%d Class Average'%week, avgRawData, color="#94060A", is_selected = False)
    tableScores = Table()

    scoreHeaders = ["Rubrics Item", "Score", "Class Average"]
    scoreRows = []
    for i in range(len(rubrics)):
        print(rubrics)
        print(studentScore)
        print(avgScore)
        scoreRows.append([rubrics[i][0], studentScore[i], avgScore[i]])

    '''
    弃用
    scoreRows = [
        ["Knowledge Acquisition", studentScore[0], avgScore[0]],
        ["Motivation", studentScore[1], avgScore[1]],
        ["Communication", studentScore[2], avgScore[2]],
        ["Hands-On Skills", studentScore[3], avgScore[3]],
        ["ThinkingSkills", studentScore[4], avgScore[4]],
        ["Responsibility", studentScore[5], avgScore[5]],
        ["Project Execution", studentScore[6], avgScore[6]]
    ]
    '''

    tableScores.add(scoreHeaders, scoreRows).set_global_opts(
        title_opts=opts.ComponentTitleOpts(title="Scores")
    )

    tableComments = Table()
    commentHeaders = ["Commentor","Comment"]
    commentRows = []
    for i in range(len(commentor)):
        commentRows.append([commentor[i], comment[i]])
    tableComments.add(commentHeaders, commentRows).set_global_opts(
        title_opts=opts.ComponentTitleOpts(title="Comments")
    )

    page.add(radarChart)
    page.add(tableScores)
    page.add(tableComments)
    page.render("%s/%dWEEK%d.html"%(dir, student, week))
    #page.render("%s\%dWEEK%d.html"%(dir, student, week))

def generateRadarMap(session, student, week, course, path):
    #查询课程采用的rubrics
    rubrics = session.query(db_classes.Rubrics.RubricsName).all()

    #向定义好的类中加入rubrics属性
    for i in range(len(rubrics)):
        if not hasattr(db_classes.TotalGrade, rubrics[i][0]):
            setattr(db_classes.TotalGrade, rubrics[i][0], Column(String(100)))
    for i in range(len(rubrics)):
        if not hasattr(db_classes.AverageGrade, rubrics[i][0]):
            setattr(db_classes.AverageGrade, rubrics[i][0], Column(String(100)))


    #查询学生的分数
    gradeRecord = session.query(db_classes.TotalGrade).filter(
        sqlalchemy.and_(db_classes.TotalGrade.EvaluateeID == student,
                        db_classes.TotalGrade.Week == week)).first()
    #学生名字
    studentName = gradeRecord.EvaluateeName
    #缓存分数和班级平均分
    studentScore = []
    avgScore = []

    #读取分数
    for i in range(len(rubrics)):
        studentScore.append(gradeRecord.__dict__[rubrics[i][0]] if gradeRecord.__dict__[rubrics[i][0]] is not None else 0)

    #查询平均分
    Average = session.query(db_classes.AverageGrade).filter(
        sqlalchemy.and_(db_classes.AverageGrade.Week == week,
                        db_classes.AverageGrade.StudentGroup == None)).first()

    #读取分数
    for i in range(len(rubrics)):
        avgScore.append(Average.__dict__[rubrics[i][0]] if Average.__dict__[rubrics[i][0]] is not None else 0)

    commentTAQuery = session.query(db_classes.Comments).filter(
        sqlalchemy.and_(db_classes.Comments.EvaluateeID == student,
                        db_classes.Comments.Week == week,
                        db_classes.Comments.Source == 2)).all()

    commentINQuery = session.query(db_classes.Comments).filter(
        sqlalchemy.and_(db_classes.Comments.EvaluateeID == student,
                        db_classes.Comments.Week == week,
                        db_classes.Comments.Source == 3)).all()
    commentor = []
    comment = []
    for commentIN in commentINQuery:
        INname = session.query(db_classes.Persons.PersonName).filter(db_classes.Persons.id == commentIN.EvaluatorID).first()[0]
        commentor.append(INname)
        comment.append(commentIN.Comment)
    for commentTA in commentTAQuery:
        TAname = session.query(db_classes.Persons.PersonName).filter(db_classes.Persons.id == commentTA.EvaluatorID).first()[0]
        commentor.append(TAname)
        comment.append(commentTA.Comment)

    radar(student, studentName, course, week, rubrics, studentScore, avgScore, commentor, comment, path)

if __name__ == '__main__':

    args = sys.argv #[filename, course, week]
    course = args[1]
    week = int(args[2])
    SQLALCHEMY_DATABASE_URI = 'mysql+pymysql://' + dbinfo.user + ':' + dbinfo.password + '@' + dbinfo.host + '/' + course
    engine = sqlalchemy.create_engine(SQLALCHEMY_DATABASE_URI, echo=True)
    Session = sessionmaker(bind=engine)
    session = Session()

    students = session.query(db_classes.Persons.id).filter(db_classes.Persons.PersonRole == 1).all()

    folder = r'/opt/lampp/htdocs/SDIM/RESULT'
    #folder = r'D:\xampp\htdocs\test\RESULT'
    tempPath = os.path.join(folder, r"temp")
    if not os.path.exists(tempPath):
        os.mkdir(tempPath)
    radarMapPath = os.path.join(tempPath, r"radarMap")
    if not os.path.exists(radarMapPath):
        os.mkdir(radarMapPath)
    for student in students:
        studentid = student[0]
        generateRadarMap(session, studentid, week, course, radarMapPath)




