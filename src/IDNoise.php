<?php
namespace Xubin\IDNoise;

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
     * @return string
     */
    public static function encode($number)
    {
        $len = strlen($number);
        $last = substr($number, -1);
        $suff = 99 - ( $len+$last);
        
        $numberAry = str_split($number);
        
        $rtStr = $suff . $last . $len;
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
     * @param string|number $string
     * @return number
     */
    public static function decode($string)
    {
        $suff = substr($string, 0, 2);
        $last = substr($string, 2, 1);
        $len = 99 - $last - $suff;
        $start = $suff . $last . $len;
        
        $rtStr = '';
        $i = strlen($start);
        for ($n = 1; $n <= $len; $n++) {
            $nStr = substr($string, $i, 1);
            $rtStr .= $nStr;
            $i += strlen(self::$dictionary[$last][$nStr]) + 1;
        }
        
        return $rtStr;
    }
    
}


