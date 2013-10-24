<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('events')};
CREATE TABLE {$this->getTable('events')} (
          `event_id` mediumint(9) NOT NULL auto_increment,        
          `event_title` varchar(250) default NULL,                
          `event_venu` varchar(250) default NULL,                 
          `event_start_date` datetime default NULL,                   
          `event_end_date` datetime default NULL,                     
          `event_image` mediumtext,                               
          `event_thumb_image` mediumtext,                         
          `event_medium_image` mediumtext,                        
          `event_content` mediumtext,                             
          `event_video` mediumtext,                               
          `event_status` int(10) default NULL,                    
          `event_url_prefix` varchar(250) default NULL,           
          `event_page_title` varchar(250) default NULL,           
          `event_meta_keywords` text,                             
          `event_meta_description` text,                          
          `contact_name` varchar(250) default NULL,               
          `contact_phone` varchar(250) default NULL,              
          `contact_fax` varchar(250) default NULL,                
          `contact_email` varchar(250) default NULL,              
          `contact_address` text,                                 
          `created_time` datetime default NULL,                   
          `update_time` datetime default NULL,                    
          PRIMARY KEY  (`event_id`)                               
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS {$this->getTable('events_gallery')};
CREATE TABLE {$this->getTable('events_gallery')} (                                                                                           
                  `image_id` mediumint(9) NOT NULL auto_increment,                                                                        
                  `image_file` mediumtext,                                                                                                
                  `image_name` varchar(250) default NULL,                                                                                 
                  `image_order` int(250) default NULL,                                                                                    
                  `image_status` int(10) default NULL,                                                                                    
                  `events_id` mediumint(9) NOT NULL,                                                                                      
                  PRIMARY KEY  (`image_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS {$this->getTable('events_product')};
CREATE TABLE {$this->getTable('events_product')} (                          
                  `id` int(250) unsigned NOT NULL auto_increment,        
                  `eventid` mediumint(9) default NULL,                   
                  `product_id` mediumint(9) default NULL,                
                  PRIMARY KEY  (`id`)                                    
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS {$this->getTable('events_store')};                
CREATE TABLE {$this->getTable('events_store')} (                
                `event_id` int(11) unsigned NOT NULL,      
                `store_id` smallint(5) unsigned NOT NULL,  
                PRIMARY KEY  (`event_id`,`store_id`),      
                KEY `FK_EVENTS_STORE_STORE` (`store_id`)   
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8;                         
    ");

$installer->endSetup(); 
