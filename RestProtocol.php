<?php
/**
 * User: ray
 * Date: 2019-05-23
 * Time: 12:24
 */

namespace protocol;


/**
 * 基础接口协议，做一些通用的验证规则
 * Class RestProtocol
 * @package protocal
 */
abstract class RestProtocol
{
    /**
     * 客户端传递过来的协议数据
     * @var array
     */
    protected $inputData;

    /**
     * 错误信息
     * @var array
     */
    protected $errors;

    protected $errorCode=0;

    public function __construct(array $inputData)
    {
        $this->inputData = $inputData;
        if($inputData){
            foreach ($inputData as $k=>$val){
                if(property_exists(get_class($this),$k)){
                    $this->$k = $val;
                }
            }
        }
        $this->init();
    }

    protected function init(){

    }

    /**
     * 执行获取数据操作
     * @return mixed
     */
    abstract public function run();

    /**
     * 校验协议规则
     * @return bool
     */
    public function validate()
    {
        $rules = $this->rules();
        foreach ($rules as $rule) {
            $function = "validate" . ucfirst($rule[1]);
            if(!$this->$function($rule)){
                return false;
            }
        }
        if(!empty($this->errors)){
            return false;
        }
        return true;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getFirstError()
    {
        if(!empty($this->errors)){
            foreach ($this->errors as $k=>$v){
                return $v;
            }
        }
        return '';
    }

    /**
     * 定义验证规则
     * {
    return [
    [['mchId', 'sn', 'supplierid', 'outTradeNo', 'goodsList'], 'required'],
    [['outTradeNo'], 'string', 'max' => 32],
    ];
    }
     * @return array
     */
    protected function rules(){
        return [
        ];
    }

    /**
     * 校验必需参数
     * @param $keyArr
     * @param $data
     * @return bool
     */
    protected function validateRequired($rule)
    {
        $keyArr = $rule[0];
        foreach ($keyArr as $key) {
            if (is_null($this->$key)) {
                $this->addError($key,$key.' Required');
                return false;
            }
        }
        return true;
    }

    /**
     * 添加错误信息
     * @param $key
     * @param $val
     */
    protected function addError($key,$val){
        $this->errors[$key] = $val;
    }

    /**
     * 校验字符长度
     * @param $rule
     * @return bool
     */
    protected function validateString($rule)
    {
        $keyArr = $rule[0];
        $maxLength = $rule['max'];
        foreach ($keyArr as $key) {
            if (!property_exists(get_class($this),$key) || strlen($this->$key) > $maxLength) {
                $this->addError($key,"{$key} illegal length");
                return false;
            }
        }
        return true;
    }

}