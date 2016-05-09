SELECT * FROM output
ORDER BY (IFNULL(easiness, 0)*e_slider+IFNULL(relevance, 0)*r_slider+IFNULL(quality, 0)*q_slider) DESC;