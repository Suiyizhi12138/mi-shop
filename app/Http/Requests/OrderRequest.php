<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ProductSku;
use Illuminate\Validation\Rule;
class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //判断用户提交的地址 ID 是否存在于数据库并且属于当前用户
            //后面这个条件非常重要，否则恶意用户可以用不同的地址ID 不断提交订单来遍历出平台所有用户的收货地址
            'address_id'=>[
                'required',
                Rule::exists('user_addresses','id')->where('user_id',$this->user()->id)
            ],
            'items'=>['required','array'],
            'items.*.product_sku_id'=>[
                //检查items数组下每一个子数组的sku_id参数
                'required',
                function($attribute,$value,$fail){
                    if(!$sku = ProductSku::find($value)){
                        return $fail('该商品不存在');
                    }
                    if(!$sku->product->on_sale){
                        return $fail('该商品未上架');
                    }
                    if($sku->stock===0){
                        return $fail('该商品已售完');
                    }

                    //获取当前索引
                    preg_match('/items\.(d+).product_sku_id/',$attribute,$m);

                    $index = $m[1];

                    //根据索引找到用户所提交的购买数量
                    $amount = $this->input('items')[$index]['amount'];

                    if($amount>0&&$amount>$sku->stock){
                        return $fail('该商品库存不足');
                    }
                }
            ],
            'item.*.amount'=>['required','integer','min:1']

        ];
    }
}
