import sqlalchemy
from sqlalchemy.orm import sessionmaker
import db_classes
import dbinfo
import csv
import os
import codecs
import zipfile
import sys

args = sys.argv  # [filename, course, week, weighIN, weightTA, weightST]
course = args[1]
week = args[2]

SQLALCHEMY_DATABASE_URI = \
        'mysql+pymysql://' + dbinfo.user + ':' + dbinfo.password + '@' + dbinfo.host + '/' + course
engine = sqlalchemy.create_engine(SQLALCHEMY_DATABASE_URI, echo=True)
Session = sessionmaker(bind=engine)
session = Session()

folder = r'D:\xampp\htdocs\test\RESULT'

pathGradeFile = os.path.join(folder, r"\temp\evaluations%sWEEK%d.csv"%(course, week))
evaluationsFile = codecs.open(pathGradeFile, 'w', "gbk")
evaluationsWritter = csv.writer(evaluationsFile)
evaluationsColumns = db_classes.Grade.__table__.columns.keys()
grades = session.query(db_classes.Grade).filter(db_classes.Grade.Week == week).all()
evaluationsWritter.writerow([column for column in evaluationsColumns])
for grade in grades:
    evaluationsWritter.writerow([getattr(grade, column) for column in evaluationsColumns])
evaluationsFile.close()

pathTotalGradeFile = os.path.join(folder, r"\temp\totalGrade%sWEEK%d.csv"%(course, week))
totalGradeFile = codecs.open(pathTotalGradeFile, 'w', "gbk")
totalGradeWritter = csv.writer(totalGradeFile)
totalGradeColumns = db_classes.TotalGrade.__table__.columns.keys()
totalGrades = session.query(db_classes.TotalGrade).filter(db_classes.TotalGrade.Week == week).all()
totalGradeWritter.writerow([column for column in totalGradeColumns])
for grade in totalGrades:
    totalGradeWritter.writerow([getattr(grade, column) for column in totalGradeColumns])
totalGradeFile.close()

pathZip = os.path.join(folder, "%sWEEK%d.zip"%(course, week))
Zip = zipfile.ZipFile(pathZip,'a')
Zip.write(pathGradeFile, "gradeFromDifferentSource%sWEEK%d.csv"%(course, week), compress_type=zipfile.ZIP_DEFLATED)
Zip.write(pathTotalGradeFile, "totalGrade%sWEEK%d.csv"%(course, week), compress_type=zipfile.ZIP_DEFLATED)
Zip.close()

os.remove(os.path.join(folder, r"\temp"))