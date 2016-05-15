#!/usr/bin/python
import MySQLdb

import math
from mpl_toolkits.mplot3d import Axes3D
import numpy as np
import matplotlib.pyplot as plt

from sklearn.cluster import KMeans
from sklearn import datasets
from pylab import plot,show
from numpy import vstack,array
from numpy.random import rand
from scipy.cluster.vq import kmeans,vq
import random

db = MySQLdb.connect(host="us-cdbr-iron-east-03.cleardb.net",    # your host, usually localhost
                     user="b5777848a3bae2",         # your username
                     passwd="89213f1b3bdd0ad",
                     #port=8889,# your password
                     #unix_socket='/Applications/MAMP/tmp/mysql/mysql.sock',
                     db="ad_771f5ec54b7a0d1")        # name of the data base

cur = db.cursor()

cur.execute("SELECT * FROM output")

array = cur.fetchall();

session_id = array[0][0]

##Finding list of names of courses you took already.
command = "SELECT * FROM action WHERE (session_id = '"+str(session_id)+"' AND choice='1');"
print command
cur.execute(command)

tookCourses = cur.fetchall();
 
print tookCourses
tookmap = lambda item: item[3]
#print map(tookmap,tookCourses)
tookmap = map(tookmap,tookCourses)
print tookmap

fig = plt.figure()

ax = fig.gca()

#ax.set_zlabel('quality')

'''

x = np.linspace(0, 1, 100)
y = np.sin(x * 2 * np.pi) / 2 + 0.5
z = np.linspace(0,1,100)
ax.plot(x, y, z, zdir='z', label='zs=0, zdir=z')
'''

colors = ('r', 'g', 'b', 'k')
array2 = []
minArray = []
for item in array:
    x = item[8]
    y = item[9]
    z = item[10]
    if(x == None):
        x = 1
    if(y == None):
        y = 1
    if(z == None):
        z = 1
    x += random.uniform(-.02,.02)
    y += random.uniform(-.02,.02)
    z += random.uniform(-.02,.02)
    minArray.append((item[1],item[2],x,y,z))
    print minArray[-1]
##(x,y,z) == (easiness, quality, relevance)
    
##Choosing the 2d axes.
##0th column is ylabel
##1st column is xlabel.
## idk why it just is
ax.set_xlabel('easiness')
ax.set_ylabel('relevance')

#getting a tuple of the item vs item we want.
#first item is on y-axis,
#second item is on x-axis

#item[2] = easiness, item[3] = relevance, item[4] = quality.

g = lambda item: (item[2],item[3])
subsetData = map(g,minArray)
## (.7,.98)

g = lambda item: (item[0],item[1],item[2],item[3])
subsetDisplay = map(g,minArray)
## (10101, Data Structures,.4,.7,.98)


data = np.array(subsetData)

centroids,_ = kmeans(data,8)
idx,_ = vq(data,centroids)

for i in range(len(centroids)):
    ax.scatter(centroids[i][0],centroids[i][1], c='white',s=40)
    
    
colors = ["blue","red","green","orange","purple","gray","indigo","teal"]



##Printing the classes already taken.

###
###
##DISPLAY ACTUAL RATING VALUES IF THE CLASS HAS ALREADY BEEN RATED.
###
###

for i in range(0,len(subsetDisplay)):
    
    x = subsetDisplay[i][2]
    y = subsetDisplay[i][3]

    ##if this is from the courses we've already taken.
    #print array[i][1],tookmap
    an_x = x
    an_y = y
    centerx = centroids[idx[i]][0]
    centery = centroids[idx[i]][1]
    #print centerx,centery
    rel_x = x-centerx+0.00001
    rel_y = y-centery+0.00001

    
    #get line from centroid to point, then go further out by 1.1x
    
    ##
    angle = math.atan(rel_y/rel_x)
    
    if(rel_x >0 and rel_y >0):#1st quarter
        #print angle
        angle = angle
    if(rel_x <0 and rel_y >0):#2nd quarter
        #print angle
        angle = 1.57*1+(1.57+angle)
    if(rel_x <0 and rel_y <0):#3rd quarter
        #print angle
        angle = 1.57*2+angle
    if(rel_x >0 and rel_y <0):#4th quarter
        #print angle
        angle = 1.57*3+(1.57+angle)


    radius = math.sqrt(rel_x**2+rel_y**2)
       

    an_x = centerx + (radius+.1) * math.cos(angle)
    an_y = centery + (radius+.1) * math.sin(angle)
    annotation = (an_x,an_y)

    an_x = centerx + (radius+.12) * math.cos(angle)
    an_y = centery + (radius+.12) * math.sin(angle)
    notation = (an_x,an_y)
    #print subsetDisplay[i][1],angle
 

    #end_x = 
    #print x,y
    if(array[i][1] in tookmap):
        #print 'past: ',array[i][2]
#        ax.text(x, y,array[i][2], size=6,zorder=1,ha='center',color='black')
        ax.scatter(x,y, c=colors[idx[i]])#could be black

        
        note =str((angle)*(360/(2*(3.14))))[0:3]+","+str(radius)[0:3]
        note = subsetDisplay[i][1]
        note = ""
        ax.annotate(note, xy=(x, y), xytext=annotation,
            arrowprops=dict(arrowstyle="->",color="black"),size=8)
        note = subsetDisplay[i][1]
        ax.text(notation[0],notation[1],note, size=14,ha='center',color="black")
    else:
        #print 'predict: ',array[i][2]
        c =colors[idx[i]]
#        
        ax.scatter(x,y, c=colors[idx[i]])
        note =str((angle)*(360/(2*(3.14))))[0:3]+","+str(radius)[0:3]
        note = subsetDisplay[i][1]
        note = ""
        
        ax.annotate(note, xy=(x, y), xytext=annotation,color=colors[idx[i]],
            arrowprops=dict(arrowstyle="->",color=colors[idx[i]]),size=8)
        note = subsetDisplay[i][1]
        ax.text(notation[0],notation[1],note,ha='center', size=14,color=colors[idx[i]])

    
#print centroids
#print idx
'''
for item in kmeansarray:
    ax.scatter(item.x, item.y, item.z, zdir='y', c=item.group)
'''
#ax.legend()
#ax.set_xlim2d(0, 1)
#ax.set_ylim2d(0, 1)
#ax.set_zlim2d(0, 1)

plt.show()
