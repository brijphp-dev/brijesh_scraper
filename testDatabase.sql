CREATE TABLE `brij_test`.`scrapurl` 
(
    `id` INT NOT NULL AUTO_INCREMENT ,
    `shorturl` VARCHAR(25) NOT NULL ,
    `url` TEXT NOT NULL , PRIMARY KEY (`id`)
) ENGINE = MyISAM;

INSERT INTO `scrapurl` 
    (`id`, `shorturl`, `url`)
    VALUES 
        ('1', 'oponeo', 'www.oponeo.pl'),
        ('2', 'justtyres', 'www.justtyres.co.uk'),
        ('3', 'b-quik', 'www.b-quik.com');


