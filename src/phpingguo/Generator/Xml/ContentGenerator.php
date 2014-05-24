<?php
namespace Phpingguo\System\Generator\Xml;

use Phpingguo\ApricotLib\Common\Arrays;
use Phpingguo\ApricotLib\Common\String;
use Phpingguo\System\Generator\IGenerator;
use Phpingguo\System\Module\BaseModule;

/**
 * XMLのコンテンツデータを生成するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class ContentGenerator implements IGenerator
{
    // ---------------------------------------------------------------------------------------------
    // public member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * @see IGenerator::build
     */
    public function build(BaseModule $module, array $options)
    {
        $variables = $module->getModuleData()->getVariables();
        Arrays::removeWhen(true, $variables, 'response');
        
        $dom_doc = new \DomDocument('1.0', $module->getResponse()->getCharset()->getValue());
        $dom_doc->appendChild($this->getBodyElements($dom_doc, $variables, $options));
        
        return $dom_doc->saveXML();
    }

    // ---------------------------------------------------------------------------------------------
    // private member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * XMLの本体タグのインスタンスを取得します。
     * 
     * @param \DOMDocument $dom_doc DOMドキュメントのインスタンス
     * @param Array $variables      モジュールインスタンスが保持する変数の一覧
     * @param Array $options        実行時オプションデータ配列
     * 
     * @return \DOMElement XMLの本体タグのインスタンス
     */
    private function getBodyElements(\DOMDocument $dom_doc, array $variables, array $options)
    {
        $body_element = $dom_doc->createElement($options['SuperParentName']);
        
        Arrays::eachWalk(
            $variables,
            function ($value, $key) use ($dom_doc, &$body_element, $options) {
                $this->appendElementTo($body_element, $dom_doc, $options, $key, $value);
            }
        );
        
        return $body_element;
    }

    /**
     * 指定した要素に新しい要素を子として追加します。
     * 
     * @param \DOMElement $parent_elem 親となる要素のインスタンス
     * @param \DOMDocument $dom_doc    DOMドキュメントのインスタンス
     * @param Array $options           実行時オプションデータ配列
     * @param String|Integer $name     新しい要素の名前
     * @param mixed $value             新しい要素の値
     */
    private function appendElementTo(\DOMElement $parent_elem, \DOMDocument $dom_doc, array $options, $name, $value)
    {
        $element = null;
        
        if (String::isValid($value)) {
            $element = $dom_doc->createElement($name);
            $element->appendChild($dom_doc->createTextNode($value));
        } elseif (Arrays::isValid($value)) {
            $element = $dom_doc->createElement($name);
            $this->appendListElements($element, $dom_doc, $options, $value);
        }
        
        is_null($element) || $parent_elem->appendChild($element);
    }

    /**
     * 指定した要素に新しい要素を階層構造にして追加します。
     * 
     * @param \DOMElement $element  親となる要素のインスタンス
     * @param \DOMDocument $dom_doc DOMドキュメントのインスタンス
     * @param Array $options        実行時オプションデータ配列
     * @param Array $list           階層構造となる要素の元となるデータ配列
     */
    private function appendListElements(\DOMElement $element, \DOMDocument $dom_doc, array $options, array $list)
    {
        Arrays::eachWalk(
            $list,
            function ($value, $key) use (&$element, $dom_doc, $options) {
                $key_name = null;
                
                if (Arrays::isValid($value)) {
                    $key_name = String::isValid($key) ? $key : $options['DefaultListItemName'];
                } elseif (String::isValid($value)) {
                    $key_name = $key;
                }
                
                is_null($key_name) || $this->appendElementTo($element, $dom_doc, $options, $key_name, $value);
            }
        );
    }
}
