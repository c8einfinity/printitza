DELIMITER $$

DROP TRIGGER /*!50032 IF EXISTS */ `insert_mageworx_option_id`$$

CREATE    
    TRIGGER `insert_mageworx_option_id` BEFORE INSERT ON `catalog_product_option` 
    FOR EACH ROW BEGIN
IF (NEW.mageworx_option_id IS NULL) THEN SET NEW.mageworx_option_id = UUID(); END IF;
END;
$$

DELIMITER ;


DELIMITER $$

DROP TRIGGER /*!50032 IF EXISTS */ `insert_mageworx_option_type_id`$$

CREATE    
    TRIGGER `insert_mageworx_option_type_id` BEFORE INSERT ON `catalog_product_option_type_value` 
    FOR EACH ROW BEGIN
IF (NEW.mageworx_option_type_id IS NULL) THEN SET NEW.mageworx_option_type_id = UUID(); END IF;
END;
$$

DELIMITER ;

DELIMITER $$

DROP TRIGGER /*!50032 IF EXISTS */ `insert_template_mageworx_option_id`$$

CREATE    
    TRIGGER `insert_template_mageworx_option_id` BEFORE INSERT ON `mageworx_optiontemplates_group_option` 
    FOR EACH ROW BEGIN
IF (NEW.mageworx_option_id IS NULL) THEN SET NEW.mageworx_option_id = UUID(); END IF;
END;
$$

DELIMITER ;

DELIMITER $$

DROP TRIGGER /*!50032 IF EXISTS */ `insert_template_mageworx_option_type_id`$$

CREATE    
    TRIGGER `insert_template_mageworx_option_type_id` BEFORE INSERT ON `mageworx_optiontemplates_group_option_type_value` 
    FOR EACH ROW BEGIN
IF (NEW.mageworx_option_type_id IS NULL) THEN SET NEW.mageworx_option_type_id = UUID(); END IF;
END;
$$

DELIMITER ;