<?php
namespace Phpingguo\System\Request;

use Phpingguo\System\Core\Config;
use Phpingguo\System\Core\AuraDIWrapper;
use Phpingguo\System\Enums\EnumFullName;
use Phpingguo\System\Exceptions\SecurityViolationException;
use Phpingguo\System\Exceptions\ValidationErrorException;
use Phpingguo\System\Exts\Lib\Common\Arrays;
use Phpingguo\System\Exts\Lib\EnumClassGenerator as EnumClassGen;
use Phpingguo\System\Exts\Lib\Type\Generics\GenericList;
use Phpingguo\System\Validator\IValidator;
use Phpingguo\System\Validator\Options;

/**
 * クライアントからサーバーへのリクエストしたデータを保持するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class Request
{
    // ---------------------------------------------------------------------------------------------
    // private fields
    // ---------------------------------------------------------------------------------------------
    private $req_data  = null;
    private $validated = [];
    
    // ---------------------------------------------------------------------------------------------
    // public class methods
    // ---------------------------------------------------------------------------------------------
    /**
     * Request クラスのインスタンスを取得します。
     * 
     * @param Boolean $reanalyze [初期値=false]	リクエストデータの再解析を行うかどうか
     * 
     * @return Request 初回呼び出し時は新しいインスタンスを、それ以降の時は生成済みのインスタンスを返します。
     */
    public static function getInstance($reanalyze = false)
    {
        $instance = AuraDIWrapper::init()->get(__CLASS__);
        
        if (empty($instance->req_data) || $reanalyze === true) {
            $instance->setRequestData(RequestParser::getInstance($reanalyze)->get());
        }
        
        return $instance;
    }
    
    // ---------------------------------------------------------------------------------------------
    // public member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * クライアントが要求した情報を取得します。
     * 
     * @return RequestData クライアントが要求した情報を返します。
     */
    public function getRequestData()
    {
        return $this->req_data;
    }
    
    /**
     * クライアントが要求したAPIのバージョン番号を取得します。
     * 
     * @return UnsignedFloat クライアントが要求したAPIのバージョン番号を返します。
     */
    public function getApiVersion()
    {
        return $this->getRequestData()->getApiVersion();
    }
    
    /**
     * クライアントが要求したモジュールの名前を取得します。
     * 
     * @return String クライアントが要求したモジュールの名前を返します。
     */
    public function getModuleName()
    {
        return $this->getRequestData()->getModuleName();
    }
    
    /**
     * クライアントが要求したシーンの名前を取得します。
     * 
     * @return String クライアントが要求したシーンの名前を返します。
     */
    public function getSceneName()
    {
        return $this->getRequestData()->getSceneName();
    }
    
    /**
     * クライアントが要求したシーンに渡すパラメータの一覧を取得します。
     * 
     * @return Array クライアントが要求したシーンに渡すパラメータの一覧を返します。
     */
    public function getParameters()
    {
        return $this->getRequestData()->getParameters();
    }
    
    /**
     * クライアントが要求したシーンに渡すパラメータ一覧を新しく設定します。
     * 
     * @param Array $params	シーンに渡す新しいパラメータの一覧
     */
    public function setParameters(array $params)
    {
        $this->getRequestData()->setParameters($params);
    }
    
    /**
     * シーンへ渡すパラメータ一覧に指定した名前のパラメータが存在するかどうかを調べます。
     * 
     * @param String $name	パラメータ一覧に存在するかどうかを調べるパラメータの名前
     * 
     * @return Boolean パラメータ一覧に存在する場合は true を、それ以外の場合は false を返します。
     */
    public function isExistParam($name)
    {
        return $this->getRequestData()->isExistParameter($name);
    }
    
    /**
     * シーンへ渡すパラメータ一覧から指定した名前の値を取得します。
     * 
     * @param Variable|String $type	値を取得するパラメータの型のインスタンスまたは名前
     * @param String $name			値を取得するパラメータの名前
     * 
     * @throws SecurityViolationException	バリデーションを通過していないパラメータを取得しようとした場合
     * （※"sys.security.validation_forced"が有効の時のみ）
     * 
     * @return mixed シーンへ渡すパラメータ一覧から指定した名前の値を返します。
     */
    public function getParameter($type, $name)
    {
        $param_value = $this->getRequestData()->getParameter($name);
        
        if (Config::get('sys.security.validation_forced', true) && in_array($name, $this->validated) === false) {
            throw new SecurityViolationException('Access to parameter value that has not passed validation.');
        }
        
        return is_null($param_value) ? null : $this->createParamValue($type, $param_value);
    }
    
    /**
     * シーンへ渡すパラメータに指定した名前の値を設定します。
     * 
     * @param Variable|String $type	値を設定するパラメータの型のインスタンスまたは名前
     * @param String $name			値を設定するパラメータの名前
     * @param mixed $value			パラメータに新しく設定する値
     */
    public function setParameter($type, $name, $value)
    {
        $this->getRequestData()->setParameter($name, $this->createParamValue($type, $value));
    }
    
    /**
     * シーンへ渡すパラメータの値を検証します。
     * 
     * @param Validator|String $type	実行する検証の種類
     * @param String $name				検証の対象となるパラメータの名前
     * @param Options $options			検証時に利用されるオプション設定
     * 
     * @throws SecurityViolationException	存在しないパラメータを検証しようとした場合
     * 
     * @return Boolean|Array 検証に成功した時は true を、失敗した時はその理由を含む配列を、
     * それ以外の場合は false を返します。
     */
    public function validate($type, $name, Options $options)
    {
        list(, $obj_validator) = EnumClassGen::done(EnumFullName::VALIDATOR, $type);
        
        $param_value = $this->getRequestData()->getParameter($name);
        
        if (is_null($param_value)) {
            throw new SecurityViolationException('Access to nil parameter value.');
        }
        
        $exec_method = is_array($param_value) ? 'doArrayValidate' : 'execValidation';
        
        return $this->$exec_method($obj_validator, $name, $param_value, $options);
    }
    
    /**
     * シーンへ渡すパラメータの値を一括で複数検証します。
     * 
     * @param Validator|String $type	検証の種類
     * @param String $name					検証の対象となるパラメータの名前
     * @param Options $options				検証時に利用されるオプション設定
     * 
     * @throws InvalidArgumentException	メソッドに渡した引数の内容が正しくない場合
     * 
     * @return Boolean|Array 検証した全てのパラメータの値が正しいものであれば true を、
     * エラー情報がある場合はその配列を、エラー情報が無く nullable 許可時に検証した値のうち
     * 少なくとも一つが null 該当文字である場合は false を返します。
     */
    public function multipleValidate()
    {
        $args_count = func_num_args();
        
        // 引数が 0個、または、引数が3の倍数でない時は可変長引数の内容が正しくないため例外をスローする
        if ($args_count === 0 || $args_count % 3 !== 0) {
            throw new \InvalidArgumentException();
        }
        
        $error_lists   = [];
        $args_list     = func_get_args();
        $result_status = false;
        
        for ($i = 0; $i < $args_count; $i += 3) {
            $result = $this->validate($args_list[$i], $args_list[$i + 1], $args_list[$i + 2]);
            
            if (Arrays::mergeWhen(is_array($result), $error_lists, $result) === false) {
                $result_status |= $result;
            }
        }
        
        return (empty($error_lists) === false) ? $error_lists : (bool)$result_status;
    }
    
    // ---------------------------------------------------------------------------------------------
    // private member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * クライアントが要求したリクエスト情報を設定します。
     * 
     * @param RequestData $data	クライアントが要求したリクエスト情報
     */
    private function setRequestData(RequestData $data)
    {
        $this->req_data = $data;
    }
    
    /**
     * パラメータの適正な値を生成します。
     * 
     * @param Variable|String $type	適正値を生成するパラメータの型のインスタンスまたは名前
     * @param mixed $value			適正値を生成するパラメータの名前
     * 
     * @return mixed パラメータの適正な値を返します。
     */
    private function createParamValue($type, $value)
    {
        list($obj_type, $obj_value) = EnumClassGen::done(EnumFullName::VARIABLE, $type);
        
        if (is_array($value)) {
            return (new GenericList($obj_type, $value))->toArray();
        }
        
        return $obj_value->getValue($value);
    }
    
    /**
     * パラメータの値検証を実行します。
     * 
     * @param IValidator $obj_validator	検証を実行するクラスのインスタンス
     * @param String $param_name		検証対象のパラメータの名前
     * @param String $param_value		検証対象のパラメータの値
     * @param Options $options			検証実行オプションを保持するインスタンス
     * 
     * @return Boolean|Array 検証に成功した時は true を、失敗した時はその理由を含む配列を、
     * それ以外の場合は false を返します。
     */
    private function execValidation(IValidator $obj_validator, $param_name, $param_value, Options $options)
    {
        $result = false;
        
        try {
            if (true === ($result = $obj_validator->validate($param_value, $options))) {
                Arrays::addWhen(in_array($param_name, $this->validated) === false, $this->validated, $param_name);
            }
        } catch (ValidationErrorException $e) {
            $result = [ $param_name => $e->getErrorTypes() ];
        }
        
        return $result;
    }
    
    /**
     * 配列を値として持つパラメータの検証を行います。
     * 
     * @param IValidator $obj_validator	検証を実行するクラスのインスタンス
     * @param String $name				検証対象のパラメータの名前
     * @param Array $param_value		検証対象のパラメータの値
     * @param Options $options			検証実行オプションを保持するインスタンス
     * 
     * @return Boolean|Array 検証に成功した時は true を、失敗した時はその理由を含む配列を、
     * それ以外の場合は false を返します。
     */
    private function doArrayValidate(IValidator $obj_validator, $name, array $param_value, Options $options)
    {
        $result = false;
        $errors = [];
        
        foreach ($param_value as $inner_param) {
            if (false === ($result = $this->execValidation($obj_validator, $name, $inner_param, $options))) {
                return false;
            }
            
            Arrays::addWhen(is_array($result), $errors, $result);
        }
        
        return empty($errors) ? $result : $errors;
    }
}
