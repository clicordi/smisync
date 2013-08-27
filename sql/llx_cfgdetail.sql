create table llx_cfgdetail
(
  cfgdetail_rowid       integer AUTO_INCREMENT PRIMARY KEY,
  cfgdetail_column   varchar(255) NOT NULL,
  cfgdetail_label       varchar(255) NOT NULL,
  cfgdetail_table       varchar(255) NOT NULL,
  cfgdetail_display    integer DEFAULT 1 NOT NULL
)ENGINE=innodb;