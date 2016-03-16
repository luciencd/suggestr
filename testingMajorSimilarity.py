#!/usr/bin/python
import MySQLdb


    
def betterRelations(major1,major2):
    
    cur.execute("SELECT id FROM Departments WHERE name = '"+major1+"'")
    tup = cur.fetchall();
    id1 = str(tup[0][0])

    
    cur.execute("SELECT id FROM Departments WHERE name = '"+major2+"'")
    tup = cur.fetchall();
    id2 = str(tup[0][0])

    cur.execute("SELECT score FROM MajorRelations WHERE source_id = "+id1);
    array1 = cur.fetchall();
    

    cur.execute("SELECT score FROM MajorRelations WHERE source_id = "+id2);
    array2 = cur.fetchall();

    diff12 = []
    #major 1 first
    sum12 = 0
    for i in range(len(array1)):
        #print array1[i][0]
        if(array1[i][0] != 0 or array2[i][0] != 0):
            print array1[i][0],array2[i][0]
            diff12.append(abs(array1[i][0]-array2[i][0]))
            sum12+=abs(array1[i][0]-array2[i][0])

    if(len(diff12) == 0):
        return 0
    else:
        sum12/=len(diff12)
    print "(",major1,",",major2,") = ",1-sum12
    return 1-sum12



    
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
    print "(",major1,",",major2,") = ",score
    return score



    

db = MySQLdb.connect(host="localhost",    # your host, usually localhost
                     user="root",         # your username
                     passwd="root",
                     port=8889,# your password
                     unix_socket='/Applications/MAMP/tmp/mysql/mysql.sock',
                     db="suggestr")        # name of the data base

cur = db.cursor()


    
majorRelations("Computer Science","Electrical, Computer, and Systems Engineering")
majorRelations("Computer Science","Computer Science")

betterRelations("Computer Science","Electrical, Computer, and Systems Engineering")
betterRelations("Computer Science","Computer Science")
    
#def majorRelationBetter(major1,major2):
    

