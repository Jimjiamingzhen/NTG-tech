# %%
# -*- coding: utf-8 -*-
import sqlalchemy
from sqlalchemy.orm import sessionmaker
import pymysql
import dbinfo
import db_classes_SDIM
from db_classes_SDIM import Base
import constants
import pandas as pd

# %% connect engine
SQLALCHEMY_DATABASE_URI = 'mysql+pymysql://' + dbinfo.user + ':' + dbinfo.password + '@' + dbinfo.host + '/' + 'SDIM'
engine = sqlalchemy.create_engine(SQLALCHEMY_DATABASE_URI, echo=True)
print(engine)
# %% Create database from classes
Base.metadata.create_all(engine)


# %%  create session for database manipulation
Base.metadata.bind = engine
DBSession = sessionmaker(bind=engine)
session = DBSession()

for role in constants.ROLES:
    if (session.query(db_classes_SDIM.Roles.id).filter_by(RoleName=role).scalar() is None):
        new_role = db_classes_SDIM.Roles(RoleName=role)
        session.add(new_role)

for course in constants.COURSES:
    if (session.query(db_classes_SDIM.Courses.id).filter_by(CourseID=course[0]).scalar() is None):
        new_course = db_classes_SDIM.Courses(CourseID = course[0], CourseName = course[1])
        session.add(new_course)

session.commit()

