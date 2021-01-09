import sqlalchemy
from sqlalchemy.orm import sessionmaker
import db_classes
import dbinfo
import time
import sys
from sqlalchemy import Column, ForeignKey, Integer, String, DateTime

def summarizeEvaluations(session, student, role, week):
    #查询课程采用的rubrics
    rubrics = session.query(db_classes.Rubrics.RubricsName).all()

    #向定义好的Grade类中加入评价项目属性
    for i in range(len(rubrics)):
        if not hasattr(db_classes.Grade, rubrics[i][0]):
            setattr(db_classes.Grade, rubrics[i][0], Column(String(100)))

    #缓存分数
    scores = []

    #创建新分数记录
    new_grade = db_classes.Grade()
    new_grade.Week = week
    evaluatee = session.query(db_classes.Persons).filter(
        db_classes.Persons.id == student).first()
    new_grade.EvaluateeID = evaluatee.id
    new_grade.EvaluateeName = evaluatee.PersonName
    new_grade.DataSource = role #role == 1:学生 role == 2:TA role == 3:教授
    new_grade.InputDate = time.strftime("%Y-%m-%d")

    #统计每条记录参与计算平均分的人数，用来确认小组内是否有同学漏交。若每个人都提交了评价，则组内每个人被评价的次数相同，否则漏交的同学会比已提交的同学获得更多次评价。
    evaluatorNumber = 0

    for rubricsItem in range(1, len(rubrics) + 1):
        #对于每项ruburic：
        score = session.query(sqlalchemy.func.avg(db_classes.Evaluation.Score)).filter(
            sqlalchemy.and_(db_classes.Evaluation.EvaluateeID == student,
            db_classes.Evaluation.RubricsItem == rubricsItem,
            db_classes.Evaluation.Week == new_grade.Week,
            db_classes.Evaluation.Source == role)).all() #分数
        itemEvaluatorNumber = session.query(sqlalchemy.func.count(db_classes.Evaluation.Score)).filter(
            sqlalchemy.and_(db_classes.Evaluation.EvaluateeID == student,
            db_classes.Evaluation.RubricsItem == rubricsItem,
            db_classes.Evaluation.Week == new_grade.Week,
            db_classes.Evaluation.Source == role)).all() #评价人数
        if itemEvaluatorNumber[0][0] > evaluatorNumber: evaluatorNumber = itemEvaluatorNumber[0][0]
        scores.append(score[0][0])#加入缓存
    new_grade.EvaluatorNumber = evaluatorNumber

    #将缓存中的分数赋予new_grade的属性中
    for i in range(len(rubrics)):
        setattr(new_grade, rubrics[i][0], str(round(scores[i], 2) if scores[i] is not None else scores[i]))
    '''
    已弃用
    new_grade.KnowledgeAcquisition = round(scores[0], 2) if scores[0] is not None else scores[0]
    new_grade.Motivation = round(scores[1], 2) if scores[1] is not None else scores[1]
    new_grade.Communication = round(scores[2], 2) if scores[2] is not None else scores[2]
    new_grade.HandsOnSkills = round(scores[3], 2) if scores[3] is not None else scores[3]
    new_grade.ThinkingSkills = round(scores[4], 2) if scores[4] is not None else scores[4]
    new_grade.Responsibility = round(scores[5], 2) if scores[5] is not None else scores[5]
    new_grade.ProjectExecution = round(scores[6], 2) if scores[6] is not None else scores[6]
    '''
    session.add(new_grade)
    session.commit()

if __name__=='__main__':

    args = sys.argv #[filename, course, week]
    course = args[1]
    week = args[2]

    SQLALCHEMY_DATABASE_URI = \
        'mysql+pymysql://' + dbinfo.user + ':' + dbinfo.password + '@' + dbinfo.host + '/' + course
    engine = sqlalchemy.create_engine(SQLALCHEMY_DATABASE_URI, echo=True)
    Session = sessionmaker(bind=engine)
    session = Session()

    session.query(db_classes.Grade).filter(db_classes.Grade.Week == week).delete()
    students = session.query(db_classes.Persons.id).filter(db_classes.Persons.PersonRole == 1).all()

    roleNumber = session.query(sqlalchemy.func.count(db_classes.Roles.id)).first()[0]

    for role in range(1, roleNumber + 1):
        for student in students:
            studentid = student[0]
            summarizeEvaluations(session, studentid, role, week)
