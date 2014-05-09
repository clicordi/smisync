create table llx_dbsmi
(
	dbsmi_url varchar(255) NOT NULL,
	dbsmi_name varchar(255) NOT NULL,
	dbsmi_port integer NOT NULL,
	dbsmi_user varchar(255) NOT NULL,
	dbsmi_pwd varchar(255) NOT NULL,
	dbsmi_tpref varchar(255) NOT NULL
)ENGINE=innodb;