<?php
/**
 * 破解验证码
 * date 2016-04-07
 * author  tangyouyou
 * email youyoudon@qq.com
 */

class valite{
	// 图片所占像素宽度
	private $word_width   = 8;
	// 图片所占像素高度
	private $word_higth   = 10;
	// 验证码第一个数字距离左边的距离
	private $offset_x	  = 10;
	// 验证码第一个数字距离顶部的距离
	private $offset_y	  = 6;
	// 验证码数字间距
	private $word_spacing = 1;

	// 验证码图片地址
	protected $imagePath;
	// 验证码生成的像素地址
	protected $dataArray = array();
	// 验证码图片大小
	protected $imageSize;
	protected $data;
	// 保存验证码图片二分值,数字部分取0，非数字部分取1
	protected $keys;
	protected $numberStringArray;

	/**
	 * 传入图片地址
	 * @param 
	 */
	public function __construct($imagePath){
		$this->imagePath = $imagePath;
		$this->keys = array(
			'0' => '11100111110000111001100100111100011111000011111000111100100111011100001111110111',  
	     	'1' => '11110111111001111000011111100111111001111110011111100111111001111110011110000001',
	     	'2' => '11000011110110010011110011111100111110011111001111100111110011111001111100000000',
			'3' => '10000011001110011111110011111001111000111110000111111100111111000011100110000011',
			'4' => '11111001111100011110000111001001100110010011100100000000111110011111100111111001',			
			'5' => '00000001001111110011111100100011000110011111110011111100001111001001100111000111',
			'6' => '11000011100111110011110100111111001000110011100100111101101111001001100111000011',
			'7' => '00000000111111001111110011111001111101111110011111001111100111110011111100111111',
			'8' => '11000011100110010011110010011001110000111011110100111100001111001001100111000011',
			'9' => '11000011100110010011110001111100100110001100010011111100101111001101100111100011',
    	);
	}

	/**
	 * 生成图片所占的像素二分值
	 * @return [type] [description]
	 */
	public function getCode(){
		// 根据图片地址获取图片资源
		$res = imagecreatefrompng($this->imagePath);
		// 获取图片尺寸
		$size = getimagesize($this->imagePath);
		$data = array();

		for ($i = 0; $i < $size[1]; ++$i) {
			for($j = 0; $j < $size[0]; ++ $j) {
				// 取得某像素的颜色索引值
				$rgb = imagecolorat($res, $j, $i);
				// 根据像素索引生成具体的值
				$rgbarray = imagecolorsforindex($res, $rgb);
				// 通过吸色笔判断得出障碍部分的区域为200像素左右
				if ($rgbarray['red'] < 200 || $rgbarray['green'] < 200 || $rgbarray['blue'] < 200) {
					$data[$i][$j] = 0;
				} else{
					$data[$i][$j] = 1;
				}
			}
		}
		$this->dataArray = $data;
		$this->imageSize = $size;
	}

	public function search(){
		$result = "";
		// 查找数字,验证码一共为5个数字
		$data = array("","","","","");
		for ($i = 0; $i < 5; ++$i) {
			// 获取起始的验证码距离左部距离
			$x = ($i * ($this->word_width + $this->word_spacing)) + $this->offset_x;
			// 获取起始的验证码距离定都距离
			$y = $this->offset_y;
			for ($h = $y; $h < ($this->offset_y + $this->word_higth); ++$h) {
				for ($w = $x; $w < ($x + $this->word_width); ++$w){
					$data[$i] .= $this->dataArray[$h][$w];
				}
			}
		}

		// 进行关键字匹配
		foreach ($data as $numkey => $numstring) {
			$max = 0.0;
			$num = 0;
			foreach ($this->keys as $key => $value) {
				$percent = 0.0;
				// 匹配两个字符串的相似程度，并返回相似程度百分比
				similar_text($value, $numstring,$percent);
				if (intval($percent) > $max) {
					$max = $percent;
					$num = $key;
					if (intval($percent) > 95){
						break;
					}
				}
			}
			$result .= $num;
		}
		$this->data = $result;
		return $result;
	}

	public function Draw(){
		for ($i = 0; $i < $this->imageSize[1]; ++$i) {
			for ($j = 0; $j < $this->imageSize[0]; ++$j) {
				echo $this->dataArray[$i][$j];
			}
			echo "/n";
		}
	}

}

$imagePath = 'imagecode.png';//76605 输出76605  正确
$code1 = 'code1.png';//实际59295 输出59285
$code2 = 'code2.png';//实际14464 输出14464 正确
$code3 = 'code3.png';//实际29280 输出29280  正确
$code4 = 'code4.png';//实际77301 输出77384
$code5 = 'code5.png';//实际27559 输出25559
$code6 = 'code6.png';//实际15484 输出15484  正确
$code7 = 'code7.png';//实际30891 输出38891
$code8 = 'code8.png';//实际77146 输出27146
$code9 = 'code9.png';//实际16012 输出15812
$code10 = 'code10.png';//实际11621 输出11621 正确
$valite = new valite($code10);
$valite->getCode();
//$valite->Draw();
$num = $valite->search();
var_dump($num);
