<?php
/*
 * $Id: class.SieveSession.inc.php,v 1.1 2003/10/12 21:21:09 lkneschke Exp $
 *
 * Copyright 2002 Stephen Grier <stephengrier@users.sourceforge.net>
 *
 * See the inclosed smartsieve-NOTICE file for conditions of use and distribution.
 */


/*
 * class SieveSession provides an interface to a sieve server, such 
 * as Cyrus timsieved, via the managesieve protocol.
 *
 */
class SieveSession {

    var $server;  /* sieve server to open session to. */
    var $port;    /* port to connect to. */
    var $proxy;   /* proxy authorization user. */
    var $uid;     /* authentication user. */
    var $passwd;  /* authentication user's password. */
    var $implentation;    /* server implementation and version. */
    var $saslmethods;     /* array: SASL auth methods server supports. */
    var $extensions;      /* array: sieve extensions server supports. */
    var $starttls_avail;  /* boolean: will server do starttls? */
    var $socket;          /* file pointer to open socket. */
    var $socket_timeout;  /* socket timeout in seconds. */
    var $scriptlist;      /* array: user's scripts on the server. */
    var $activescript;    /* the currently active script on server (if any). */
    var $errstr;          /* error text. */

    /* 
     * class constructor. 
     */
    function SieveSession ($server='127.0.0.1', $port='2000', $uid, $passwd, $proxy='', $socket_timeout=2) {

	$this->server = $server;
	$this->port = $port;
	$this->uid = $uid;
	$this->passwd = $passwd;
	$this->proxy = $proxy;
        $this->socket_timeout = $socket_timeout;

	$this->implementation = array('unknown');
	$this->saslmethods = array('unknown');
	$this->extensions = array('unknown');
	$this->starttls_avail = false;
        $this->scriptlist = array();
        $this->activescript = '';
        $this->errstr = '';

    }
    // end constructor


    // class methods

    /*
     * start a sieve session. open a socket to $this->server, read and 
     * note server capabilities, and then attempt to authenticate the 
     * user.
     */
    function start () {

	if(!isset($this->socket)){
	    $this->socket = fsockopen($this->server, $this->port, $this->errnum, $this->errstr, "60");
	}
	if (!$this->socket) {
	    return false;
	}

	$said = $this->read();
	if (!preg_match("/timsieved/i",$said)) {
	    $this->close();
	    $this->errstr = "start: bad response from $this->server: $said";
	    return false;
	}

	// If response starts ""IMPLEMENTATION" "(..." server is Cyrus version 2.
	// else, we will assume Cyrus version 1.

	if (preg_match("/IMPLEMENTATION/",$said))
	{
	  while (!preg_match("/^OK/",$said)) {
	    if (preg_match("/^\"IMPLEMENTATION\" +\"(.*)\"/",$said,$bits))
		$this->implementation = $bits[1];
	    elseif (preg_match("/^\"SASL\" +\"(.*)\"/",$said,$bits)) {
		$auth_types = $bits[1];
		$this->saslmethods = split(" ", $auth_types);
	    }
	    elseif (preg_match("/^\"SIEVE\" +\"(.*)\"/",$said,$bits)) {
		$extensions = $bits[1];
		$this->extensions = split(" ", $extensions);
	    }
            elseif (preg_match("/^\"STARTTLS\"/",$said))
                $this->starttls_avail = true;
	    $said = $this->read();
	  }
	}
	else
	{
	    // assume cyrus v1.
	    if (preg_match("/\"(.+)\" +\"(.+)\"/",$said,$bits)) {
		$this->implementation = $bits[1];
		$sasl_str = $bits[2];  // should look like: SASL={PLAIN,...}
		if (preg_match("/SASL=\{(.+)\}/",$sasl_str,$morebits)) {
		    $auth_types = $morebits[1];
		    $this->saslmethods = split(", ", $auth_types);
		}
	    }
	    else {
		// a bit desperate if we get here.
		$this->implementation = $said;
		$this->saslmethods = $said;
	    }
	}

//	$said = $this->read();   /* retrieve \n following OK. */

	$authstr = $this->proxy . "\x00" . $this->uid . "\x00" . $this->passwd;
	$encoded = base64_encode($authstr);
	$len = strlen($encoded);
	fputs($this->socket,"AUTHENTICATE \"PLAIN\" \{$len+}\r\n");
	fputs($this->socket,"$encoded\r\n");
	$said = $this->read();

	if (preg_match("/NO/",$said)) {
	    $this->close();
	    $this->errstr = "start: authentication failure connecting to $this->server";
	    return false;
	}
	elseif (!preg_match("/OK/",$said)) {
	    $this->close();
	    $this->errstr = "start: bad authentication response from $this->server: $said";
	    return false;
	}

	return true;
    }

    /*
     * end the session. logout and close the socket.
     */
    function close () {

	if (!$this->socket) {
	    return true;
	}
	fputs($this->socket,"LOGOUT\r\n");
	$rc = fclose ($this->socket);
	if ($rc != 1) {
	    $this->errstr = "close: failed closing socket to $this->server";
	    return false; 
	}
	return true;
    }

    /*
     * read a line from socket.
     * line might end in either a newline, or a CRLF, in which case 
     * we will need to retrieve the newline also.
     */
    function read () {

        $buffer = '';

        if (!$this->socket)
            return $buffer;

        socket_set_timeout($this->socket,$this->socket_timeout);
        socket_set_blocking($this->socket,true);

        /* read one character at a time and add to $buffer. */
	while (!feof($this->socket)) {
	    $char = fread($this->socket,1);

            $status = socket_get_status($this->socket);
            if ($status['timed_out'])
                return $buffer;

            /* return $buffer if we've reached end on line.
             * if line ends with CRLF, fetch the \n also. */
	    if (($char == "\n") || ($char == "\r")) {
                if ($char == "\r")
                    fread($this->socket,1);
		return $buffer;
	    }
	    $buffer .= $char;
	}
	return $buffer;
    }


    /*
     * return an array containing the list of sieve scripts on the 
     * server belonging to the current user.
     */
    function listscripts () {
	if (!$this->socket) {
            $this->errstr = "listscripts: no connection open to $this->server";
            return false;
        }

	$scripts = array();

	fputs($this->socket,"LISTSCRIPTS\r\n");

	$said = $this->read();
	while (!preg_match("/^OK/",$said) && !preg_match("/^NO/",$said)) {

	    // Cyrus v1 script lines look like '"script*"' with the 
	    // asterisk denoting the active script. Cyrus v2 script 
	    // lines will look like '"script" ACTIVE' if active.

	    if (preg_match("/^\"(.+)\"\s*(.+)*$/m",$said,$bits)) {
		if (preg_match("/\*$/",$bits[1])){
		    $bits[1] = preg_replace("/\*$/","",$bits[1]);
		    $this->activescript = $bits[1];
		}
		if (isset($bits[2]) && $bits[2] == 'ACTIVE')
		    $this->activescript = $bits[1];
		array_push($scripts,$bits[1]);
	    }
	    $said = $this->read();
	}

	if (preg_match("/^OK/",$said)) {
	    $this->scriptlist = $scripts;
            return true;
        }
 
        $this->errstr = "listscripts: could not get list of scripts: $said";
        return false;
    }


    /*
     * return the contents of the sieve script $scriptfile, retrieved 
     * from the server, or false if the script does not exist.
     */
    function getscript ($scriptfile) {
	if (!isset($scriptfile)) {
	    $this->errstr = "getscript: no script file specified";
	    return false;
	}
	if (!$this->socket) {
	    $this->errstr = "getscript: no connection open to $this->server";
	    return false;
	}

        $script = '';

	fputs($this->socket,"GETSCRIPT \"$scriptfile\"\r\n");

	$said = $this->read();
	while ((!preg_match("/^OK/",$said)) && (!preg_match("/^NO/",$said))) {
	    // replace newlines which read() removed
	    if (!preg_match("/\n$/",$said)) $said .= "\n";
	    $script .= $said;
	    $said = $this->read();
	}

	if (preg_match("/^OK/",$said)) {
	    if ($script == '') {
		$this->errstr = "getscript: zero length script";
		return false;
	    }
	    return $script;
	}

	$this->errstr = "getscript: could not get script $scriptfile: $said";
	return false;
    }


    /*
     * set $scriptfile as the active sieve script.
     */
    function activatescript ($scriptfile) {
	if (!isset($scriptfile)) {
            $this->errstr = "activatescript: no script file specified";
            return false;
        }

        if (!$this->socket) {
            $this->errstr = "activatescript: no connection open to $this->server";
            return false;
        }

	fputs($this->socket,"SETACTIVE \"$scriptfile\"\r\n");

	$said = $this->read();

	if (preg_match("/^OK/",$said)) {
            return true;
        }

	$this->errstr = "activatescript: could not activate script $scriptfile: $said";
        return false;
    }


   /*
    * check that the user will not exceed their storage quota
    * by uploading script $scriptname of size $size bytes.
    */
    function havespace ($scriptname, $size) {
        if (!$this->socket) {
            $this->errstr = 'havespace: no connection open to ' . $this->server;
            return false;
        }
        if (!$scriptname){
            $this->errstr = 'havespace: no script name specified';
            return false;
        }
        if (!$size){
            $this->errstr = 'havespace: script size not specified';
            return false;
        }

        fputs($this->socket,"HAVESPACE \"$scriptname\" $size\r\n");

        $said = $this->read();

        if (preg_match("/^OK/",$said)) {
            return true;
        }

        $this->errstr = "havespace: $said";
        return false;
    }


    /*
     * upload the script $script to the server. save it as $scriptfile.
     * the script will not be active. call activatescript() to do this.
     */
    function putscript ($scriptfile,$script) {
	if (!isset($scriptfile)) {
            $this->errstr = "putscript: no script file specified";
            return false;
        }
	if (!isset($script)) {
            $this->errstr = "putscript: no script specified";
            return false;
        }
	if (!$this->socket) {
            $this->errstr = "putscript: no connection open to $this->server";
            return false;
        }

	$len = strlen($script);
	fputs($this->socket,"PUTSCRIPT \"$scriptfile\" \{$len+}\r\n");
	fputs($this->socket,"$script\r\n");

	$said = '';
	while ($said == '') {
	    $said = $this->read();
	}
 
        if (preg_match("/^OK/",$said)) {
	    return true;
	}

        $this->errstr = "putscript: could not put script $scriptfile: $said";
        return false;
    }


   /*
    * delete the script $scriptname.
    */
    function deletescript ($scriptname) {
        if (!$this->socket) {
            $this->errstr = "deletescript: no connection open to $this->server";
            return false;
        }
        if (!$scriptname){
            $this->errstr = 'deletescript: no script name specified';
            return false;
        }

        fputs($this->socket,"DELETESCRIPT \"$scriptname\"\r\n");

        $said = $this->read();

        if (preg_match("/^OK/",$said)) {
            return true;
        }

        $this->errstr = "deletescript: could not delete script '$scriptname': $said";
        return false;
    }


}
// end SieveSession class


?>
