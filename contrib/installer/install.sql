DROP TABLE IF EXISTS ikarus1_cache_adapter;
CREATE TABLE ikarus1_cache_adapter (
	adapterID INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	adapterClass VARCHAR (255) NOT NULL
);

DROP TABLE IF EXISTS ikarus1_cache_source;
CREATE TABLE ikarus1_cache_source (
	connectionID INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	adapterID INT NOT NULL,
	adapterParameters TEXT NOT NULL,
	isDefaultConnection TINYINT (1) NOT NULL DEFAULT '0',
	fallbackFor INT NOT NULL,
	isDisabled TINYINT (1) NOT NULL DEFAULT '0'
);

DROP TABLE IF EXISTS ikarus1_event_listener;
CREATE TABLE ikarus1_event_listener (
	listenerID INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	className VARCHAR (400) NOT NULL,
	eventName VARCHAR (255) NOT NULL,
	listenerClass VARCHAR (400) NOT NULL,
	inerhit TINYINT (1) NOT NULL DEFAULT '0',
	packageID INT NOT NULL
);

DROP TABLE IF EXISTS ikarus1_option;
CREATE TABLE ikarus1_option (
	optionID INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	optionName VARCHAR (255) NOT NULL,
	optionValue TEXT NULL,
	optionType VARCHAR (255) NOT NULL,
	packageID INT NOT NULL
);

DROP TABLE IF EXISTS ikarus1_package_dependency;
CREATE TABLE ikarus1_package_dependency (
	packageID INT NOT NULL,
	dependencyID INT NOT NULL,
	dependencyLevel INT NOT NULL DEFAULT '0',
	PRIMARY KEY (packageID, dependencyID)
);

-- rows
INSERT INTO ikarus1_cache_adapter (adapterClass) VALUES ('DiskCacheAdapter');
INSERT INTO ikarus1_cache_source (adapterID, adapterParameters, isDefaultConnection, fallbackFor, isDisabled) VALUES (1, '', 1, 0, 0);

INSERT INTO ikarus1_option (optionID, optionName, optionValue, optionType, packageID) VALUES
	(NULL,	'global.advanced.debug',				'1',	'boolean',	1),
	(NULL,	'filesystem.general.defaultAdapter',	'0',	'boolean',	1),
	(NULL,	'filesystem.general.adapterParameters',	NULL,	'serialized', 1);