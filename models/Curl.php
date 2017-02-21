<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class Curl extends Model
{

	public static function model($className=__CLASS__)
	{
		$model = new $className;
	return $model;
	}
 
    public $_ch;

    // config from config.php
    public $options;

    // default config
    private $_config = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_AUTOREFERER    => true,         
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:5.0) Gecko/20110619 Firefox/5.0',
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        CURLOPT_DNS_USE_GLOBAL_CACHE => false,
        );

    private function _exec($url){

        self::setOption(CURLOPT_URL, $url);
        $c = curl_exec($this->_ch);
        if(!curl_errno($this->_ch))
            return $c;
        else{
            $error = curl_error($this->_ch);
            Yii::log($error.' url: '.$url, CLogger::LEVEL_WARNING);
            throw new CException($error);
            }

        return false;

    }

    public function get($url, $params = array()){
        $this->setOption(CURLOPT_HTTPGET, true);

        return $this->_exec($this->buildUrl($url, $params));
    }

    public function post($url, $data = array()){
        $this->setOption(CURLOPT_POST, true);
        $this->setOption(CURLOPT_POSTFIELDS, $data);

        return $this->_exec($url);
    }

    public function put($url, $data, $params = array()){        

        // write to memory/temp
        $f = fopen('php://temp', 'rw+');
        fwrite($f, $data);
        rewind($f);

        $this->setOption(CURLOPT_PUT, true);
        $this->setOption(CURLOPT_INFILE, $f);
        $this->setOption(CURLOPT_INFILESIZE, strlen($data));
        
        return $this->_exec($this->buildUrl($url, $params));
    }

    public function delete($url, $params = array()) {

        $this->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->setOption(CURLOPT_CUSTOMREQUEST, 'DELETE');

        return $this->_exec($this->buildUrl($url, $params));
    }

    public function buildUrl($url, $data = array()){
        $parsed = parse_url($url);
        isset($parsed['query'])?parse_str($parsed['query'],$parsed['query']):$parsed['query']=array();
        $params = isset($parsed['query'])?array_merge($parsed['query'], $data):$data;
        $parsed['query'] = ($params)?'?'.http_build_query($params):'';
        if(!isset($parsed['path']))
            $parsed['path']='/';

        return $parsed['scheme'].'://'.$parsed['host'].$parsed['path'].$parsed['query'];
    }
    
    public function setOptions($options = array()){
        curl_setopt_array( $this->_ch , $options);

        return $this;
    }

    public function setOption($option, $value){
        curl_setopt($this->_ch, $option, $value);

        return $this;
    }

    public function setHeaders($header = array())
    {
        if($this->_isAssoc($header)){
            $out = array();
            foreach($header as $k => $v){
                $out[] = $k .': '.$v;
            }
            $header = $out;
        }

        $this->setOption(CURLOPT_HTTPHEADER, $header);
        
        return $this;
    }


    private function _isAssoc($arr)
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public function getError()
    {
        return curl_error($this->_ch);
    }

    public function getInfo()
    {
        return curl_getinfo($this->_ch);
    }


 	public function __construct()
	{
			self::init();
	}
	
	public function close()
	{
		curl_close($this->_ch);	
	}
	
	// initialize curl
    public function init(){
        try{
            $this->_ch = curl_init();
            $options = is_array($this->options)? ($this->options + $this->_config):$this->_config;
            $this->setOptions($options);

            $ch = $this->_ch;
            
            // close curl on exit
            Yii::$app->on('afterRequest', function() use(&$ch){
                curl_close($ch);
            });
        }catch(Exception $e){
            throw new CException('Curl not installed');
        }
    }
}