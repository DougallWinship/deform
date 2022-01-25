/*
this is meant to be a reasonable rather than comprehensive set
@see https://mariadb.com/kb/en/data-types/
*/

/* TABLE DEFINITIONS */
CREATE TABLE test2
(
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `textField` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE test
(
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,

    /* numeric */
    `tinyintField` TINYINT,
    `boolField` BOOLEAN,
    `smallintField` SMALLINT,
    `mediumintField` MEDIUMINT,
    `intField` INT,
    `unsignedIntField` INT UNSIGNED,
    `bigintField` BIGINT,
    `unsignedBigintField` BIGINT UNSIGNED,
    `defaultDecimalField` DECIMAL,
    `unsignedDecimalField` DECIMAL UNSIGNED,
    `currencyDecimalField` DECIMAL(10,2),
    `floatField` FLOAT,
    `unsignedFloatField` FLOAT UNSIGNED,
    `doubleField` DOUBLE,
    `unsignedDoubleField` DOUBLE UNSIGNED,
    `bitField` BIT(8),

    /* strings */
    `binaryField` BINARY(10),
    `blobField` BLOB,
    `enumField` ENUM('One','Two','Three'),
    `charField` CHAR,
    `largeCharField` CHAR(255),
    /* `inetField` INET6, */
    `jsonField` JSON,
    `mediumtextField` MEDIUMTEXT,
    `textField` TEXT,
    `longTextField` LONGTEXT,
    `tinytextField` TINYTEXT,

    /* dates & times */
    `dateField` DATE,
    `timeField` TIME,
    `datetimeField` DATETIME,
    `timestampField` TIMESTAMP,
    `yearField` YEAR,

    /* foreign key fields */
    `test2id` INT UNSIGNED NOT NULL,

    /* keys & constraints */
    CONSTRAINT `fk_test2_id` FOREIGN KEY (`test2id`) REFERENCES `test2`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/* DATA */
INSERT INTO `test2`(`id`, `textField`)
VALUES
    (1,'One'),
    (2,'Two'),
    (3,'Three');

INSERT INTO test(
    `tinyintField`,
    `boolField`,
    `smallintField`,
    `mediumintField`,
    `intField`,
    `unsignedIntField`,
    `bigintField`,
    `unsignedBigintField`,
    `defaultDecimalField`,
    `unsignedDecimalField`,
    `currencyDecimalField`,
    `floatField`,
    `unsignedFloatField`,
    `doubleField`,
    `unsignedDoubleField`,
    `bitField`,

    /* strings */
    `binaryField`,
    `blobField`,
    `enumField`,
    `charField`,
    `largeCharField`,
    /* inetField, */
    `jsonField`,
    `mediumtextField`,
    `textField`,
    `longTextField`,
    `tinytextField`,

    /* dates & times */
    `dateField`,
    `timeField`,
    `datetimeField`,
    /* `timestampField`,*/
    `yearField`,

    /* foreign key fields */
    `test2id`
)

VALUES (
           127,
           1,
           32000,
           8380000,
           -2147480000,
           4294960000,
           -9223372036854770000,
           18446744073709550000,
           -1234567890,
           1234567890,
           123.12,
           -1234567890.34,
           1234567890.34,
           -1234567890.56,
           1234567890.56,
           b'01010101',

           /* strings */
           '1234567890',
           null,
           'Two',
           'c',
           'largeCharField',
           /* '2001:db8::ff00:42:8329',*/
           '{"id": 1, "foo": "bar"}',
           'mediumtextField',
           'textField',
           'longTextField',
           'tinytextField',

           /* dates & times */
           '2022-01-01',
           '01:01:01',
           '2022-01-01 01:01:01',
           /* timestampField,*/
           '2022',

           /* foreign key fields */
           1
       );



