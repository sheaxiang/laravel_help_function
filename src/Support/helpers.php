<?php
use Intervention\Image\Facades\Image;

if(!function_exists('is_weixin')) {

	/**
	 * 判断是否在微信浏览器
	 * @return bool
	 */
	function is_weixin(){
		if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
			return true;
		}
		return false;
	}
}

if(!function_exists('filter_emoji')) {

	/**
	 * 过滤表情
	 * @param $str
	 * @return null|string|string[]
	 */
	function filter_emoji($str)
	{
		$str = preg_replace_callback(
			'/./u',
			function (array $match) {
				return strlen($match[0]) >= 4 ? '' : $match[0];
			},
			$str);

		return $str;
	}
}

if(!function_exists('modify_env')) {

	/**
	 * 修改env配置文件
	 * @param array $data
	 */
	function modify_env(array $data)
	{
		$envPath = base_path() . DIRECTORY_SEPARATOR . '.env';

		$contentArray = collect(file($envPath, FILE_IGNORE_NEW_LINES));

		$contentArray->transform(function ($item) use ($data){
			foreach ($data as $key => $value){
				if(str_contains($item, $key)){
					return $key . '=' . $value;
				}
			}

			return $item;
		});

		$content = implode($contentArray->toArray(), "\n");

		\Illuminate\Support\Facades\File::put($envPath, $content);
	}
}

if(!function_exists('generate_promotion_code')) {

	/**
	 * 生成优惠码
	 * @param int $no_of_codes//定义一个int类型的参数 用来确定生成多少个优惠码
	 * @param array $exclude_codes_array//定义一个exclude_codes_array类型的数组
	 * @param int $code_length //定义一个code_length的参数来确定优惠码的长度
	 * @return array//返回数组
	 */
	function generate_promotion_code($no_of_codes,$exclude_codes_array='',$code_length = 12)
	{
		$characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$promotion_codes = array();//这个数组用来接收生成的优惠码
		for($j = 0 ; $j < $no_of_codes; $j++)
		{
			$code = "";
			for ($i = 0; $i < $code_length; $i++)
			{
				$code .= $characters[mt_rand(0, strlen($characters)-1)];
			}
			//如果生成的4位随机数不再我们定义的$promotion_codes函数里面
			if(!in_array($code,$promotion_codes))
			{
				if(is_array($exclude_codes_array))//
				{
					if(!in_array($code,$exclude_codes_array))//排除已经使用的优惠码
					{
						$promotion_codes[$j] = $code;//将生成的新优惠码赋值给promotion_codes数组
					} else {
						$j--;
					}
				} else {
					$promotion_codes[$j] = $code;//将优惠码赋值给数组
				}
			} else {
				$j--;
			}
		}
		return $promotion_codes;
	}
}

if(!function_exists('get_uid')) {

	/**
	 * 生成唯一码
	 * @param $no_of_codes
	 * @return array
	 */
	function get_uid($no_of_codes) {
		$promotion_codes = array();//这个数组用来接收生成的优惠码
		for($j = 0 ; $j < $no_of_codes; $j++)
		{
			mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
			$charid = strtoupper(md5(uniqid(rand(), true)));
			$hyphen = chr(45);// "-"
			$promotion_codes[$j] = substr($charid, 0, 8).$hyphen
				.substr($charid,16, 8);
		}
		return $promotion_codes;
	}
}

if(!function_exists('get_order_no')) {
	/**
	 * 生成订单编号
	 *
	 * @param string $prefix
	 * @return string
	 */
	function get_order_no($prefix = 'Q')
	{
		/* 选择一个随机的方案 */
		mt_srand((double) microtime()*1000000);
		return $order_no = $prefix.date('YmdHis').str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
	}
}

if(!function_exists('is_debug')){

	/**
	 * 检测是否调试模式
	 *
	 * @return bool
	 */
	function is_debug(){

		return Config::get('app.debug');
	}
}

if(!function_exists('upload_image')) {

	/**
	 * 上传图片
	 * @param $file_name
	 * @param $file
	 * @param array $size
	 * @return null|string
	 */
	function upload_image($file_name, $file, array $size = array())
	{
		if(count($file) > 1 || is_array($file)) {//多图上传
			$images = [];
			foreach($file as $item) {
				$path = $file_name.'/'.date('Ym/').md5(time().str_random(20)).'.'.$item->getClientOriginalExtension();
				$image = empty($size) ? Image::make($item)->encode($item->getClientOriginalExtension(), 75) :
					Image::make($item)->fit($size['width'], $size['height'])->encode($item->getClientOriginalExtension(), 75);

				if (Storage::disk('public')->put($path, (string)$image, 'public')) {
					$images[] =  Storage::url($path);
				}
			}
			return count($images) > 1 ? $images : $images[0];
		} else {//单图上传
			$path = $file_name.'/'.date('Ym/').md5(time().str_random(20)).'.'.$file->getClientOriginalExtension();
			$image = empty($size) ? Image::make($file)->encode($file->getClientOriginalExtension(), 75) :
				Image::make($file)->fit($size['width'], $size['height'])->encode($file->getClientOriginalExtension(), 75);

			if (Storage::disk('public')->put($path, (string)$image, 'public')) {
				return Storage::url($path);
			}
		}


		return null;
	}
}

if(!function_exists('del_file')) {

	/**
	 * 删除文件
	 * @param $fileName
	 * @return bool
	 */
	function del_file($fileName) {
		Storage::disk('public')->delete(preg_replace('/\/storage/','',$fileName));
		return true;
	}
}




