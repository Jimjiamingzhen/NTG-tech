import dbinfo
import pandas as pd
import sqlalchemy
from sqlalchemy.orm import sessionmaker
import db_classes

SQLALCHEMY_DATABASE_URI = 'mysql+pymysql://' + dbinfo.user + ':' + dbinfo.password + '@' + dbinfo.host + '/' + 'SDM272'
engine = sqlalchemy.create_engine(SQLALCHEMY_DATABASE_URI, echo=True)
Session = sessionmaker(bind=engine)
session = Session()

evaluations_file = r'D:\xampp\htdocs\test\EVA.csv'
evaluations_data = pd.read_csv(evaluations_file)
evaluations_list = [evaluations_data.iloc[i] for i in range(len(evaluations_data['id']))]
record_file = r'D:\xampp\htdocs\test\submitrecord.csv'
record_data = pd.read_csv(record_file,encoding='gb18030')
record_list = [record_data.iloc[i] for i in range(len(record_data['id']))]

for evaluation in evaluations_list:
    new_evaluation = db_classes.Evaluation()
    new_evaluation.EvaluateeID = int(evaluation.EvaluateeID)
    new_evaluation.Week = int(evaluation.Week)
    new_evaluation.EvaluatorID = int(evaluation.EvaluatorID)
    new_evaluation.RubricsItem = int(evaluation.RubricsItem)
    new_evaluation.Score = str(evaluation.Score)
    new_evaluation.InputDate = str(evaluation.InputDate)
    new_evaluation.Source = int(evaluation.Source)
    session.add(new_evaluation)
    session.commit()

for record in record_list:
    new_record = db_classes.SubmitRecord()
    new_record.Evaluator = str(record.Evaluator)
    new_record.Week = int(record.Week)
    new_record.InputDate = record.InputDate
    session.add(new_record)
    session.commit()