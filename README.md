# IPLog - The Documentation

## Disclaimer
This software is capable of usage that goes against the principles of data protection (more on data protection on [Privacy International](https://www.privacyinternational.org/node/44) and the [OECD Privacy Principles](http://oecdprivacy.org)), and any damage incurred by such usage is solely the responsibility of the individual making use of IPLog (hereinafter referred to as "the Software") for nefarious purposes.

The author of IPLog (hereinafter referred to as "the Author") does not hold themselves liable for any such abuse of the Software, and any legal action against the Author for the provision of this software is thus unjustified.

The contents of this disclaimer apply in addition to the MIT license.

## Use case
IPLog is a honeypot (IPLog's primary purpose) that mimics as a content serving website, whereas all it does is lead the person visiting the page (the client) to a fake 404 page and logs their IP address, access query used, log time and optionally further information.

In order to use IPLog, simply clone the Git repository and copy the files to your desired location. The server has to support PHP with the SQLite3 extension (usually comes with `PHP 5 >= 5.3.0` and upwards). When calling the script with the relevant parameters (see config section), IPLog will do its job. The file `index.php` can be renamed to any other name,

By default, the query parameter `imgid` makes IPLog URLs resemble an image serving website. When a client clicks on such a URL (e.g. `http://example.org/?imgid=test.jpg`), the client's IP address is automatically logged in an SQLite3 database, together with the log time and query, which triggered the log (in this case `test.jpg`), and optional IP geolocation information, if enabled in the config file. Instead of an actual file, a realistic 404 page will be served to the client, complete with proper headers.

Even upon refresh, the script will not show the client their report, and will keep on showing the 404 page. However, this behavior can be bypassed by modifying the config file as found in the Config section of this documentation.

IPLog is useful for multiple purposes, such as:

1. A honeypot to catch hacker attempts by URLs.
2. A tool to prank people on chat sites such as Omegle or Chatroulette.
3. A method to ensure wether your friend/partner is really located where they pretend to be. Send 'em a link, and bust 'em!

Do note that \#1 on this list was the purpose of creating IPLog. Also, please be aware that \#2 and \#3 may or may not get you into trouble, so use with care!

Original use case was a set up involving links on my server desktop that looked like links to banking websites, whereas in reality, each link was pointing to an instance of IPLog, with the query name identifying which links the hacker has opened. It worked like a charm, and I used the full report for investigating further into a recent hack attack!

## Config
IPLog supports numerous config options, which alter its behavior, which are configured using the `config.php` file:

- `DB_FILE`: The SQLite database file to which all client access events should be logged. If non-existent, new file with given name will be created and used.

  Default is `IPs.sqlite`.

- `ENABLE_FORCESHOW`: This option, when set to `true`, allows the client to specify the parameter `forceShow=true` in the URL, which will allow them to see their IPLog report.

  Default is `false`.

- `ENABLE_ALL_CLIENTS_REPORT`: When enabled, this option shows the complete IPLog report for all collected IP addresses and all queries when the parameter `allClients=true` is specified. Note that this parameter takes precedence over all other parameters, except when used with `REPORT_ACCESS_SECRET`.

  Default is `true`.

- `REPORT_ACCESS_SECRET`: When empty, access to a report for all clients is open to everyone. When set, then `allClients=true` will only work if secret is set to `REPORT_ACCESS_SECRET`.

  Default is `mySecret`.

- `LOG_MODE`: When set to `single`, only 1 client per query is logged, when set to `multi`, all requests for a particular query are logged.

  Default is `single`.

- `PROCESS_IP`: If set to `true`, the IP that is logged will be sent to the IP-API at http://ip-api.com/json/ for geolocation and ISP detection purposes. If set to `false`, the country, city and ISP will be left empty.

  Default is `true`.

- `QUERY_PARAM`: Changing this can make IPLog mimic any service, as this is the query parameter in the URL, e.g. setting `QUERY_PARAM` to `employeeData` makes the URL resemble an employee database. Do not forget to update the fake error page template accordingly to reflect the type of service to be imitated!

  Default is `imgid`.

- `TIMEZONE`: this is the timezone for log times. All PHP timezones are valid.
  - Default is `Europe/Berlin`.
- `REPORT_PAGE_TPL`: The HTML template (`.tpl` file) in which the entry/entries (placeholder: `{{entries}}`) should be displayed.

  Default is `report_page.tpl`.

- `REPORT_ENTRY_TPL`: The HTML template (`.tpl` file), which formats each IPLog entry. Placeholders are:
  - `{{query}}`: The query that triggered the recording of the IP address.
  - `{{logtime}}`: The time and date at which the entry was logged, in accordance with the `TIMEZONE`.
  - `{{clientIP}}`: The IP address of the logged client.
  - `{{country}}`: If `PROCESS_IP` is enabled, the country to which the IP belongs.
  - `{{city}}`: If `PROCESS_IP` is enabled, the city to which the IP belongs.
  - `{{isp}}` If `PROCESS_IP` is enabled, the ISP which has provided the IP.

  Default is `report_entry.tpl`.

- `FAKE_ERROR_TPL`: The HTMl template (`.tpl` file), which formats the fake error page displayed to the client. Placeholder: `{{query}}`, the query that has triggered the error page.

  Default is `fake_error.tpl`.

## Screenshots
Screenshots of IPLog in action can be found in the `IPLog_screenshots` directory. IP addresses have been redacted for data protection purposes.

## What about proxies?
Yes, smart hackers will use proxies, but not all will do so! IPLog is not a cure-all when it comes to logging hacker IPs - it merely does what it does as well as possible, within the limits of IP addresses and proxy technologies.
