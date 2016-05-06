
/*eventually should change from completely rebuilding these stats to
  updating only those that have changed.
  
  It returns separate prediction statistics for each of
  three numerical sliders, another function should then get a weighted average (or other aggragate?)
  of the three based on how a user adjusts sliders for how important those aspects
  are to him/her
  
  #<variable_name> is replaced by an appropriate value in php.  Here they are #user_id, #easiness, #relevance, and #quality
*/

SELECT @user_id;
SET @user_id = #user_id;
/*
For testing, replace @@SPID with a known session_id
good samples include 4996822, 4996862, 4996872, 4996982 for easiness only  */

DROP TABLE output;
CREATE TABLE output(user INT, course_id INT, course_name TEXT, easiness FLOAT, relevance FLOAT, quality FLOAT);

DROP TABLE easy;
DROP TABLE pearson1;
DROP TABLE norm_factors1;

DROP TABLE rele;
DROP TABLE pearson2;
DROP TABLE norm_factors2;

DROP TABLE qual;
DROP TABLE pearson3;
DROP TABLE norm_factors3;

CREATE TABLE easy(user INT, course_id INT, course_name TEXT, easiness FLOAT);
CREATE TABLE pearson1(
	user1 INTEGER, user2 INTEGER, similarity FLOAT, n INTEGER, slider_id INTEGER, user1_avg FLOAT, user2_avg FLOAT, user2_rating FLOAT
);
CREATE TABLE norm_factors1(user1 INTEGER, k FLOAT DEFAULT 0);

CREATE TABLE rele(user INT, course_id INT, course_name TEXT, relevance FLOAT);
CREATE TABLE pearson2(
	user1 INTEGER, user2 INTEGER, similarity FLOAT, n INTEGER, slider_id INTEGER, user1_avg FLOAT, user2_avg FLOAT, user2_rating FLOAT
);
CREATE TABLE norm_factors2(user1 INTEGER, k FLOAT DEFAULT 0);

CREATE TABLE qual(user INT, course_id INT, course_name TEXT, quality FLOAT);
CREATE TABLE pearson3(
	user1 INTEGER, user2 INTEGER, similarity FLOAT, n INTEGER, slider_id INTEGER, user1_avg FLOAT, user2_avg FLOAT, user2_rating FLOAT
);
CREATE TABLE norm_factors3(user1 INTEGER, k FLOAT DEFAULT 0);


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
            n1.slider_id = 1 AND n2.slider_id = 1 AND /*change from 1 to any other slider_id(s) to rank by that.*/
            n1.slider_id = n2.slider_id AND
            n1.session_id = @user_id
	GROUP BY
		n1.session_id, n2.session_id) AS step1 
	ORDER BY 
		similarity DESC,
        n DESC;


/*normalization factors*/

INSERT INTO norm_factors1(user1, k)
SELECT 
	user1, (1/ SUM(ABS(similarity))) AS k
FROM pearson1 
/*WHERE similarity > 0 /*change this to match any changed cutoff for similarity*/
GROUP BY user1;

/*calculate course rating predictions weighted for rater's average ratings*/
INSERT INTO easy (user, course_id, course_name, easiness)
SELECT
	user, course_id, course_name, predicted_rating as easinesss
FROM
	(SELECT 
		pearson.user1 AS user,
        courses.name AS course_name,
        courses.id AS course_id,
        pearson.user1_avg + norm_factors.k*SUM(pearson.similarity * (slideraction.vote - pearson.user2_avg)) AS predicted_rating
	FROM pearson1 as pearson, slideraction, norm_factors1 as norm_factors, courses
	WHERE
		pearson.user2 = slideraction.session_id AND
        pearson.user1 = norm_factors.user1 AND
        pearson.slider_id = slideraction.slider_id AND
        courses.id = slideraction.course_id /*AND
        pearson.similarity > 0 /*other cutoffs for similarity may improve performance*/
	GROUP BY
		pearson.user1, slideraction.course_id
    ) AS step1
ORDER BY user, predicted_rating DESC;

      
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
            n1.session_id = @user_id
	GROUP BY
		n1.session_id, n2.session_id) AS step1 
	ORDER BY 
		similarity DESC,
        n DESC;


/*normalization factors*/

INSERT INTO norm_factors2(user1, k)
SELECT 
	user1, (1/ SUM(ABS(similarity))) AS k
FROM pearson2 
/*WHERE similarity > 0 /*change this to match any changed cutoff for similarity*/
GROUP BY user1;

INSERT INTO rele (user, course_id, course_name, relevance)
SELECT
	user, course_id, course_name, predicted_rating as relevance
FROM
	(SELECT 
		pearson.user1 AS user,
        courses.name AS course_name,
        courses.id AS course_id,
        pearson.user1_avg + norm_factors.k*SUM(pearson.similarity * (slideraction.vote - pearson.user2_avg)) AS predicted_rating
	FROM pearson2 as pearson, slideraction, norm_factors2 as norm_factors, courses
	WHERE
		pearson.user2 = slideraction.session_id AND
        pearson.user1 = norm_factors.user1 AND
        pearson.slider_id = slideraction.slider_id AND
        courses.id = slideraction.course_id /*AND
        pearson.similarity > 0 /*other cutoffs for similarity may improve performance*/
	GROUP BY
		pearson.user1, slideraction.course_id
    ) AS step1
ORDER BY user, predicted_rating DESC;



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
			(NOT n1.session_id = n2.session_id) AND 
            n1.slider_id = 3 AND n2.slider_id = 3 AND /*change from 1 to any other slider_id(s) to rank by that.*/
            n1.slider_id = n2.slider_id AND
            n1.session_id = @user_id
	GROUP BY
		n1.session_id, n2.session_id) AS step1 
	ORDER BY 
		similarity DESC,
        n DESC;


/*normalization factors*/

INSERT INTO norm_factors3(user1, k)
SELECT 
	user1, (1/ SUM(ABS(similarity))) AS k
FROM pearson3 
/*WHERE similarity > 0 /*change this to match any changed cutoff for similarity*/
GROUP BY user1;

INSERT INTO qual (user, course_id, course_name, quality)
SELECT
	user, course_id, course_name, predicted_rating as quality
FROM
	(SELECT 
		pearson.user1 AS user,
        courses.name AS course_name,
        courses.id AS course_id,
        pearson.user1_avg + norm_factors.k*SUM(pearson.similarity * (slideraction.vote - pearson.user2_avg)) AS predicted_rating
	FROM pearson3 as pearson, slideraction, norm_factors3 as norm_factors, courses
	WHERE
		pearson.user2 = slideraction.session_id AND
        pearson.user1 = norm_factors.user1 AND
        pearson.slider_id = slideraction.slider_id AND
        courses.id = slideraction.course_id /*AND
        pearson.similarity > 0 /*other cutoffs for similarity may improve performance*/
	GROUP BY
		pearson.user1, slideraction.course_id
    ) AS step1
ORDER BY user, quality DESC;

SELECT session_id FROM slideraction GROUP BY session_id;

SELECT * FROM easy;
SELECT * FROM rele;
SELECT * FROM qual;

INSERT INTO output(user, course_id, course_name, easiness, relevance, quality)
SELECT @user_id as user, courses.id as course_id, courses.name as course_name, easy.easiness, rele.relevance, qual.quality
FROM (courses
	LEFT JOIN
		easy ON easy.course_id = courses.id
	LEFT JOIN
		rele ON rele.course_id = courses.id
	LEFT JOIN
		qual ON qual.course_id = courses.id)
WHERE (easy.course_id = courses.id OR rele.course_id = courses.id OR qual.course_id = courses.id)
;

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
		action.session_id = @user_id AND
        action.course_id = courses.id AND
        action.session_id = slideraction.session_id AND
        slideraction.course_id = action.course_id AND
        slideraction.slider_id <= 3
        
	GROUP BY
		courses.name, slideraction.slider_id) AS step
;


SELECT * FROM output
ORDER BY IFNULL(easiness, 0)*#easiness + IFNULL(relevance, 0)*#relevance + IFNULL(quality, 0)*#quality DESC; /*the decimals here are stand ins for slider values*/
