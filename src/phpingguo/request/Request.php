<?php
namespace Phpingguo\System\Request;

use Phpingguo\ApricotLib\Common\Arrays;
use Phpingguo\ApricotLib\Enums\LibEnumName;
use Phpingguo\ApricotLib\Type\Enum\EnumClassGenerator as EnumClassGen;
use Phpingguo\ApricotLib\Type\Generics\GenericList;
use Phpingguo\BananaValidator\Enums\EnumFullName;
use Phpingguo\BananaValidator\IValidator;
use Phpingguo\BananaValidator\Options;
use Phpingguo\BananaValidator\ValidationErrorException;
use Phpingguo\System\Core\Config;
use Phpingguo\System\Core\Supervisor;

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
     * @param Boolean $reanalyze [初期値=false] リクエストデータの再解析を行うかどうか
     * 
     * @return Request 初回呼び出し時は新しいインスタンス。それ以降の時は生成済みのインスタンス。
     */
    public static function getInstance($reanalyze = false)
    {
        /** @var Request $instance */
        $instance = Supervisor::getDiContainer('system')->get(__CLASS__);
        
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
     * @return RequestData クライアントが要求した情報
     */
    public function getRequestData()
    {
        return $this->req_data;
    }
    
    /**
     * クライアントが要求したAPIのバージョン番号を取得します。
     * 
     * @return \Phpingguo\ApricotLib\Type\Float\UnsignedFloat APIのバージョン番号
     */
    public function getApiVersion()
    {
        return $this->getRequestData()->getApiVersion();
    }
    
    /**
     * クライアントが要求したモジュールの名前を取得します。
     * 
     * @return String クライアントが要求したモジュールの名前
     */
    public function getModuleName()
    {
        return $this->getRequestData()->getModuleName();
    }
    
    /**
     * クライアントが要求したシーンの名前を取得します。
     * 
     * @return String クライアントが要求したシーンの名前
     */
    public function getSceneName()
    {
        return $this->getRequestData()->getSceneName();
    }
    
    /**
     * クライアントが要求したシーンに渡すパラメータの一覧を取得します。
     * 
     * @return Array クライアントが要求したシーンに渡すパラメータの一覧
     */
    public function getParameters()
    {
        return $this->getRequestData()->getParameters();
    }
    
    /**
     * クライアントが要求したシーンに渡すパラメータ一覧を新しく設定します。
     * 
     * @param Array $params シーンに渡す新しいパラメータの一覧
     */
    public function setParameters(array $params)
    {
        $this->getRequestData()->setParameters($params);
    }
    
    /**
     * シーンへ渡すパラメータ一覧に指定した名前のパラメータが存在するかどうかを調べます。
     * 
     * @param String $name パラメータ一覧に存在するかどうかを調べるパラメータの名前
     *
     * @return Boolean パラメータ一覧に存在する場合は true。それ以外の場合は false。
     */
    public function isExistParam($name)
    {
        return $this->getRequestData()->isExistParameter($name);
    }
    
    /**
     * シーンへ渡すパラメータ一覧から指定した名前の値を取得します。
     * 
     * @param \Phpingguo\ApricotLib\Enums\Variable|String $type 値を取得するパラメータの型のインスタンスまたは名前
     * @param String $name                                      値を取得するパラメータの名前
     * 
     * @throws \LogicException バリデーションを通過していないパラメータを取得しようとした場合
     * （※"sys.security.validation_forced"が有効の時のみ）
     * 
     * @return mixed シーンへ渡すパラメータ一覧から指定した名前の値
     */
    public function getParameter($type, $name)
    {
        $param_value = $this->getRequestData()->getParameter($name);
        
        if (Config::get('sys.security.validation_forced', true) && in_array($name, $this->validated) === false) {
            throw new \LogicException('The parameter value that has not passed validation is not usable.');
        }
        
        return is_null($param_value) ? null : $this->createParamValue($type, $param_value);
    }
    
    /**
     * シーンへ渡すパラメータに指定した名前の値を設定します。
     * 
     * @param \Phpingguo\ApricotLib\Enums\Variable|String $type パラメータの型のインスタンスまたは名前
     * @param String $name                                      パラメータの名前
     * @param mixed $value                                      パラメータに新しく設定する値
     */
    public function setParameter($type, $name, $value)
    {
        $this->getRequestData()->setParameter($name, $this->createParamValue($type, $value));
    }
    
    /**
     * シーンへ渡すパラメータの値を検証します。
     * 
     * @param \Phpingguo\BananaValidator\Enums\Validator|String $type 実行する検証の種類
     * @param String $name                                            検証の対象となるパラメータの名前
     * @param Options $options                                        検証時に利用されるオプション設定
     * 
     * @throws \RuntimeException 存在しないパラメータを検証しようとした場合
     * 
     * @return Boolean|Array 検証成功時は true。失敗時はその理由を含む配列。それ以外の場合は false。
     */
    public function validate($type, $name, Options $options)
    {
        list(, $obj_validator) = EnumClassGen::done(EnumFullName::VALIDATOR, $type);
        
        $param_value = $this->getRequestData()->getParameter($name);
        
        if (is_null($param_value)) {
            throw new \RuntimeException('A parameter that attempting to validate is not exist.');
        }
        
        return call_user_func(
            [ $this, is_array($param_value) ? 'doArrayValidate' : 'execValidation' ],
            $obj_validator,
            $name,
            $param_value,
            $options
        );
    }
    
    /**
     * シーンへ渡すパラメータの値を一括で複数検証します。
     * 
     * @param \Phpingguo\BananaValidator\Enums\Validator|String $type 検証の種類
     * @param String $name                                            検証の対象となるパラメータの名前
     * @param Options $options                                        検証時に利用されるオプション設定
     * 
     * @throws \InvalidArgumentException メソッドに渡した引数の内容が正しくない場合
     *
     * @return Boolean|Array 検証した全てのパラメータの値が正しいものであれば true。
     * エラーがあった場合はその配列。エラーが無く nullable 許可時に検証した値のうち
     * 少なくとも一つが null 該当文字である場合は false。
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
     * @param RequestData $data クライアントが要求したリクエスト情報
     */
    private function setRequestData(RequestData $data)
    {
        $this->req_data = $data;
    }
    
    /**
     * パラメータの適正な値を生成します。
     * 
     * @param \Phpingguo\ApricotLib\Enums\Variable|String $type パラメータの型のインスタンスまたは名前
     * @param mixed $value                                      適正値を生成するパラメータの名前
     * 
     * @return mixed 生成したパラメータの適正値
     */
    private function createParamValue($type, $value)
    {
        /** @var \Phpingguo\ApricotLib\Type\Enum\Enum $obj_value */
        list($obj_type, $obj_value) = EnumClassGen::done(LibEnumName::VARIABLE, $type);
        
        if (is_array($value)) {
            return (new GenericList($obj_type, $value))->toArray();
        }
        
        return $obj_value->getValue($value);
    }
    
    /**
     * パラメータの値検証を実行します。
     * 
     * @param IValidator $obj_validator 検証を実行するクラスのインスタンス
     * @param String $param_name        検証対象のパラメータの名前
     * @param String $param_value       検証対象のパラメータの値
     * @param Options $options          検証実行オプションを保持するインスタンス
     * 
     * @return Boolean|Array 検証成功時は true。失敗時はその理由を含む配列。それ以外の場合は false。
     */
    private function execValidation(IValidator $obj_validator, $param_name, $param_value, Options $options)
    {
        try {
            if (true === ($result = $obj_validator->validate($param_value, $options))) {
                Arrays::addWhen(in_array($param_name, $this->validated) === false, $this->validated, $param_name);
            }
        } catch (ValidationErrorException $e) {
            $result = [ $param_name => $e->getErrorLists() ];
        }
        
        return $result;
    }
    
    /**
     * 配列を値として持つパラメータの検証を行います。
     * 
     * @param IValidator $obj_validator 検証を実行するクラスのインスタンス
     * @param String $name              検証対象のパラメータの名前
     * @param Array $param_value        検証対象のパラメータの値
     * @param Options $options          検証実行オプションを保持するインスタンス
     * 
     * @return Boolean|Array 検証成功時は true。失敗時はその理由を含む配列。それ以外の場合は false。
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
