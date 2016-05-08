SELECT * FROM output
ORDER BY (IFNULL(easiness, 0)*1+IFNULL(relevance, 0)*10+IFNULL(quality, 0)*1) DESC;