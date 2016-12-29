<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * IPLog.
 */
class IPLog
{
    /**
     * The database object.
     *
     * @var object
     */
    public $db;
    /**
     * The client query.
     *
     * @var string
     */
    public $query;
    /**
     * Whether the client data should be forced to be displayed.
     *
     * @var bool
     */
    public $forceShow;
    /**
     * Whether a report of all clients should be generated.
     *
     * @var bool
     */
    public $allClients;
    /**
     * The IP address of the client.
     *
     * @var string
     */
    public $clientIP;

    /**
     * The constructor initializes IPLog.
     */
    public function __construct()
    {
        require_once 'config.php';

        date_default_timezone_set(TIMEZONE);

        $this->db = new SQLite3(DB_FILE);
        $this->db->exec('CREATE TABLE IF NOT EXISTS logged_ips (logtime TEXT, clientIP TEXT, query TEXT, country TEXT, city TEXT, isp TEXT)');

        if (isset($_GET[QUERY_PARAM])) {
            $this->query = $_GET[QUERY_PARAM];
        } else {
            $this->query = null;
        }

        if (isset($_GET['forceShow']) && ENABLE_FORCESHOW && $_GET['forceShow'] == true) {
            $this->forceShow = true;
        } else {
            $this->forceShow = false;
        }

        if (isset($_GET['allClients']) && ENABLE_ALL_CLIENTS_REPORT && $_GET['allClients'] == true) {
            if (REPORT_ACCESS_SECRET !== '') {
                if (isset($_GET['secret']) && REPORT_ACCESS_SECRET === $_GET['secret']) {
                    $this->allClients = true;
                } else {
                    $this->allClients = false;
                }
            } else {
                $this->allClients = true;
            }
        } else {
            $this->allClients = false;
        }

        $this->clientIP = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
    }

    /**
     * This determines the action to be performed based on the config
     * and URL parameters passed by the client.
     */
    public function determineAction()
    {
        $stmt = $this->db->prepare('SELECT * FROM logged_ips WHERE query = :query');
        $stmt->bindValue(':query', $this->query);
        $result = $stmt->execute();

        $data = $result->fetchArray(SQLITE3_ASSOC);

        if ($this->allClients) {
            echo $this->generateAllClientsReport();
            header('HTTP/1.1 200 OK');
            die();
        } elseif (empty($this->query)) {
            echo $this->returnFakeNotFound('UNDEFINED');
            header('HTTP/1.1 404 Not Found');
            die();
        } elseif (!$data) {
            $this->insertNewClient($this->query, $this->clientIP);
            echo $this->returnFakeNotFound($this->query);
            header('HTTP/1.1 404 Not Found');
            die();
        } elseif ($data['clientIP'] !== $this->clientIP || $this->forceShow) {
            echo $this->generateClientReportForQuery($this->query);
            header('HTTP/1.1 200 OK');
            die();
        } else {
            if (LOG_MODE === 'MULTI') {
                $this->insertNewClient($this->query, $this->clientIP);
            }
            echo $this->returnFakeNotFound($this->query);
            header('HTTP/1.1 404 Not Found');
            die();
        }
    }

    /**
     * This function generates a report of all client data.
     */
    public function generateAllClientsReport()
    {
        $result = $this->db->query('SELECT * FROM logged_ips');
        $entries = '';

        while ($data = $result->fetchArray(SQLITE3_ASSOC)) {
            $entries .= $this->processReportEntry(REPORT_ENTRY_TPL, $data);
        }

        return $this->processReportPage(REPORT_PAGE_TPL, $entries, 'IPLog report for all clients');

        header('HTTP/1.1 200 OK');
    }

    /**
     * This function generates a report of clients based on a particular query.
     * @param  string $query Client query
     * @return string        Report of clients
     */
    public function generateClientReportForQuery($query)
    {
        $stmt = $this->db->prepare('SELECT * FROM logged_ips WHERE query = :query');
        $stmt->bindValue(':query', $query);
        $result = $stmt->execute();
        $entries = '';

        while ($data = $result->fetchArray(SQLITE3_ASSOC)) {
            $entries .= $this->processReportEntry(REPORT_ENTRY_TPL, $data);
        }

        return $this->processReportPage(REPORT_PAGE_TPL, $entries, 'IPLog report for client with query "'.$query.'"');

        header('HTTP/1.1 200 OK');
    }

    /**
     * This function processes the data of a client into a report entry.
     * @param  string $tpl  Template for formating the report entry
     * @param  array  $data Array from database with client data
     * @return string       Formatted client report entry in HTML
     */
    public function processReportEntry($tpl, $data)
    {
        $tpl = file_get_contents(REPORT_ENTRY_TPL);

        $result = str_replace(array(
            '{{title}}',
            '{{query}}',
            '{{logtime}}',
            '{{clientIP}}',
            '{{country}}',
            '{{city}}',
            '{{isp}}',
        ), array(
            'Report for: '.$data['clientIP'],
            $data['query'],
            $data['logtime'],
            $data['clientIP'],
            $data['country'],
            $data['city'],
            $data['isp'],
        ),  $tpl).PHP_EOL;

        return $result;
    }

    /**
     * This function inserts the client entries into a report page template.
     * @param  string $tpl   Name of the report page template
     * @param  string $data  The HTML data to be insreted into the reports page
     * @param  string $title The title of the report page
     * @return [type]        [description]
     */
    public function processReportPage($tpl, $data, $title)
    {
        $tpl = file_get_contents(REPORT_PAGE_TPL);

        $result = str_replace(array(
            '{{entries}}', '{{title}}',
        ), array($data, $title), $tpl);

        return $result;
    }

    /**
     * This generates a fake 404 not found page.
     * @param  string $query Query that triggered the error page
     * @return string        The markup of the error page
     */
    public function returnFakeNotFound($query)
    {
        $fakeErrorTpl = file_get_contents(FAKE_ERROR_TPL);
        $out = str_replace('{{query}}', $query, $fakeErrorTpl);
        return $out;
    }

    /**
     * This inserts a new client into the database.
     * @param  string $query    The query that triggered the client insertion
     * @param  string $clientIP The IP address of the client
     */
    public function insertNewClient($query, $clientIP)
    {
        $APIURL = 'http://ip-api.com/json/';
        $logtime = date('Y-m-d H:i:s');
        $country = null;
        $city = null;
        $isp = null;

        if (PROCESS_IP) {
            $APIResponse = json_decode(file_get_contents($APIURL.$clientIP), true);
            if ($APIResponse['status'] === 'success') {
                $country = $APIResponse['country'];
                $city = $APIResponse['city'];
                $isp = $APIResponse['isp'];
            }
        }

        $stmt = $this->db->prepare('INSERT INTO logged_ips (logtime, clientIP, query, country, city, isp) VALUES (:logtime, :clientIP, :query, :country, :city, :isp)');
        $stmt->bindValue(':logtime', $logtime);
        $stmt->bindValue(':clientIP', $clientIP);
        $stmt->bindValue(':query', $query);
        $stmt->bindValue(':country', $country);
        $stmt->bindValue(':city', $city);
        $stmt->bindValue(':isp', $isp);
        $result = $stmt->execute();
    }
}

$IPLog = new IPLog();
$IPLog->determineAction();
