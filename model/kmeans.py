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
command = "SELECT * FROM action WHERE (session_id = '"+str(session_id)+"' AND choice='1');"
print command
cur.execute(command)
tookCourses = cur.fetchall();

fig = plt.figure()

ax = fig.gca(projection='3d')
ax.set_xlabel('easiness')
ax.set_ylabel('relevance')
ax.set_zlabel('quality')

'''

x = np.linspace(0, 1, 100)
y = np.sin(x * 2 * np.pi) / 2 + 0.5
z = np.linspace(0,1,100)
ax.plot(x, y, z, zdir='z', label='zs=0, zdir=z')
'''

colors = ('r', 'g', 'b', 'k')
array2 = []
for item in array:
    x = item[8]
    y = item[9]
    z = item[10]
    if(x == None):
        x = 0
        continue;
 
    if(y == None):
        y = 0
        continue;
       
    if(z == None):
        z = 0
        continue;


    array2.append((x,y,z))
    #print x,y,z
    
g = lambda item: (item[0],item[1],item[2])
print map(g,array2)

data = np.array(array2)
centroids,_ = kmeans(data,3)
idx,_ = vq(data,centroids)
colors = ["blue","red","green"]
for i in range(0,len(data)):
    print data[i]
    ax.scatter(data[i][0], data[i][1], data[i][2], zdir='y', c=colors[idx[i]],s=20)
    ax.text(data[i][0], data[i][1], data[i][2], array[i][2],  color=colors[idx[i]],size=6,zorder=1,ha='center')

for i in range(len(tookCourses)):
    for k in range(len(array)):
        if(tookCourses[i][3] == array[k][1]):
            x = array[k][8]
            y = array[k][9]
            z = array[k][10]
            if(x == None):
                x = 0
                continue;
 
            if(y == None):
                y = 0
                continue;
       
            if(z == None):
                z = 0
                continue;
            print x,y,z
#            ax.annotate(array[k][2][0:5], x,y,z, 
            ax.text(x, y, z, array[k][2], size=6,zorder=1,ha='center',color='black')
            ax.scatter(x,y,z, zdir='y', c="black")
            

    
print centroids
print idx
'''
for item in kmeansarray:
    ax.scatter(item.x, item.y, item.z, zdir='y', c=item.group)
'''
ax.legend()
ax.set_xlim3d(0, 1)
ax.set_ylim3d(0, 1)
ax.set_zlim3d(0, 1)

plt.show()
