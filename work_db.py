# %%
# -*- coding: utf-8 -*-
import sqlalchemy
from sqlalchemy.orm import sessionmaker
import pymysql
import dbinfo
import db_classes
from db_classes import Base
import constants
import pandas as pd
import db_classes_SDIM
import time

course = 'SDM232'
# %% connect engine
SQLALCHEMY_DATABASE_URI_COURSE = 'mysql+pymysql://' + dbinfo.user + ':' + dbinfo.password + '@' + dbinfo.host + '/' + course
engine_course = sqlalchemy.create_engine(SQLALCHEMY_DATABASE_URI_COURSE, echo=True)
# %% Create database from classes
Base.metadata.create_all(engine_course)


# %%  create session for database manipulation
Base.metadata.bind = engine_course
DBSession_couse = sessionmaker(bind=engine_course)
session = DBSession_couse()

# %% initiate the database
for role in constants.ROLES:
    if (session.query(db_classes.Roles.id).filter_by(RoleName=role).scalar() is None):
        new_role = db_classes.Roles(RoleName=role)
        session.add(new_role)

for group in constants.GROUPS:
    if (session.query(db_classes.Groups.id).filter_by(GroupID=group).scalar() is None):
        new_group = db_classes.Groups(GroupID=group)
        session.add(new_group)
'''
--------------------------------------------------------
'''
for rubric in constants.RUBRICS:
    if (session.query(db_classes.Rubrics.id).filter_by(RubricsName=rubric).scalar() is None):
        new_rubric = db_classes.Rubrics(RubricsName=rubric)
        session.add(new_rubric)

session.commit()

# %% initiate the students
person_file = './studentIDinfo.csv'
person_data = pd.read_csv(person_file) 
person_list = [person_data.iloc[i] for i in range(len(person_data['PersonID']))]
print('--------------------------')
for person in person_list:
    if (session.query(db_classes.Persons.id).filter_by(PersonID=str(person.PersonID)).scalar() is None):
        new_person = db_classes.Persons()
        new_person.PersonID = str(person.PersonID)
        new_person.PersonName = person.PersonName
        new_person.PersonNameE = person.PersonNameE
        new_person.Email = person.Email
        new_person.Affiliation = person.Affiliation
        new_person.StudentGroup = session.query(db_classes.Groups.id).filter_by(GroupID=str(person.StudentGroup)).first()[0]
        new_person.PersonRole = session.query(db_classes.Roles.id).filter_by(RoleName=person.PersonRole).first()[0]
        new_person.Password = str(person.PersonID)
        session.add(new_person)
session.commit()

#%% initiate students in SDIM database
SQLALCHEMY_DATABASE_URI_SDIM = 'mysql+pymysql://' + dbinfo.user + ':' + dbinfo.password + '@' + dbinfo.host + '/' + 'SDIM'
engine_SDIM = sqlalchemy.create_engine(SQLALCHEMY_DATABASE_URI_SDIM, echo=True)
Session_SDIM = sessionmaker(bind=engine_SDIM)
session_SDIM = Session_SDIM()

person_file = './studentIDinfo.csv'
person_data = pd.read_csv(person_file)
person_list = [person_data.iloc[i] for i in range(len(person_data['PersonID']))]

for person in person_list:
    new_election = db_classes_SDIM.ElectiveLog()
    new_election.PersonName = person.PersonName
    new_election.CourseName = course
    new_election.PersonRole = \
    session_SDIM.query(db_classes_SDIM.Roles.id).filter_by(RoleName=person.PersonRole).first()[0]
    new_election.InputDate = time.strftime("%Y-%m-%d")
    session_SDIM.add(new_election)
    if (session_SDIM.query(db_classes_SDIM.Persons.id).filter_by(PersonID=str(person.PersonID)).scalar() is None):
        new_person = db_classes_SDIM.Persons()
        new_person.PersonID = str(person.PersonID)
        new_person.PersonName = person.PersonName
        new_person.PersonNameE = person.PersonNameE
        new_person.Email = person.Email
        new_person.Affiliation = person.Affiliation
        new_person.Password = str(person.PersonID)
        session_SDIM.add(new_person)
session_SDIM.commit()


# %% insert a person
# new_person = db_classes.Persons(PersonName='test')
# session.add(new_person)
# session.commit()
# %%