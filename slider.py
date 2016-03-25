slider_id = 4

for i in range(10):
    print slider_id
    slider_id = ((slider_id-4) + 1)%3 + 4;

slider_id = 4
print ""
for i in range(10):
    print slider_id
    slider_id = ((slider_id-4) - 1)%3 + 4;
