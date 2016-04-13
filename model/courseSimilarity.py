#!/usr/bin/python
import MySQLdb

from stemming.porter2 import stem
import math
from textblob import TextBlob as tb
import math
import string
import operator

from testingMajorSimilarity import majorRelations

idfcache = {}

def tf(word, blob):
    return float(blob.words.count(word))/ float(len(blob.words))

def n_containing(word, bloblist):
    return sum(1 for blob in bloblist if word in blob)

def idf(word, bloblist):
    if(word in idfcache):
        return idfcache[word]

        
    answer = float(math.log(len(bloblist)) / float((1 + n_containing(word, bloblist))))
    idfcache[word] = answer
    return answer

def tfidf(word, blob, bloblist):
    return tf(word, blob) * idf(word, bloblist)


class Database:

    def __init__(self):
        self.courses = {}
    def addCourse(self,course):
        self.courses[course.id] = course
        
    def getCourse(self,cid):
        return self.courses[cid]
    
class Course:
 
    def __init__(self,cid_,name_,dep_,num_,desc_):
        self.cid = cid_
        self.name = name_
        self.department = dep_
        self.number = num_
        self.description = desc_
        self.blob = ""
        self.similarities = []
        self.scores = {}
        self.stemming = True
        self.courseLevels = True
        self.majorRelations = True
        self.stemmedTitle = ""
    def setBlob(self,blob_):
        paragraph = filter(lambda x: x in printable, blob_)
        blob = tb(paragraph)
        newBlob = ""
        if(self.stemming):
            for word in blob.words:
                newBlob+=" "+(stem(word.lower()))
                
        self.blob = tb(newBlob)

    def getBlob(self):
        return self.blob

    def setWords(self,array):
        self.similarities = array

    def getWords(self):
        return self.similarities

    def setScore(self,course2,score):
        thousands = self.number/1000
        thousands2 = course2.number/1000

        hundreds2 = course2.number/100
        tens2 = course2.number/10
        numRep1 = list(str(self.number))
        numRep2 = list(str(course2.number))
        #print thousands,thousands2, "=>",

        ##this is a mess
        
        coeff = 1.0
        if(self.courseLevels):
            if(numRep2[1] == '9' and numRep2[2] == '9'):
                coeff= 0.0;
            elif(thousands == 2 and thousands2 == 2):
                coeff = 2.0
            elif(thousands == 4 and thousands2==4):
                coeff = 4.0
            elif(thousands == 1 and thousands2==1):
                coeff = 1.5
            elif(thousands == 4 and thousands2==1):
                coeff = 0.25
            elif(thousands == 2 and thousands2==1):
                coeff = 0.5
            elif(thousands == 1 and thousands2==2):
                coeff = 1.2
            elif(thousands == 1 and thousands2==4):
                coeff = 0.8
            elif(thousands == 1 and thousands2==6):
                coeff = .05
            elif(thousands == 2 and thousands2==6):
                coeff = .15
            elif(thousands == 4 and thousands2==6):
                coeff = 2.0
            elif(thousands == 6 and thousands2==6):
                coeff = 6.0
            else:
                coeff = 1.0
        else:
            coeff *= 1.0
        
        if(self.majorRelations):
            if(self.department == ""):
                coeff *=1 
            else:
                coeff *= (majorRelations(self.department,course2.department)+0.02)
           
        #print coeff
        self.scores[course2] = score*coeff
            
    def getScore(self,course2):
        return self.scores[course2]

def similarity(array1,array2):
    ##name similarity too!
    #print array1,array2
    if(len(array1)==0 or len(array2)==0):
        return -1
    
    score1 = 0.0
    score2 = 0.0
    dict_ = {}
    '''
    for item in array1:
        if(item[0] in dict_):
            dict_[item[0]] += float(item[1])
        else:
            dict_[item[0]] = float(item[1])

            
    for elem in array2:
        if(elem[0] in dict_):
             score1+= float(dict_[elem[0]])*float(elem[1])
             #print 
    '''
    
    #print "SIMILAR RELEVANT WORDS MULTIPLIER MACHINE"
    
    for item in array1:
        for elem in array2:
            if(item[0] == elem[0]):
                score1 += float(item[1]*elem[1])
                #print item[0],item[1],":",elem[0],elem[1]
    


    
    return score1
def twoCourses(course1str,course2str):
    ##PRINTING
    print "Comparing ",course1str," to ",course2str

    
    similarities = {}

    ##NUMBER OF RELEVANT WORDS TO CONSIDER
    cutoff_limit = 30

    ##GENERATE COURSE1 from course1 (string name),
    cur.execute("SELECT * FROM Courses WHERE name = '"+course1str+"'")
    tup = cur.fetchall()[0]
    course1 = Course(tup[0],tup[1],tup[2],tup[3],tup[4])
    course1.setBlob(course1.description)
    blob1 = course1.getBlob()

    ##GENERATE COURSE2 from course2 (string name),
    cur.execute("SELECT * FROM Courses WHERE name = '"+course2str+"'") 
    tup = cur.fetchall()[0]
    course2 = Course(tup[0],tup[1],tup[2],tup[3],tup[4])
    course2.setBlob(course2.description)
    blob2 = course2.getBlob()

    print course1.name
    ##GET MOST RELEVANT WORDS IN COURSE1 DESCRIPTION
    scores = {word: tfidf(word, blob1, bloblist) for word in blob1.words}
    sorted_words = sorted(scores.items(), key=lambda x: x[1], reverse=True)
    ##LIMIT IS THE AMOUNT OF RELEVANT WORDS TO CONSIDER,
    ##or total amount of words, whichever is less
    limit = min(cutoff_limit,len(sorted_words))
    ##PRINTING MOST RELEVANT WORDS JUST CALCULATED
    for word, score in sorted_words[:limit]:
        print("\tWord: {}, TF-IDF: {}".format(word, round(score, 5)))
    ##SET THOSE COMMON WORDS TO THE COURSE1 OBJ.
    course1.setWords(sorted_words[:limit])

    
    print course2.name
    ##GET MOST RELEVANT WORDS IN COURSE1 DESCRIPTION
    scores = {word: tfidf(word, blob2, bloblist) for word in blob2.words}
    sorted_words = sorted(scores.items(), key=lambda x: x[1], reverse=True)
    ##LIMIT IS THE AMOUNT OF RELEVANT WORDS TO CONSIDER,
    ##or total amount of words, whichever is less
    limit = min(cutoff_limit,len(sorted_words))
    ##PRINTING MOST RELEVANT WORDS JUST CALCULATED
    for word, score in sorted_words[:limit]:
        print("\tWord: {}, TF-IDF: {}".format(word, round(score, 5)))
    ##SET THOSE COMMON WORDS TO THE COURSE2 OBJ.
    course2.setWords(sorted_words[:limit])

    ##SETTING SIMILARITY SCORE OF COURSE1 OF COURSE2
    course1.setScore(course2,similarity(course1.getWords(),course2.getWords()))
    score = course1.getScore(course2)
    ##PRINTING THE SCORES SO YOU CAN SEE WHAT'S UP!
    print course1.name," , ",course2.name," ->" ,course1.getScore(course2)
    

    return score


def courseSimilarity(course1):
    print "\n \n ",course1," \n"
    similarities = {}
    
    cutoff_limit = 30
    
    cur.execute("SELECT * FROM Courses WHERE name = '"+course1+"'")
    
    tup = cur.fetchall()[0]
    course = Course(tup[0],tup[1],tup[2],tup[3],tup[4])
    course.setBlob(course.description)
    blob = course.getBlob()

    
    scores = {word: tfidf(word, blob, bloblist) for word in blob.words}
    sorted_words = sorted(scores.items(), key=lambda x: x[1], reverse=True)
    

    limit = max(cutoff_limit,len(sorted_words))
    for word, score in sorted_words[:limit]:
        print("\tWord: {}, TF-IDF: {}".format(word, round(score, 5)))
    course.setWords(sorted_words[:limit])

    
    for c in courseList:
        i = c.cid
        blob = c.getBlob()

        scores = {word: tfidf(word, blob, bloblist) for word in blob.words}
        sorted_words = sorted(scores.items(), key=lambda x: x[1], reverse=True)

        c.setWords(sorted_words)
        limit = min(cutoff_limit,len(sorted_words))
        course.setScore(c,similarity(course.getWords(),sorted_words[:limit]))


    sorted_courses = sorted(courseList, key=lambda x: course.getScore(x), reverse=True)
    
    for cour in sorted_courses[0:8]:
        #print cour.cid," ",cour.department," ",cour.name," ",course.getScore(cour)
        print cour.department,cour.number,cour.name
            


    score = 0
    return score

def customCourseSimilarity(level,description,major):

    similarities = {}
    
    cutoff_limit = 30
    if(major == ""):
        major = "Computer Science"
    cur.execute("SELECT id FROM Departments WHERE name = '"+major+"'")
    
    tup = cur.fetchall()[0]
    course = Course(1,"custom",tup[0],level,description)
    course.setBlob(course.description)
    blob = course.getBlob()

    
    scores = {word: tfidf(word, blob, bloblist) for word in blob.words}
    sorted_words = sorted(scores.items(), key=lambda x: x[1], reverse=True)

    limit = max(cutoff_limit,len(sorted_words))
    course.setWords(sorted_words[:limit])

    
    for c in courseList:
        i = c.cid
        blob = c.getBlob()

        scores = {word: tfidf(word, blob, bloblist) for word in blob.words}
        sorted_words = sorted(scores.items(), key=lambda x: x[1], reverse=True)

        c.setWords(sorted_words)
        limit = min(cutoff_limit,len(sorted_words))
        course.setScore(c,similarity(course.getWords(),sorted_words[:limit]))


    sorted_courses = sorted(courseList, key=lambda x: course.getScore(x), reverse=True)
    
    for cour in sorted_courses[0:15]:
        #print cour.cid," ",cour.department," ",cour.name," ",course.getScore(cour)
        print cour.department,cour.number,cour.name
            


    score = 0
    return score



    

db = MySQLdb.connect(host="localhost",    # your host, usually localhost
                     user="root",         # your username
                     passwd="root",
                     port=8889,# your password
                     unix_socket='/Applications/MAMP/tmp/mysql/mysql.sock',
                     db="suggestr")        # name of the data base

cur = db.cursor()

coursesData = []

cur.execute("SELECT * FROM courses")
courses = cur.fetchall()


cur.execute("SELECT Description FROM Courses")
allCourses = cur.fetchall()

printable = set(string.printable)

bloblist = []
for item in allCourses:
    if(item[0]!= ""):
        line = filter(lambda x: x in printable, item[0])
        
        blob = tb(line)
        for item in blob.words:
            item = item.lower()
        bloblist.append(blob)



courseList = []

cur.execute("SELECT * FROM courses")
courses = cur.fetchall()
for item in courses:
    tup = item
    course = Course(tup[0],tup[1],tup[2],tup[3],tup[4])
    course.setBlob(course.description)
    courseList.append(course)
    
'''       
for i, blob in enumerate(bloblist):
    print("Top words in document {}".format(i + 1))
    scores = {word: tfidf(word, blob, bloblist) for word in blob.words}
    sorted_words = sorted(scores.items(), key=lambda x: x[1], reverse=True)
    for word, score in sorted_words[:3]:
        print("\tWord: {}, TF-IDF: {}".format(word, round(score, 5)))

'''
'''
crs = ["Calculus I","Calculus II","Computer Organization","Foundations of Computer Science","Computer Science I","Beginning Programming for Engineers","Machine and Computational Learning","Data Science",\
       "Introduction to Algorithms","Data Structures",\
       "Networking Laboratory I","Numerical Computing"]
crs = ["General Psychology","Programming For Cognitive Science And Artific","Sculpture I"]
crs = ["Introduction To Visual Communication","Research in Biochemistry/Biophysics"]
crs = ["Introduction to Materials Engineering","Molecular Biochemistry I","Energy Politics"\
       ,"Nature and Society","Options, Futures, and Derivatives Markets","A Passion for Physics"\
       ,"Computer Music","Deep Listening","Construction Systems","Experimental Physics","Minds and Machines","Introduction to Engineering Analysis"]
for item in crs:
    try:
        courseSimilarity(item)
    except:
        print "Fail"
'''
#twoCourses("Introduction To Visual Communication","Research in Biochemistry/Biophysics")


#courseSimilarity("")
#customCourseSimilarity(2000,"Networking server big data","Computer Science")
#customCourseSimilarity(2000,"Big data finance finance finance prediction","Computer Science")
#customCourseSimilarity(4000,"I want a course that talks about philosophy and metaphysics","")
