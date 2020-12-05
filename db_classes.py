import sqlalchemy
from sqlalchemy import Column, ForeignKey, Integer, String, DateTime
from sqlalchemy.ext.declarative import declarative_base

Base = declarative_base()

class Persons(Base):
    __tablename__ = 'Persons'
    id = Column(Integer, primary_key=True)
    PersonID = Column(String(100), nullable=False)
    PersonName = Column(String(100), nullable=False)
    PersonNameE = Column(String(100))
    Email = Column(String(100))
    Affiliation = Column(String(100))
    StudentGroup = Column(Integer, ForeignKey('Groups.id'))
    PersonRole = Column(Integer, ForeignKey('Roles.id'))
    IDInGroup = Column(Integer)
    Password = Column(String(15))
    def __repr__(self):
        return '<Person %r>' % self.PersonName

class Groups(Base):
    __tablename__ = 'Groups'
    id = Column(Integer, primary_key=True)
    GroupID = Column(String(100), nullable=False)
    GroupName = Column(String(100))

class Roles(Base):
    __tablename__ = 'Roles'
    id = Column(Integer, primary_key=True)
    RoleName = Column(String(100), nullable=False)

class Rubrics(Base):
    __tablename__ = 'Rubrics'
    id = Column(Integer, primary_key=True)
    RubricsName = Column(String(100), nullable=False)

class Evaluation(Base):
    __tablename__ = 'Evaluation'
    id = Column(Integer, primary_key=True)
    Week = Column(Integer)
    EvaluateeID = Column(Integer, ForeignKey('Persons.id'), nullable=False)
    EvaluatorID = Column(Integer, ForeignKey('Persons.id'), nullable=False)
    RubricsItem = Column(Integer, ForeignKey('Rubrics.id'))
    Score = Column(String(100), nullable=False)
    Comment = Column(String(1000), nullable=False)
    InputDate = Column(DateTime, nullable = False)
    Source = Column(Integer, ForeignKey('Roles.id'), nullable=False)

class Grade(Base):
    __tablename__ = 'Grade'
    id = Column(Integer, primary_key=True)
    Week = Column(Integer)
    EvaluateeID = Column(Integer, ForeignKey('Persons.id'), nullable=False)
    EvaluateeName = Column(String(100), nullable=False)
    KnowledgeAcquisition = Column(String(100))
    Motivation = Column(String(100))
    Communication = Column(String(100))
    HandsOnSkills = Column(String(100))
    ThinkingSkills = Column(String(100))
    Responsibility = Column(String(100))
    ProjectExecution = Column(String(100))
    DataSource = Column(Integer, ForeignKey('Roles.id'), nullable=False)
    EvaluatorNumber = Column(Integer, nullable=False)
    InputDate = Column(DateTime, nullable = False)

class TotalGrade(Base):
    __tablename__ = 'TotalGrade'
    id = Column(Integer, primary_key=True)
    Week = Column(Integer)
    EvaluateeID = Column(Integer, ForeignKey('Persons.id'), nullable=False)
    EvaluateeName = Column(String(100), nullable=False)
    KnowledgeAcquisition = Column(String(100))
    Motivation = Column(String(100))
    Communication = Column(String(100))
    HandsOnSkills = Column(String(100))
    ThinkingSkills = Column(String(100))
    Responsibility = Column(String(100))
    ProjectExecution = Column(String(100))
    weightST = Column(String(100))
    weightTA = Column(String(100))
    weightIN = Column(String(100))
    InputDate = Column(DateTime, nullable = False)

class AverageGrade(Base):
    __tablename__ = 'AverageGrade'
    id = Column(Integer, primary_key=True)
    Week = Column(Integer)
    StudentGroup =  Column(Integer, ForeignKey('Groups.id'))
    KnowledgeAcquisition = Column(String(100))
    Motivation = Column(String(100))
    Communication = Column(String(100))
    HandsOnSkills = Column(String(100))
    ThinkingSkills = Column(String(100))
    Responsibility = Column(String(100))
    ProjectExecution = Column(String(100))
    InputDate = Column(DateTime, nullable = False)

class SubmitRecord(Base):
    __tablename__ = 'SubmitRecord'
    id = Column(Integer, primary_key=True)
    Evaluator = Column(String(100), nullable=False)
    Week = Column(Integer)
    InputDate = Column(DateTime, nullable = False)

class Loginlogs(Base):
    __tablename__ = 'Loginlogs'
    id = Column(Integer, primary_key=True)
    Username = Column(String(100), nullable=False)
    Ip = Column(String(100), nullable=False)
    InputDate = Column(DateTime, nullable = False)



