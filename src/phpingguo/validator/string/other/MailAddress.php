<?php
namespace Phpingguo\System\Validator\String\Other;

use Phpingguo\System\Enums\ValidationError;
use Phpingguo\System\Exceptions\RfcMailViolationException;
use Phpingguo\System\Validator\String\BaseString;
use Phpingguo\System\Validator\Options;

/**
 * RFC準拠のメールアドレスかどうかを検証するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class MailAddress extends BaseString
{
    /**
     * @see BaseString::checkInvalidFormat()
     */
    protected function checkInvalidFormat($value, Options $options)
    {
        $error_result = parent::checkInvalidFormat($value, $options);
        
        return is_null($error_result) ? $this->checkRfcMailViolation($value) : $error_result;
    }
    
    /**
     * RFCのメールアドレス表記に違反しているかどうかをチェックします。
     * 
     * @param String $mail_address	違反チェックを行うメールアドレス
     * 
     * @throws RfcMailViolationException	RFCに違反するメールアドレスの場合
     * 
     * @return ValidationError|null RFCに違反していなかった場合は null を、それ以外はエラー理由を返します。
     */
    private function checkRfcMailViolation($mail_address)
    {
        if (false === ($at_index = strrpos($mail_address, '@'))) {
            return ValidationError::FORMAT();
        }
        
        // ローカル部の先頭または最後に「.」がある、または、二つ以上の連続した「.」が使われている、
        // または、「"」で囲まれていないときに「()<>[]:;@,」が使用されている、
        // または、ローカル部が64文字以上、ドメイン部が255文字以上、全体が256文字以上の場合
        if ($this->isValidLength($mail_address, $at_index) === false ||
            preg_match($this->getMatchPattern(), $mail_address) !== 1
        ) {
            throw new RfcMailViolationException();
        }
        
        return null;
    }
    
    /**
     * メールアドレスの各部分の長さがRFCに準拠しているかどうかを調べます。
     * 
     * @param String $mail_address	長さのチェックを行うメールアドレス
     * @param Index $at_index		ローカル部とドメイン部を区切る「@」のインデックス番号
     * 
     * @return Boolean RFCに準拠した長さである場合は true を、それ以外の場合は false を返します。
     */
    private function isValidLength($mail_address, $at_index)
    {
        $localpart_len = strlen(substr($mail_address, 0, $at_index));
        $domain_len    = strlen(substr($mail_address, $at_index));
        $address_len   = strlen($mail_address);
        
        return ($localpart_len <= 64 && $domain_len <= 255 && $address_len <= 256);
    }
    
    /**
     * メールアドレスの検索パターン文字列を取得します。
     * 
     * @return String メールアドレスの検索パターン文字列を返します。
     */
    private function getMatchPattern()
    {
        $pattern = [
            '[^(\040)<>@,;:".\\\\\[\]\000-\037\x80-\xff]',
            '"[^\\\\\x80-\xff\n\015"]*(?:\\\\[^\x80-\xff][^\\\\\x80-\xff\n\015"]*)*"',
            '(?:[^\\\\\x80-\xff\n\015\[\]]|\\\\[^\x80-\xff])'
        ];
        
        $get_sub_pattern = function ($is_domain = true) use ($pattern) {
            $get_part_pattern = function () use ($pattern, $is_domain) {
                return '(?:' . $pattern[0] . '+(?!' . $pattern[0] . ')|' . (
                    ($is_domain === false) ? $pattern[1] : ('\[' . $pattern[2] . '*\]')) . ')';
            };
            
            $str_pattern = $get_part_pattern();
            
            return $str_pattern . '(?:\.' . $str_pattern . ')*';
        };
        
        return '/^' . $get_sub_pattern(false) . '@' . $get_sub_pattern() . '$/';
    }
}
