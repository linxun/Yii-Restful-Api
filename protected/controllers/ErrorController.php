<?php

class ErrorController extends Controller
{
    public function actionError() {
        $error = Yii::app()->errorHandler->error;
        $codes = [ 404 => APIException::OBJECT_NOT_FOUND ];
        $code = isset($codes[$error['code']]) ? $codes[$error['code']] : APIException::UNKNOWN;
        $ret = array('code' => $code, 'result'=>$error['message'], 'requestId'=>@$_SERVER['HTTP_REQUESTID']);
        $this->render('json',$ret);
    }
}

