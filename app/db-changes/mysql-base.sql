-- CREATE DATABASE IF NOT EXISTS pro_totems DEFAULT CHARACTER SET 'utf8' DEFAULT COLLATE 'utf8_spanish_ci';

CREATE TABLE sist_users (
    id_user         int(11) AUTO_INCREMENT NOT NULL,
    login           varchar(24) NOT NULL,
    passwd          char(32) NOT NULL,
    id_empresa      int(11) UNSIGNED NOT NULL DEFAULT '1',
    first_name      varchar(96) NULL,
    last_name       varchar(128) NULL,
    created         timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    active          tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
    salt_hash       char(32) NOT NULL,
    PRIMARY KEY(id_user),
    UNIQUE KEY(login)
);

CREATE TABLE sist_grp (
    id_grp       int(11) AUTO_INCREMENT NOT NULL,
    description  varchar(255) NOT NULL,
    created      timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(id_grp) 
);

CREATE TABLE sist_multigroup ( 
    id_user  int(11) UNSIGNED NOT NULL,
    id_grp   int(11) UNSIGNED NOT NULL,
    PRIMARY KEY(id_user,id_grp)
);

CREATE TABLE sist_activity (
    id_activity int(11) AUTO_INCREMENT NOT NULL, 
    id_user     int(11) NOT NULL,
    at          timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    k           char(3) NOT NULL,
    v           varchar(511) NULL,
    session     char(32) NOT NULL,
    PRIMARY KEY(id_activity),
    UNIQUE KEY(id_user,session,k,at)
);

CREATE TABLE sist_mails ( 
    id_mail       int(11) AUTO_INCREMENT NOT NULL,
    entry_date    timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    to_name       varchar(255) NOT NULL,
    to_mail       varchar(255) NOT NULL,
    subject       varchar(255) NOT NULL,
    body          longtext NOT NULL,
    footer        longtext NULL,
    sent          tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
    sent_date     timestamp NULL,
    PRIMARY KEY(id_mail)
);

CREATE TABLE sist_errores ( 
    id_error    int(11) AUTO_INCREMENT NOT NULL,
    errno       int(11) UNSIGNED NOT NULL,
    errstr      varchar(511) NOT NULL,
    errfile     varchar(255) NOT NULL,
    errline     int(11) NOT NULL,
    errctx      longtext NOT NULL,
    id_user     int(11) UNSIGNED NOT NULL,
    at          timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    seen        tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
    fixed       int(11) UNSIGNED NOT NULL DEFAULT '0',
    notes       varchar(255) NULL,
    duplicated  int(11) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY(id_error)
);

CREATE TABLE sist_menu ( 
    id_menu     int(11) AUTO_INCREMENT NOT NULL,
    visible     tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
    description varchar(64) NOT NULL,
    link        varchar(255) NOT NULL,
    id_grp      int(11) UNSIGNED NOT NULL,
    id_order    int(11) UNSIGNED NOT NULL DEFAULT '0',
    created     timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(id_menu)
);

CREATE TABLE sist_options_descriptions (
    id_option CHAR(3) NOT NULL,
    description VARCHAR(255) NOT NULL,
    PRIMARY KEY(id_option)
);
INSERT INTO sist_options_descriptions (id_option,description) VALUES ('sop','System Options');

CREATE TABLE sist_sessions ( 
  id_session  char(32) NOT NULL,
  ip          bigint(20) UNSIGNED NOT NULL,
  useragent   varchar(511) NULL,
  at          timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(`id_session`)
);

CREATE TABLE sist_options (
    id_option CHAR(3) NOT NULL,
    name      VARCHAR(96) NOT NULL,
    v         VARCHAR(128) NOT NULL DEFAULT '',
    id_user   INT(11) UNSIGNED NOT NULL DEFAULT '0',
    created   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_option,name)
);

