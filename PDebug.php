<?php

namespace Poundation;

class PDebug extends PObject
{

    private $sessionId;

    private $host;

    private $port;

    /**
     * Refreshes the debugging settings so the next request will be debugged again.
     * @return bool
     */
    static function refreshDebugger() {
        $debugger = self::debuggerFromRequest();
        if ($debugger) {
            return $debugger->enableDebugging();
        }
        return false;
    }

    /**
     * Returns the debugger object which is already in use.
     * @return null|PDebug
     */
    static function debuggerFromRequest() {

        $sessionID = rand(10000, 20000);
        if (isset($_GET['debug_session_id'])) {
            $sessionID = $_GET['debug_session_id'];
        } else if (isset($_COOKIE['debug_session_id'])) {
            $sessionID = $_COOKIE['debug_session_id'];
        }

        $host = null;
        if (isset($_GET['debug_host'])) {
            $host = $_GET['debug_host'];
        } else if (isset($_COOKIE['debug_host'])) {
            $host = $_COOKIE['debug_host'];
        } else {
            $host = $_SERVER['REMOTE_ADDR'];
        }

        $port = null;
        if (isset($_GET['debug_port'])) {
            $port = $_GET['debug_port'];
        } else if (isset($_COOKIE['debug_port'])) {
            $port = $_COOKIE['debug_port'];
        } else  {
            $port = 10137;
        }

        if ($sessionID && $host && $port) {
            $debugger = new PDebug($sessionID, $host, $port);
            return $debugger;
        }

        return null;
    }

    public function __construct($sessionID = null, $host = null, $port = null) {
        $this->sessionId = $sessionID;
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * Sets the session id.
     * @param $sessionID
     * @return PDebug
     */
    public function setSessionID($sessionID) {
        $this->sessionId = $sessionID;
        return $this;
    }

    /**
     * Returns the session id.
     * @return null
     */
    public function getSessionID() {
        return $this->sessionId;
    }

    /**
     * Sets the debugging host address (the one with the IDE running).
     * @param $host
     * @return PDebug
     */
    public function setHost($host) {
        $this->host = $host;
        return $this;
    }

    /**
     * Returns the debugging host.
     * @return null
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * Sets the debugging port. Must match the port specified in the debugging IDE.
     * @param $port
     * @return PDebug
     */
    public function setPort($port) {
        $this->port = $port;
        return $this;
    }

    /**
     * Returns the debugging port.
     * @param $port
     * @return null
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * Starts the debugging session.
     * @return bool
     */
    public function enableDebugging() {
        return $this->writeCookies($this->getSessionID(), $this->getHost(), $this->getPort(), 0);
    }

    public function writeCookies($sessionId, $host, $port, $expires) {
        if (!is_null($sessionId) && !is_null($host)) {
            $doPort = (is_null($port) || $port <= 0) ? 10137 : $this->getPort();
            setcookie('start_debug', 1, $expires,'/');
            setcookie('debug_start_session', 1, $expires, '/');
            setcookie('debug_session_id', $sessionId, $expires, '/');
            setcookie('debug_port', $port, $expires, '/');
            setcookie('debug_host', $host, $expires, '/');
            return true;
        }
        return false;
    }

}