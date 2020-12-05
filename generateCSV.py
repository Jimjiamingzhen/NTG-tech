import sqlalchemy
from sqlalchemy.orm import sessionmaker
import db_classes
import dbinfo
import constants
import csv
import os
import codecs
import zipfile

week = 10
course = 'SDM242'

folder = r'C:\Users\Jim\Desktop\sdmtemp'
pathFile = os.path.join(folder, "%sWEEK%d.csv"%(course, week))
pathZip = os.path.join(folder, "%sWEEK%d.zip"%(course, week))
file = codecs.open(pathFile, 'w', "gbk")
csvWritter = csv.writer(file)


SQLALCHEMY_DATABASE_URI = \
        'mysql+pymysql://' + dbinfo.user + ':' + dbinfo.password + '@' + dbinfo.host + '/' + course
engine = sqlalchemy.create_engine(SQLALCHEMY_DATABASE_URI, echo=True)
Session = sessionmaker(bind=engine)
session = Session()
grades = session.query(db_classes.Grade).filter(db_classes.Grade.Week == week).all()
columns = db_classes.Grade.__table__.columns.keys()
csvWritter.writerow([ column for column in columns])
for grade in grades:
    csvWritter.writerow([getattr(grade, column) for column in columns])
file.close()
Zip = zipfile.ZipFile(pathZip,'a')
Zip.write(pathFile, "%sWEEK%d.csv"%(course, week), compress_type=zipfile.ZIP_DEFLATED)
Zip.close()
