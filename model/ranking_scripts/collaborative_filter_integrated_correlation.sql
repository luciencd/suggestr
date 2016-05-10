
/*eventually should change from completely rebuilding these stats to
  updating only those that have changed.
  
  It returns separate prediction statistics for each of
  three numerical sliders, another function should then get a weighted average (or other aggragate?)
  of the three based on how a user adjusts sliders for how important those aspects
  are to him/her
  
  This version generates similarity bewteen users along all axes at once (and thus there is only one pearson table)
  The similarity is weighted based on the sliders.
  
  #<variable_name> is replaced by an appropriate value in php.  Here they are #user_id, #easiness, #relevance, and #quality
*/

/*SELECT @user_id;
SET @user_id = 4996822;*/
/*
For testing, replace @@SPID with a known session_id
good samples include 4996822, 4996862, 4996872, 4996982 for easiness only  */

/*get the slider weights*/
SELECT @slider_easiness;
SET @slider_easiness =
	IFNULL((SELECT vote FROM weights
    WHERE slider_id = 1 AND session_id = user_id), 1);
SELECT @slider_relevance;
SET @slider_relevance =
	IFNULL((SELECT vote FROM weights
    WHERE slider_id = 2 AND session_id = user_id), 1);
SELECT @slider_quality;
SET @slider_quality =
	IFNULL((SELECT vote FROM weights
    WHERE slider_id = 3 AND session_id = user_id), 1);

DROP TABLE output;
CREATE TABLE output(user INT, course_id INT, course_name TEXT, description TEXT, department_id INT, department_code TEXT, department_name TEXT, level INT, easiness FLOAT, relevance FLOAT, quality FLOAT);

DROP TABLE easy;
DROP TABLE pearson1;

DROP TABLE rele;
DROP TABLE pearson2;

DROP TABLE qual;
DROP TABLE pearson3;

DROP TABLE pearson;
DROP TABLE norm_factors;

CREATE TABLE easy(user INT, course_id INT, course_name TEXT, easiness FLOAT);
CREATE TABLE pearson1(
	user1 INTEGER, user2 INTEGER, similarity FLOAT, n INTEGER, slider_id INTEGER, user1_avg FLOAT, user2_avg FLOAT, user2_rating FLOAT
);

CREATE TABLE rele(user INT, course_id INT, course_name TEXT, relevance FLOAT);
CREATE TABLE pearson2(
	user1 INTEGER, user2 INTEGER, similarity FLOAT, n INTEGER, slider_id INTEGER, user1_avg FLOAT, user2_avg FLOAT, user2_rating FLOAT
);

CREATE TABLE qual(user INT, course_id INT, course_name TEXT, quality FLOAT);
CREATE TABLE pearson3(
	user1 INTEGER, user2 INTEGER, similarity FLOAT, n INTEGER, slider_id INTEGER, user1_avg FLOAT, user2_avg FLOAT, user2_rating FLOAT
);

CREATE TABLE pearson(
	user1 INTEGER, user2 INTEGER, similarity FLOAT, n INTEGER
);
CREATE TABLE norm_factors(user1 INTEGER, k FLOAT DEFAULT 0);


/*Pearson Correlation coefficient plus some simple statistics*/
INSERT INTO pearson1 (user1, user2, similarity, n, slider_id, user1_avg, user2_avg, user2_rating)
SELECT  
	user1, user2,
	IFNULL((psum - (sum1 * sum2 / n)) / sqrt((sum1sq - pow(sum1, 2.0) / n) * (sum2sq - pow(sum2, 2.0) / n)), 0) AS similarity,
	n, slider_id, user1_avg, user2_avg, user2_rating
FROM
	(SELECT 
		n1.session_id AS user1,
		n2.session_id AS user2,
		SUM(n1.vote) AS sum1,
		SUM(n2.vote) AS sum2,
		SUM(n1.vote * n1.vote) AS sum1sq,
		SUM(n2.vote * n2.vote) AS sum2sq,
		SUM(n1.vote * n2.vote) AS psum,
		COUNT(*) AS n,
		n1.slider_id AS slider_id,
		AVG(n1.vote) AS user1_avg,
		AVG(n2.vote) AS user2_avg,
		n2.vote AS user2_rating
	FROM
		slideraction AS n1
	LEFT JOIN
		slideraction AS n2
	ON
		n1.course_id = n2.course_id
        WHERE   
			(NOT n1.session_id = n2.session_id) AND 
            n1.slider_id = 1 AND n2.slider_id = 1 AND /*similarity is based on all 3 qualities*/
            n1.slider_id = n2.slider_id AND
            n1.session_id = user_id
	GROUP BY
		n1.session_id, n2.session_id) AS step1 
	ORDER BY 
		similarity DESC,
        n DESC;


      
/*Pearson Correlation coefficient plus some simple statistics*/
INSERT INTO pearson2 (user1, user2, similarity, n, slider_id, user1_avg, user2_avg, user2_rating)
SELECT  
	user1, user2,
	IFNULL((psum - (sum1 * sum2 / n)) / sqrt((sum1sq - pow(sum1, 2.0) / n) * (sum2sq - pow(sum2, 2.0) / n)), 0) AS similarity,
	n, slider_id, user1_avg, user2_avg, user2_rating
FROM
	(SELECT 
		n1.session_id AS user1,
		n2.session_id AS user2,
		SUM(n1.vote) AS sum1,
		SUM(n2.vote) AS sum2,
		SUM(n1.vote * n1.vote) AS sum1sq,
		SUM(n2.vote * n2.vote) AS sum2sq,
		SUM(n1.vote * n2.vote) AS psum,
		COUNT(*) AS n,
		n1.slider_id AS slider_id,
		AVG(n1.vote) AS user1_avg,
		AVG(n2.vote) AS user2_avg,
		n2.vote AS user2_rating
	FROM
		slideraction AS n1
	LEFT JOIN
		slideraction AS n2
	ON
		n1.course_id = n2.course_id
        WHERE   
			(NOT n1.session_id = n2.session_id) AND 
            n1.slider_id = 2 AND n2.slider_id = 2 AND /*change from 1 to any other slider_id(s) to rank by that.*/
            n1.slider_id = n2.slider_id AND
            n1.session_id = user_id
	GROUP BY
		n1.session_id, n2.session_id) AS step1 
	ORDER BY 
		similarity DESC,
        n DESC;


/*Pearson Correlation coefficient plus some simple statistics*/
INSERT INTO pearson3 (user1, user2, similarity, n, slider_id, user1_avg, user2_avg, user2_rating)
SELECT  
	user1, user2,
	IFNULL((psum - (sum1 * sum2 / n)) / sqrt((sum1sq - pow(sum1, 2.0) / n) * (sum2sq - pow(sum2, 2.0) / n)), 0) AS similarity,
	n, slider_id, user1_avg, user2_avg, user2_rating
FROM
	(SELECT 
		n1.session_id AS user1,
		n2.session_id AS user2,
		SUM(n1.vote) AS sum1,
		SUM(n2.vote) AS sum2,
		SUM(n1.vote * n1.vote) AS sum1sq,
		SUM(n2.vote * n2.vote) AS sum2sq,
		SUM(n1.vote * n2.vote) AS psum,
		COUNT(*) AS n,
		n1.slider_id AS slider_id,
		AVG(n1.vote) AS user1_avg,
		AVG(n2.vote) AS user2_avg,
		n2.vote AS user2_rating
	FROM
		slideraction AS n1
	LEFT JOIN
		slideraction AS n2
	ON
		n1.course_id = n2.course_id
        WHERE   
			(NOT n1.session_id = n2.session_id) AND /*Don't observe your own ratings*/
            n1.slider_id = 3 AND n2.slider_id = 3 AND /*change from 1 to any other slider_id(s) to rank by that.*/
            n1.slider_id = n2.slider_id AND
            n1.session_id = user_id /*make sure you are looking at your own ratings on left side
            As in, comparing your rating of a given class for a given slider with someone else's ratings.*/
	GROUP BY
		n1.session_id, n2.session_id) AS step1 
	ORDER BY 
		similarity DESC,
        n DESC;

/*aggragate correlations between users across all the qualities, weight by sliders*/
INSERT INTO pearson (user1, user2, similarity, n)
SELECT
	user_id as user1,
    sessions.id as user2,
    /*slider values used here*/
	(IFNULL(pearson1.similarity, 0)*@slider_easiness +
    IFNULL(pearson2.similarity, 0)*@slider_relevance +
    IFNULL(pearson3.similarity, 0)*@slider_quality)/
    (@slider_easiness + @slider_relevance + @slider_quality) AS similarity,
	
    /*
	IFNULL(pearson1.similarity, 0)*.6 +
    IFNULL(pearson2.similarity, 0)*.3 +
    IFNULL(pearson3.similarity, 0)*.1 AS similarity,
    */
    IFNULL(pearson1.n, 0) +
    IFNULL(pearson2.n, 0) +
    IFNULL(pearson3.n, 0) AS n
    
FROM (sessions
	LEFT JOIN pearson1 ON pearson1.user2 = sessions.id
    LEFT JOIN pearson2 ON pearson2.user2 = sessions.id
    LEFT JOIN pearson3 ON pearson3.user2 = sessions.id)
WHERE
	pearson1.user2 = sessions.id OR pearson2.user2 = sessions.id OR pearson3.user2 = sessions.id
;
/*generate average values*/
DROP TABLE averages;
CREATE TABLE averages(user INT, easy FLOAT, rele FLOAT, qual FLOAT);

INSERT INTO averages(user, easy, rele, qual)
SELECT
	pearson.user2 as user,
    easy,
    rele,
    qual
FROM 
	(SELECT session_id, AVG(slideraction.vote) as easy
		FROM slideraction
        WHERE slider_id = 1
        GROUP BY session_id) AS e,
	(SELECT session_id, AVG(slideraction.vote) as rele
		FROM slideraction
        WHERE slider_id = 2
        GROUP BY session_id) AS r,
	(SELECT session_id, AVG(slideraction.vote) as qual
		FROM slideraction
        WHERE slider_id = 3
        GROUP BY session_id) AS q,
	pearson
WHERE
	e.session_id = pearson.user2 AND
    r.session_id = pearson.user2 AND
    q.session_id = pearson.user2
;

/*normalization factor(s), only need to apply to the aggregated pearson scores*/

INSERT INTO norm_factors(user1, k)
SELECT 
	user1, (1/ SUM(ABS(similarity))) AS k
FROM pearson 
/*WHERE similarity > 0 /*change this to match any changed cutoff for similarity*/
GROUP BY user1;



INSERT INTO easy (user, course_id, course_name, easiness)
SELECT
	user, course_id, course_name, predicted_rating as easinesss
FROM
	(SELECT 
		pearson_agg.user1 AS user,
        courses.name AS course_name,
        courses.id AS course_id,
        IFNULL(s2.vote,
        pearson_axis.user1_avg + norm_factors.k*SUM(pearson_agg.similarity * (slideraction.vote - IFNULL(averages.easy, .5)))) AS predicted_rating
	FROM pearson1 as pearson_axis, pearson as pearson_agg, slideraction as slideraction, slideraction as s2, norm_factors as norm_factors, courses, averages
	WHERE
		pearson_axis.user2 = slideraction.session_id AND
        pearson_axis.user1 = norm_factors.user1 AND
        slideraction.slider_id = 1 AND
        courses.id = slideraction.course_id AND
        pearson_agg.user1 = norm_factors.user1 AND
        pearson_agg.user2 = slideraction.session_id AND
        averages.user = pearson_agg.user2 AND
        s2.session_id = user_id AND
        s2.slider_id = slideraction.slider_id AND
        s2.course_id = courses.id/*AND
        pearson.similarity > 0 /*other cutoffs for similarity may improve performance*/
	GROUP BY
		pearson_agg.user1, slideraction.course_id
    ) AS step1
ORDER BY user, predicted_rating DESC;

INSERT INTO rele (user, course_id, course_name, relevance)
SELECT
	user, course_id, course_name, predicted_rating as relevance
FROM
	(SELECT 
		pearson_agg.user1 AS user,
        courses.name AS course_name,
        courses.id AS course_id,
        IFNULL(s2.vote,
        pearson_axis.user1_avg + norm_factors.k*SUM(pearson_agg.similarity * (slideraction.vote - IFNULL(averages.rele, .5)))) AS predicted_rating
	FROM pearson2 as pearson_axis, pearson as pearson_agg, slideraction as slideraction, slideractoin as s2, norm_factors as norm_factors, courses, averages
	WHERE
		pearson_axis.user2 = slideraction.session_id AND
        pearson_axis.user1 = norm_factors.user1 AND
        slideraction.slider_id = 2 AND
        courses.id = slideraction.course_id AND
        pearson_agg.user1 = norm_factors.user1 AND
        pearson_agg.user2 = slideraction.session_id AND
        averages.user = pearson_agg.user2 AND
        s2.session_id = user_id AND
        s2.slider_id = slideraction.slider_id AND
        s2.course_id = courses.id/*AND
        pearson.similarity > 0 /*other cutoffs for similarity may improve performance*/
	GROUP BY
		pearson_agg.user1, slideraction.course_id
    ) AS step1
ORDER BY user, predicted_rating DESC;

INSERT INTO qual (user, course_id, course_name, quality)
SELECT
	user, course_id, course_name, predicted_rating as quality
FROM
	(SELECT 
		pearson_agg.user1 AS user,
        courses.name AS course_name,
        courses.id AS course_id,
        IFNULL(s2.vote,
        pearson_axis.user1_avg + norm_factors.k*SUM(pearson_agg.similarity * (slideraction.vote - IFNULL(averages.qual, .5)))) AS predicted_rating
	FROM pearson3 as pearson_axis, pearson as pearson_agg, slideraction as slideraction, slideraction as s2, norm_factors as norm_factors, courses, averages
	WHERE
		pearson_axis.user2 = slideraction.session_id AND
        pearson_axis.user1 = norm_factors.user1 AND
        slideraction.slider_id = 3 AND
        courses.id = slideraction.course_id AND
        pearson_agg.user1 = norm_factors.user1 AND
        pearson_agg.user2 = slideraction.session_id AND
        averages.user = pearson_agg.user2 AND
        s2.session_id = user_id AND
        s2.slider_id = slideraction.slider_id AND
        s2.course_id = courses.id
        /*AND
        pearson.similarity > 0 /*other cutoffs for similarity may improve performance*/
	GROUP BY
		pearson_agg.user1, slideraction.course_id
    ) AS step1
ORDER BY user, predicted_rating DESC;


SELECT session_id FROM slideraction GROUP BY session_id;

SELECT * FROM easy;
SELECT * FROM rele;
SELECT * FROM qual;

INSERT INTO output(user, course_id, course_name,description,department_id, department_code,department_name,level, easiness, relevance, quality)
SELECT user_id as user, courses.id as course_id, courses.name as course_name, courses.description as description, courses.department_id as department_id, departments.code as department_code,departments.name as department_name,courses.number as level, easy.easiness, rele.relevance, qual.quality
FROM (courses
	LEFT JOIN
		easy ON easy.course_id = courses.id
	LEFT JOIN
		rele ON rele.course_id = courses.id
	LEFT JOIN
		qual ON qual.course_id = courses.id
	LEFT JOIN 
		departments ON departments.id = courses.department_id)


WHERE (easy.course_id = courses.id OR rele.course_id = courses.id OR qual.course_id = courses.id)
;
/*
SELECT department_code as 
FROM (departments
	LEFT JOIN
		department_code ON departments.id = courses.id)
)*/

SELECT name, id, rating, slider_id
FROM (
	SELECT
		action.course_id as id,
        courses.name as name,
        slideraction.vote as rating,
        slideraction.slider_id as slider_id
	FROM action, courses, slideraction
	WHERE 
		action.choice = 1 AND
		action.session_id = user_id AND
        action.course_id = courses.id AND
        action.session_id = slideraction.session_id AND
        slideraction.course_id = action.course_id AND
        slideraction.slider_id <= 3
        
	GROUP BY
		courses.name, slideraction.slider_id) AS step
;


/*
SELECT * FROM output
ORDER BY IFNULL(easiness, 0) DESC;*/

/*This is where the array of weights comes in.. not sure if similiarity score should be calculated at first with these in mind but they arent here*/
#easiness + IFNULL(relevance, 0)*#relevance + IFNULL(quality, 0)*#quality DESC; /*the decimals here are stand ins for slider values*/


/* after this, should add all relevant columns to this for json object returns
Stuff like department id department code, shit we shouldn't need to look up in php*/
