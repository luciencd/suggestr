#!/usr/bin/python
import MySQLdb

import math
    
def betterRelations(major1,major2):
    
    cur.execute("SELECT id FROM Departments WHERE name = '"+major1+"'")
    tup = cur.fetchall();
    id1 = str(tup[0][0])

    
    cur.execute("SELECT id FROM Departments WHERE name = '"+major2+"'")
    tup = cur.fetchall();
    id2 = str(tup[0][0])
    
    ##getting scores from major1's relations
    cur.execute("SELECT score FROM MajorRelations WHERE source_id = "+id1);
    array1 = cur.fetchall();
    
    ##getting scores from major2's relations
    cur.execute("SELECT score FROM MajorRelations WHERE source_id = "+id2);
    array2 = cur.fetchall();

    ##getting count of number of courses major2 has taken total, if 0, major meanlingless
    cur.execute("SELECT SUM(Count) FROM MajorRelations WHERE target_id = "+id2);
    array3 = cur.fetchall();
    ##so we return 0.
    if(array3[0][0] == 0):
        return 0


    
    
    
    


    diff12 = []
    #major 1 first
    sum12 = 0

    ## How to get similarity between two majors....
    for i in range(len(array1)):
        if(array1[i][0] != 0 or array2[i][0] != 0):
            val = abs(array1[i][0]-array2[i][0])
            diff12.append(val)
            sum12+=val

    ##print sum12
    
    if(len(diff12) == 0):
        return 0
    else:
        sum12/=len(diff12)
    
    return 1-sum12
    ###



    
def majorRelations(major1,major2):
    
    cur.execute("SELECT id FROM Departments WHERE name = '"+major1+"'")
    tup = cur.fetchall();
    id1 = str(tup[0][0])

    
    cur.execute("SELECT id FROM Departments WHERE name = '"+major2+"'")
    tup = cur.fetchall();
    id2 = str(tup[0][0])

    
    cur.execute("SELECT score FROM MajorRelations WHERE source_id = "+id1+" AND target_id = "+id2);
    final = cur.fetchall();
    score = final[0][0]
    #print "(",major1,",",major2,") = ",score
    return score



    

db = MySQLdb.connect(host="localhost",    # your host, usually localhost
                     user="root",         # your username
                     passwd="root",
                     port=8889,# your password
                     unix_socket='/Applications/MAMP/tmp/mysql/mysql.sock',
                     db="suggestr")        # name of the data base

cur = db.cursor()


    
'''
majorRelations("Computer Science","Electrical, Computer, and Systems Engineering")
majorRelations("Computer Science","Computer Science")

betterRelations("Computer Science","Electrical, Computer, and Systems Engineering")
betterRelations("Computer Science","Computer Science")
'''



cur.execute("SELECT name FROM departments")
depts = cur.fetchall();
firstmajor = "Computer Science"
bigArray = []
minimum = 1
maximum = 0
summation = 0
for item in depts:
    rate1 = majorRelations(firstmajor,item[0])
    rate2 = betterRelations(firstmajor,item[0])
    print rate2
    rate2 = math.pow(rate2,.01)
    print rate2
    bigArray.append([firstmajor,item[0],rate1,rate2])

    
    if(rate2 > maximum):
        maximum = rate2

    if(rate2 < minimum and rate2 != 0):
        minimum = rate2

for item in bigArray:
    if(item[3] != 0):
        item[3] = item[3]-minimum
    summation+=item[3]
    
for item in bigArray:
    if(item[3] != 0):
        item[3] /= summation

for item in bigArray:
    firstmajor = item[0]
    secondmajor = item[1]
    rate1 = item[2]
    rate2 = item[3]
    #print "("+firstmajor+" -> ",item[0],") == (",rate1, ") (",rate2,")*"
    print "(%-*s -> %-*s : (%-*s) (%-*s)*" % (20,firstmajor[0:20],20,secondmajor[0:20],5,str(rate1)[0:5],5,str(rate2)[0:5])

    
#    betterRelations("Computer Science",item[0])
#def majorRelationBetter(major1,major2):
    

