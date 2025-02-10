CREATE TABLE itb_balls_list (
    id INT NOT NULL AUTO_INCREMENT,
    sort INT NOT NULL,
    active VARCHAR(1) DEFAULT NULL,
    name VARCHAR(50) DEFAULT NULL,
    add_bonus VARCHAR(50) DEFAULT NULL,
    type VARCHAR(50) DEFAULT NULL,
    user INT NOT NULL,
    conditions TEXT DEFAULT NULL,
    conditions_price TEXT DEFAULT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE itb_bonus_order (
    id INT NOT NULL AUTO_INCREMENT,
    user INT NOT NULL,
    order_id INT NOT NULL,
    minus_price FLOAT NOT NULL,
    bonus_price FLOAT NOT NULL,
    after_price_bonus FLOAT NOT NULL,
    before_price_bonus FLOAT NOT NULL,
    type VARCHAR(50) DEFAULT NULL,
    operation_type VARCHAR(50) DEFAULT NULL,
    PRIMARY KEY (id)
)

