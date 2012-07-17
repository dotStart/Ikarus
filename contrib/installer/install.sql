DROP TABLE IF EXISTS ikarus1_application;
CREATE TABLE ikarus1_application (
	applicationID INT UNSIGNED AUTO_INCREMENT NOT NULL,
	applicationTitle VARCHAR (255) NOT NULL,
	applicationAbbreviation VARCHAR (255) NOT NULL,
	className VARCHAR (400) NOT NULL,
	libraryPath TEXT NOT NULL,
	templatePath TEXT NOT NULL,
	packageID INT NOT NULL,
	PRIMARY KEY (applicationID)
);

DROP TABLE IF EXISTS ikarus1_cache_adapter;
CREATE TABLE ikarus1_cache_adapter (
	adapterID INT UNSIGNED AUTO_INCREMENT NOT NULL,
	adapterClass VARCHAR (255) NOT NULL,
	PRIMARY KEY (adapterID)
);

DROP TABLE IF EXISTS ikarus1_cache_source;
CREATE TABLE ikarus1_cache_source (
	connectionID INT UNSIGNED AUTO_INCREMENT NOT NULL,
	adapterID INT NOT NULL,
	adapterParameters TEXT NOT NULL,
	isDefaultConnection TINYINT (1) NOT NULL DEFAULT '0',
	fallbackFor INT NOT NULL,
	isDisabled TINYINT (1) NOT NULL DEFAULT '0',
	PRIMARY KEY (connectionID)
);

DROP TABLE IF EXISTS ikarus1_event_listener;
CREATE TABLE ikarus1_event_listener (
	listenerID INT UNSIGNED AUTO_INCREMENT NOT NULL,
	className VARCHAR (400) NOT NULL,
	eventName VARCHAR (255) NOT NULL,
	listenerClass VARCHAR (400) NOT NULL,
	inerhit TINYINT (1) NOT NULL DEFAULT '0',
	packageID INT NOT NULL,
	PRIMARY KEY (listenerID)
);

DROP TABLE IF EXISTS ikarus1_language;
CREATE TABLE ikarus1_language (
	languageID INT UNSIGNED AUTO_INCREMENT NOT NULL,
	languageName VARCHAR (255) NOT NULL,
	translatedName VARCHAR (255) NOT NULL,
	languageCode VARCHAR (20) NOT NULL,
	isEnabled TINYINT (1) NOT NULL DEFAULT '1',
	hasContent TINYINT (1) NOT NULL DEFAULT '0',
	isDefault TINYINT (1) NOT NULL DEFAULT '0',
	packageID INT NOT NULL,
	PRIMARY KEY (languageID)
);

DROP TABLE IF EXISTS ikarus1_language_variables;
CREATE TABLE ikarus1_language_variables (
	variableID INT UNSIGNED AUTO_INCREMENT NOT NULL,
	variableName VARCHAR (255) NOT NULL,
	variableContent VARCHAR (255) NOT NULL,
	isDynamicVariable TINYINT (1) NOT NULL DEFAULT '0',
	languageID INT NOT NULL,
	packageID INT NOT NULL,
	PRIMARY KEY(variableID),
	UNIQUE KEY(variableName, languageID)
);

DROP TABLE IF EXISTS ikarus1_option;
CREATE TABLE ikarus1_option (
	optionID INT UNSIGNED AUTO_INCREMENT NOT NULL,
	optionName VARCHAR (255) NOT NULL,
	optionValue TEXT NULL,
	optionType VARCHAR (255) NOT NULL,
	packageID INT NOT NULL,
	PRIMARY KEY (optionID)
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
	controllerTypeID INT UNSIGNED AUTO_INCREMENT NOT NULL,
	parameterName VARCHAR (255) NOT NULL,
	controllerNamespace VARCHAR (255) NOT NULL,
	packageID INT NOT NULL,
	PRIMARY KEY (controllerTypeID)
);

DROP TABLE IF EXISTS ikarus1_request_route;
CREATE TABLE ikarus1_request_route (
	routeID INT UNSIGNED AUTO_INCREMENT NOT NULL,
	parameterName VARCHAR (250) NOT NULL,
	routeName VARCHAR (255) NOT NULL,
	controllerName VARCHAR (255) NOT NULL,
	controllerNamespace VARCHAR (255) NOT NULL,
	packageID INT NOT NULL,
	PRIMARY KEY (routeID)
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
	PRIMARY KEY (sessionID, packageID)
);

DROP TABLE IF EXISTS ikarus1_style;
CREATE TABLE ikarus1_style (
	styleID INT UNSIGNED AUTO_INCREMENT NOT NULL,
	styleTitle VARCHAR (255) NOT NULL,
	authorName VARCHAR (255) NOT NULL,
	authorAlias VARCHAR (255) NULL,
	authorUrl TEXT NULL,
	styleVersion VARCHAR (255) NOT NULL,
	styleUrl TEXT NOT NULL,
	styleUrlAlias VARCHAR (255) NULL,
	licenseName VARCHAR (255) NOT NULL,
	licenseUrl VARCHAR (255) NOT NULL,
	environment VARCHAR (255) NOT NULL,
	isDefault TINYINT (1) NOT NULL DEFAULT '0',
	isEnabled TINYINT (1) NOT NULL DEFAULT '1',
	packageID INT NOT NULL,
	PRIMARY KEY(styleID)
);

DROP TABLE IF EXISTS ikarus1_style_css;
CREATE TABLE ikarus1_style_css (
	definitionID INT UNSIGNED AUTO_INCREMENT NOT NULL,
	cssSelector VARCHAR (255) NOT NULL,
	definitionComment VARCHAR (255) NOT NULL,
	cssCode TEXT NOT NULL,
	cssMediaQuery VARCHAR (255) NOT NULL,
	styleID INT NOT NULL,
	isEnabled TINYINT (1) NOT NULL DEFAULT '1',
	PRIMARY KEY (definitionID)
);

-- rows
INSERT INTO ikarus1_application (applicationAbbreviation, className, libraryPath, templatePath, packageID) VALUES ('ikarus', 'ikarus\\system\\application\\IkarusApplication', './lib/', './template/', 1);

INSERT INTO ikarus1_cache_adapter (adapterClass) VALUES ('DiskCacheAdapter');
INSERT INTO ikarus1_cache_source (adapterID, adapterParameters, isDefaultConnection, fallbackFor, isDisabled) VALUES (1, '', 1, 0, 0);

INSERT INTO ikarus1_language (languageName, translatedName, languageCode, isEnabled, hasContent, isDefault, packageID) VALUES ('English', 'English', 'en', 1, 1, 1, 1);

INSERT INTO ikarus1_option (optionID, optionName, optionValue, optionType, packageID) VALUES
	(NULL,	'global.advanced.debug',		'1',	'boolean',	1),
	(NULL,	'filesystem.general.defaultAdapter',	'Disk',	'text',		1),
	(NULL,	'filesystem.general.adapterParameters',	NULL,	'serialized',	1);

INSERT INTO ikarus1_request_controller_type (controllerTypeID, parameterName, controllerDirectory, packageID) VALUES
	(NULL, 'action', 'action/', 1),
	(NULL, 'form', 'form/', 1),
	(NULL, 'page', 'page/', 1);

INSERT INTO ikarus1_style (styleTitle, authorName, authorAlias, authorUrl, styleVersion, styleUrl, styleUrlAlias, licenseName, licenseUrl, environment, isDefault, isEnabled, packageID) VALUES ('Ikarus Default Administration', 'Johannes Donath', 'Akkarin', 'http://www.akkarin.de', '1.0.0 Alpha 1', 'http://www.ikarus-framework.de', 'Ikarus Framework', 'GNU Lesser Public License', 'http://www.gnu.org/licenses/lgpl.txt', 'administration', 1, 1, 1);