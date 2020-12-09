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
    Password = Column(String(15))
    def __repr__(self):
        return '<Person %r>' % self.PersonName

class Courses(Base):
    __tablename__ = 'Courses'
    id = Column(Integer, primary_key=True)
    CourseID = Column(String(100), nullable=False)
    CourseName = Column(String(100))

class ElectiveLog(Base):
    __tablename__ = 'ElectiveLog'
    id = Column(Integer, primary_key=True)
    PersonName = Column(String(100), nullable=False)
    CourseName = Column(String(100), nullable=False)
    PersonRole = Column(Integer, ForeignKey('Roles.id'))
    InputDate = Column(DateTime, nullable = False)

class Roles(Base):
    __tablename__ = 'Roles'
    id = Column(Integer, primary_key=True)
    RoleName = Column(String(100), nullable=False)

class Loginlogs(Base):
    __tablename__ = 'Loginlogs'
    id = Column(Integer, primary_key=True)
    Username = Column(String(100), nullable=False)
    Ip = Column(String(100), nullable=False)
    InputDate = Column(DateTime, nullable = False)
