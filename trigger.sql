DROP TRIGGER IF EXISTS `tri_ecp_created`;

DELIMITER $$

CREATE TRIGGER `tri_ecp_created` AFTER INSERT ON `provider`
FOR EACH ROW BEGIN
DECLARE p_phone_no INT(1);
DECLARE p_fax_no INT(1);
DECLARE p_dob INT(1);

set p_phone_no = 0;
set p_fax_no  = 0;
set p_dob  = 0;

IF  NEW.provider_phone <> '' THEN
set p_phone_no = 1;
END IF;

IF NEW.provider_fax <> '' THEN
set p_fax_no = 1;
END IF;

IF NEW.birth_date <> '' THEN
set p_dob = 1;
END IF;

INSERT INTO provider_profile_completion (provider_id,profile_image, name,dob)
VALUES((SELECT new.idprovider FROM provider order by new.idprovider DESC limit 1),p_image,p_fname,p_dob);

INSERT INTO provider_sub_detail (provider_id) values (NEW.idprovider);
END$$

//////////////////////////
//backup table TRIGGER
DELIMITER $$

create trigger order_map_backup after insert on order_mapping
for each row
begin
  insert into order_mapping_bk (id,order_id,patient_id,provider_id,practice_id,sales_user_id,added_date,modified_date) values (NEW.id,NEW.order_id,NEW.patient_id,NEW.provider_id,NEW.practice_id,NEW.sales_user_id,NEW.added_date,NEW.modified_date);
end$$

DELIMITER ;

//////////////////

ROP TABLE IF EXISTS 'practice_sub_detail';
CREATE TABLE `practice_sub_detail` (
  `practice_id` int(11) NOT NULL,
  `patient_count` int(11) NOT NULL DEFAULT '0',
  `active_provider` int(11) NOT NULL DEFAULT '0',
  `active_patient` int(11) NOT NULL DEFAULT '0',
  `recent_order_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `recent_patient_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted` tinyint(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `practice_sub_detail`
  ADD PRIMARY KEY (`practice_id`);

INSERT INTO practice_sub_detail (practice_id) (SELECT id FROM practice);

DROP TRIGGER IF EXISTS `practice_id_in_sub_detail`;

DELIMITER $$
CREATE TRIGGER `practice_id_in_sub_detail` AFTER INSERT ON `practice`
 FOR EACH ROW IF  NEW.id <> '' THEN
  INSERT INTO practice_sub_detail (practice_id) values (NEW.id);
END IF;
END $$