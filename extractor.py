from tika import parser # pip install tika
import re
import win32com.client
import docx2txt
import os
import xlwt
import sys 
from xlwt import Workbook 

def content(path):
    try:
        if(path.endswith('.pdf')):
            raw = parser.from_file(path)
            file1=raw['content']  
            return file1
        elif(path.endswith('.docx')and not path.startswith('~$')):
            file1= docx2txt.process(path)
            return file1
        elif(path.endswith('.doc') and not path.startswith('~$')):
            path=doc2pdf(path)#first converting to pdf and get path
            raw = parser.from_file(path)
            file1=raw['content']#using path of pdf
            return file1
                       
    except Exception as e:
        print("Error at {} \n Error:{}".format(path,e))

def delete_word(path):
    try:
        path=os.path.abspath(path)
        os.remove(path)
        return True
    except Exception as e:
        print("Exception {}".format(e))
            

def doc2pdf(path):
    try:
        pf=sys.platform
        if(not path.endswith('.doc')):
            raise Exception(".doc expected but .{} give".format(path.split('.')[-1]))
        
        abs_path=os.path.abspath(path)
        pdf=abs_path.replace('.doc','.pdf')
        
        if(pf=='linux'):
            os.system("antiword {} > {}".format(abs_path,pdf))

        if (not os.path.isfile(pdf))and pf!='linux':
            word = win32com.client.Dispatch("Word.Application")
            word.visible = False
            wordDoc = word.Documents.Open(abs_path)
            wordDoc.SaveAs2(pdf, FileFormat = 17)
            wordDoc.Close()
            print("Created and closed {}".format(pdf))
            word.Quit()
            
        if os.path.isfile(abs_path) and os.path.isfile(pdf):
            if delete_word(abs_path):
                print("deleted {}".format(abs_path))
    
        return pdf
    
    except Exception as e:
        print("Exception {}".format(e))
        
        
def doc2docx(paths):
    try:
        pf=sys.platform
        if(not paths.endswith('.doc')):
            raise Exception(".doc expected but .{} give".format(paths.split('.')[-1]))

        abs_path=os.path.abspath(paths)
        docx=abs_path.replace('.doc','.docx')
        if(pf=='linux'):
            os.system("antiword {} > {}".format(abs_path,docx))

        if (not os.path.isfile(docx)) and pf!='linux':
            word = win32com.client.Dispatch("Word.Application")
            word.visible = False
            wordDoc = word.Documents.Open(abs_path)
            wordDoc.SaveAs2(docx, FileFormat = 16)
            wordDoc.Close()
            print("Created and closed {}".format(docx))
            word.Quit()
            
        if os.path.isfile(abs_path) and os.path.isfile(docx):
            if delete_word(abs_path):
                print("deleted {}".format(abs_path))
        
        return docx
    
    except Exception as e:
        print("Exception {}".format(e))
        
        
def emails(content):
    try:
        mail= re.findall("([a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+)", content)
        mail=list(set(mail))
        mail=[i for i in mail if(re.search('^[a-z0-9]+[\._]?[a-z0-9]+[@]\w+[.]\w{2,3}$',i))]
        return mail
    except Exception as e:
        print("Email Exception {}".format(e))
        return 

def mobile2(content):
    #start with zeroes
    try:
        num=[]
        mob_num = re.compile(r'(\s0)?(\s91)?(\+91-)?(\+91)?([6-9]{1})([0-9]{9})')
        for (_,_,_,_,c,d) in re.findall(mob_num, content):
            str=( c + d)
            if(str.startswith("+91-")):
                str= str.replace("+91-","")
            elif(str.startswith("+91")):
                str= str.replace("+91","")
            num.append(str)
        num=[int(i) for i in num if(len(i)==10)]
        num=list(set(num))
        return num
    except Exception as e:
        print("Mobile Exception {}".format(e))
        return 

wb = Workbook() 
  
# add_sheet is used to create sheet. 
sheet1 = wb.add_sheet('Sheet 1',cell_overwrite_ok=True) 

style = xlwt.easyxf('font: bold 1') 
cols=['file','Phone1','Phone2','Email1','Email2']
for i in range(len(cols)):
    sheet1.write(0, i, cols[i],style)
# path='/xampp/htdocs/DataExtractorSecured/storage/app/files/'
path='/Users/Aakarsh Teja.I/Desktop/larvel_projects/DataExtractorSecured/storage/app/public/files/'
pf=sys.platform
if(pf=='linux'):
    path='/var/www/workassistnew'+path

files= os.listdir(path)
for f in files:
    if(f.endswith('.doc')and not f.startswith('~$')):
        doc2pdf(path+f)

files= os.listdir(path)
row=1
for f in files:
    if(not f.startswith("~$")):
        print(f)
        con=content(path+f)
        num=mobile2(con)
        em=emails(con)
        if(num or em):
            sheet1.write(row, 0, f)
            if(len(num)>=1):
                sheet1.write(row, 1, num[0])
            if(len(num)>=2):
                sheet1.write(row, 2, num[1])
            if(len(em)>=1):
                sheet1.write(row, 3, em[0])
            if(len(em)>=2):
                sheet1.write(row, 4, em[1])
        row+=1
        

wb.save('storage/app/resume_data.xls')
print("Saved the file in below location\n{}".format(os.path.abspath('storage/app/resume_data.xls')))