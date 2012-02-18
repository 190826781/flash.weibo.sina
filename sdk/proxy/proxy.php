<?php
class SDKProxy {
    var $uri;
    var $method;
    var $url;
    const OPEN_API='https://api.weibo.com/';

    public function run() {
        $this->uri = $_GET['uri'];
        $this->method =strtolower($_GET['method']);
        $this->url=self::OPEN_API.$this->uri.'.json';
        $param=$this->getParamStr($_POST);
        if($this->method=='get'){
            $this->url.='?'.$param;
            $result=$this->apiGet($this->url);
        }  else {
            if(!empty($_FILES)){
                $param=$_POST;
                $ext=end(explode(".",$_FILES['pic']['name'] ));
                $file=$_FILES['pic']['tmp_name'].'.'.$ext;
                move_uploaded_file($_FILES['pic']['tmp_name'],$file);
                $param['pic']='@'.$file;
            }
            $result=$this->apiPost($this->url,$param);
            if(file_exists($file))@unlink ($file);
        }
        echo $result;
    }
    
    public function getParamStr($params=array()){
        if(!is_array($params)||empty($params))return false;
        $str='';
        foreach ($params as $k=>$v){
              $str.=$k.'='.$v.'&';
        }
        return $str;
    }

    /**
     * 接口Get调用
     * @param	string	$url		URL地址
     * @param	integer	$timeout	超时时间
     * @return	all
     */
    public function apiGet($url, $timeout=1, $cookieStr='') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIE, $cookieStr);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * 接口Post调用
     * @param	string	$url		URL地址
     * @param	array	$data		post数据
     * @param	integer	$timeout	超时时间
     * @return	string
     */
    public function apiPost($url, $data, $timeout=1) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}

$proxy = new SDKProxy();
$proxy->run();
?>
