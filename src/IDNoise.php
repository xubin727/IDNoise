<?php
namespace Xubin\IDNoise;

use Google\CRC32\CRC32;

class IDNoise {
    
    /**
     *
《玄鸟》：天命玄鸟，降|
而生商，宅殷土芒芒。古帝|
命武汤，正域彼四方。方命|
厥后，奄有九有。商之先后|
，受命不殆，在武丁孙子。武|
丁孙子，武王靡不胜。龙旂|
十乘，大糦是承。邦畿千里|
，维民所止，肇域彼四海。四|
海来假，来假祁祁。景员维|
河，殷受命咸宜，百禄是何。
     * 共99+23=122字
     * @var array
     */
    protected static $dictionary = array(
        0 => array(2,5,5,2,2, 4,8,5,5,8),
        1 => array(6,5,11,6,10, 3,6,6,5,9),
        2 => array(8,8,6,5,11, 8,5,4,4,8),
        3 => array(12,6,8,6,2, 6,11,3,6,6),
        4 => array(8,8,4,9,6, 8,2,6,3,8),
        5 => array(2,6,3,8,4, 19,4,9,5,10),
        6 => array(2,10,3,18,9, 8,6,15,3,7),
        7 => array(11,5,8,4,14, 11,8,5,10,5),
        8 => array(10,7,11,7,11, 6,6,12,7,11),
        9 => array(8,10,8,8,9, 8,6,13,9,7),
    );
    
    
    /**
     * 对数字进行干扰编码
     * @param number $number
     * @param string $secret
     * @return string
     */
    public static function encode($number, $secret='')
    {
        $len = strlen($number);
        $last = substr($number, -1);
        $suff = 99 - ( $len+$last);
        $crcLastChar = self::_crcLastChar($number, $secret);
        
        $numberAry = str_split($number);
        
        $rtStr = $crcLastChar . $suff . $last . $len;
        for ($n=0; $n<=max(9, $len-1); $n++) {
            $dict = self::$dictionary[$last];
            if (isset($numberAry[$n])) {
                $num = $numberAry[$n];
                $rtStr .= $num . $dict[$num];
            } else {
                $dict = ( 9==$last ? self::$dictionary[0] : self::$dictionary[$last+1] );
                $rtStr .= $dict[$n];
            }
        }
        
        return $rtStr;
    }
    
    
    /**
     * 对一个已干扰数字进行解码
     * @param string $string
     * @param string $secret
     * @return number
     */
    public static function decode($string, $secret='')
    {
        $crcLastChar = substr($string, 0, 1);
        $suff = substr($string, 1, 2);
        $last = substr($string, 3, 1);
        $len = 99 - $last - $suff;
        $start = $crcLastChar . $suff . $last . $len;
        
        $rtStr = '';
        $i = strlen($start);
        for ($n = 1; $n <= $len; $n++) {
            $nStr = substr($string, $i, 1);
            $rtStr .= $nStr;
            $i += strlen(self::$dictionary[$last][$nStr]) + 1;
        }
//         var_dump($rtStr, self::_crcLastChar($rtStr, $secret), $crcLastChar);
        if (self::_crcLastChar($rtStr, $secret) != $crcLastChar) {
            exit; // 验证不通过的字符串ID是人造的，不可用，不输出任何信息直接退出
        }
        
        return $rtStr;
    }
    
    /**
     * 返回CRC后的字符串的最后一个字符
     * @param string $number
     * @param string $secret
     * @return string
     */
    protected static function _crcLastChar($number, $secret)
    {
        $crc = CRC32::create(CRC32::CASTAGNOLI);
        $crc->update($number . $secret);
        $crcLastChar = strtoupper(substr($crc->hash(), -1));
        
        return $crcLastChar;
    }
    
}


