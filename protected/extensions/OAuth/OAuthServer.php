<?php


class OAuthServer {
    protected $timestamp_threshold = 300; // in seconds, five minutes

    function __construct() {

    }

    public function fetchRequestToken() {

    }

    /**
    * process an access_token request
    * returns the access token on success
    */
    public function fetchAccessToken($consumer_id, $uid) {
        return [];
    }
}

class EOAuthServerException extends CException {
}
