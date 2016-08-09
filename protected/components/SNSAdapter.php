<?php

abstract class SNSAdapter {
  abstract function getFeeds($from, $to, $limit);

  abstract function postFeed($msg);

  abstract function getFriends();

  static function create($name, $uid) {
    $class = $name.'Adapter';
    $ret = new $class($uid);
    return $ret;
  }
}

// $weibo = SNSAdapter::create('Weibo', $uid);
//  if($weibo->fetchAccessToken($uid)) ok;


class WeiboAdapter extends SNSAdapter {
  protected $client;
  protected $uid;
  protected $accessToken;

  const SOURCE_URL = "http://app.weibo.com/t/feed/4TfdGF";

  function __construct($uid) {
    Yii::import('ext.Weibo.SaeTClientV2',true);
    $this->accessToken = self::fetchAccessToken($uid);
    $this->client = new SaeTClientV2(WB_AKEY, WB_SKEY, $this->accessToken);
    $this->client->oauth->remote_ip = isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
  }

  static function fetchAccessToken($uid) {
    $bind = UserBind::model()->findByAttributes(['uid'=>$uid, 'source'=>'weibo']);
    if (empty($bind)) {
      return null;
    }
    return $bind['accessToken'];
  }

  function getFeeds($from, $to, $limit) {
    $page = 1;
    $limit = 200;
    $sinceId = null;
    $max_id = 0;
    $feature = 1;

    //$result = $this->client->user_timeline_by_id($this->client->get_uid(), $feature, $limit, $from, $to, 0, 1, 0);
    $result = $this->client->user_timeline_by_id($this->client->get_uid(), $page, $limit, $sinceId, $max_id, $feature, 0, 0);
    $ret = [];
    foreach ($result['statuses'] as $status) {
      if ($status['visible']['type'] != 0) {
        continue;
      }
      $ret[]= [
        "id"        => $status['idstr'],
        "text"      => $status['text'],
        "createdAt" => strtotime($status['created_at']),
        "xl"        => strpos($status['source'], self::SOURCE_URL) !== false,
      ];
    }
    return $ret;
  }

  function getFriends() {
    $pagesize = 100;
    $ret = [];
    for ($page = 1; $page < 100; $page++) {
      $bilateralList = $this->client->bilateral($weiboUid, $page, $pagesize);
      foreach ($bilateralList['users'] as $value) {
        $ret[] = [
          'id'        => $value['id'],
          'name'      => $value['name'],
          'sign'      => $value['description'],
          'avatarUrl' => $value['profile_image_url'],
          'source'    => 'weibo'
        ];
      }
      if (count($bilateralList['users']) < $pagesize) {
        break;
      }
    }
    return $ret;
  }

  function postFeed($text, $imageUrl = '') {
    if ($imageUrl != '') {
      $this->client->upload($text, $imageUrl);
    } else {
      $this->client->update($text);
    }
  }
}


class InstagramAdapter extends SNSAdapter {
  protected $uid;
  protected $accessToken;

  function __construct($uid) {
    $this->accessToken = self::fetchAccessToken($uid);
  }

  static function fetchAccessToken($uid) {
    $bind = UserBind::model()->findByAttributes(['uid'=>$uid, 'source'=>UserBind::APP_INSTAGRAM]);
    if (empty($bind)) {
      return null;
    }
    return $bind['accessToken'];
  }

  function getFeeds($from, $to, $limit) {
    // get config
    // get data

    $ret = [];
    foreach ($result['statuses'] as $status) {
      if ($status['visible']['type'] != 0) {
        continue;
      }
      $ret[]= [
          "id"        => $status['idstr'],
      ];
    }
    return $ret;
  }

  function getFriends() {
  }

  function postFeed($text, $imageUrl = '') {
    if ($imageUrl != '') {
      $this->client->upload($text, $imageUrl);
    } else {
      $this->client->update($text);
    }
  }
}

