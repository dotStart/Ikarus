DROP TABLE IF EXISTS ikarus1_application;
CREATE TABLE ikarus1_application (
	applicationID INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	applicationTitle VARCHAR (255) NOT NULL,
	applicationAbbreviation VARCHAR (255) NOT NULL,
	className VARCHAR (400) NOT NULL,
	libraryPath TEXT NOT NULL,
	packageID INT NOT NULL
);

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

DROP TABLE IF EXISTS ikarus1_request_controller_type;
CREATE TABLE ikarus1_request_controller_type (
	controllerTypeID INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	parameterName VARCHAR (255) NOT NULL,
	controllerDirectory VARCHAR (255) NOT NULL,
	packageID INT NOT NULL
);

DROP TABLE IF EXISTS ikarus1_session;
CREATE TABLE ikarus1_session (
	sessionID VARCHAR (255) NOT NULL,
	sessionData TEXT NOT NULL,
	humanReadableUserIdentifier VARCHAR (255) NULL,
	userID INT NULL,
	ipAddress VARBINARY (16) NOT NULL,
	userAgent VARCHAR (256) NOT NULL,
	packageID INT NOT NULL,
	environment VARCHAR (255) NOT NULL,
	abbreviation VARCHAR (255) NOT NULL,
	PRIMARY KEY (sessionID, packageID, environment)
);

-- rows
INSERT INTO ikarus1_application (applicationAbbreviation, className, libraryPath, packageID) VALUES ('ikarus', 'ikarus\\system\\application\\IkarusApplication', './lib/', 1);

INSERT INTO ikarus1_cache_adapter (adapterClass) VALUES ('DiskCacheAdapter');
INSERT INTO ikarus1_cache_source (adapterID, adapterParameters, isDefaultConnection, fallbackFor, isDisabled) VALUES (1, '', 1, 0, 0);

INSERT INTO ikarus1_option (optionID, optionName, optionValue, optionType, packageID) VALUES
	(NULL,	'global.advanced.debug',				'1',	'boolean',	1),
	(NULL,	'filesystem.general.defaultAdapter',	'0',	'boolean',	1),
	(NULL,	'filesystem.general.adapterParameters',	NULL,	'serialized', 1);

INSERT INTO ikarus1_request_controller_type (controllerTypeID, parameterName, controllerDirectory, packageID) VALUES
	(NULL, 'action', 'action/', 1),
	(NULL, 'form', 'form/', 1),
	(NULL, 'page', 'page/', 1);