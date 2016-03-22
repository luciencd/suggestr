SUGGESTR - suggestr.co, http://suggestr.mybluemix.net/
Suggestr is a stress-free, web-based course suggester app centered around simplicity.

Contributors: Lucien Christie-Dervaux, Ben Espey, & Leopold Joy
Questions & Comments: chrisl2@rpi.edu, espeyb@rpi.edu, or joyl@rpi.edu

TECHNOLOGIES USED:

PHP, SQL, Bootstrap, HTML, CSS, Javascript+jQuery, Mustache.

MACHINE LEARNING:

Outputs:
Our end goal when it comes to a student using the app is to give them a map which maps a course to a suggestion score, which the front end will display of course. 

Inputs: 
When a student writes up a list of courses he's taken in the past, the model compiles a list of similar "course histories" - which can be based on a number of heuristics(top n%, top n, similarity threshold).

Operators:
We use "Jaccard Index" to determine similarity between any two given "course histories". Essentially, given two sets A and B: JI = |A intersection B|/|A Union B|

Process:
We go through the whole database of Actions and create an array of entries like this: (Jaccard Index, "course history") => [ 0.90, ("Physics I","Calculus I","CS1","General Psychology") ]

Then, we iterate through the list of these entries, 

The suggestion score for a given class is determined by this heuristic.
map [ course ] += JI(parent)*(1/(log(frequency(course))+c))

Each time a course appears in a "course history", we take the Jaccard Index of it, and multiply it by the inverse of the log of how frequently it has been chosen by all students.

JI(Parent) : This favors courses which appear in very similar "course histories".

1/(log(frequency(course))+c) : This favors courses that don't appear as frequently in general, as they are probably the courses that most people won't know about.

If we did not assign a lower priority to very common classes, we would quickly end up with a popularity contest in our suggested courses list. By the same token, if we assigned a 1/n multiplier to the score of courses, it would be the same result but in the other direction. Taking the log of the frequency is a good compromise. The +C is there to balance the scale out a bit more.

Then, it removes all the courses that have already been taken by the student, and we are currently working on eliminating prerequisites from the suggestions as well.

By the end of this, we end up with a map of courses to their suggestion scores:

So for example: for student 772, map = (course["Calculus II"] => 3.4, course["Economics 101"] => 2.3, course["Intro to Bio"] => 1.4 ) and so on.



SPECIAL THANKS TO:

Lou Yufan & Jeff Hui for help and advice. YACS for the course data.

HELP & FEEDBACK:

Go to GitHub issues page to list your bugs and concerns.

Tools we use in app:

Glench/fuzzyset.js
Copyright (c) 2016, Glench





Main Copyright notice:
Copyright (c) 2016, Lucien Christie-Dervaux
All rights reserved.
