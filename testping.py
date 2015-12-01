
import json

import os

print os.path.dirname(os.path.realpath(__file__))
import site
print site.getsitepackages()
try:
    
    import MySQLdb
    
    #import _mysql
except ImportError:
    result = {'import':'failed'}
    print json.dumps(result)

result = {'FUCK':'this'}
print json.dumps(result)
