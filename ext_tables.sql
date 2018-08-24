#
# Table structure for table 'tx_abdownloads_download'
#
CREATE TABLE tx_abdownloads_download (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,
	editlock tinyint(4) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	fe_group varchar(100) DEFAULT '0' NOT NULL,
	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(30) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3_origuid int(11) DEFAULT '0' NOT NULL,
	label tinytext NOT NULL,
	description text NOT NULL,
	tags tinytext NOT NULL,
	sponsored_description text NOT NULL,
	license tinytext NOT NULL,
	language_uid int(11) DEFAULT '0' NOT NULL,
	clicks int(11) DEFAULT '0' NOT NULL,
	click_ip tinytext NOT NULL,
	rating float unsigned DEFAULT '0' NOT NULL,
	votes int(11) unsigned DEFAULT '0' NOT NULL,
	vote_ip tinytext NOT NULL,
	status int(11) unsigned DEFAULT '0' NOT NULL,
	category int(11) DEFAULT '0' NOT NULL,
	contact tinytext NOT NULL,
	homepage tinytext NOT NULL,
	image blob NOT NULL,
	sponsored int(11) unsigned DEFAULT '0' NOT NULL,
	file blob NOT NULL,
	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid)
);



#
# Table structure for table 'tx_abdownloads_category'
#
CREATE TABLE tx_abdownloads_category (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	fe_group varchar(100) DEFAULT '0' NOT NULL,
	label tinytext NOT NULL,
	description text NOT NULL,
	parent_category int(11) unsigned DEFAULT '0' NOT NULL,
	image blob NOT NULL,
	PRIMARY KEY (uid),
	KEY parent (pid),
);



#
# Table structure for table 'tx_abdownloads_category_mm'
#
CREATE TABLE tx_abdownloads_category_mm (
  uid_local int(11) unsigned DEFAULT '0' NOT NULL,
  uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'be_groups'
#
CREATE TABLE be_groups (
	ab_downloads_categorymounts tinytext,
# 	ab_downloads_cmounts_usesubcats tinyint(4) unsigned DEFAULT '0' NOT NULL
	
);

#
# Table structure for table 'be_users'
#
CREATE TABLE be_users (
	ab_downloads_categorymounts tinytext,
# 	ab_downloads_cmounts_usesubcats tinyint(4) unsigned DEFAULT '0' NOT NULL
);
