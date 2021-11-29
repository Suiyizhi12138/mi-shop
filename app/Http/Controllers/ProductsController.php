<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;


class ProductsController extends Controller
{
    //通过id获取单个商品信息
    //param id int
    //return  json
    public function getProduct(Request $request, $id)
    {
        $liked = false; //是否已被用户收藏
        $product = Product::where('id', $id)->with([
            'details' => function ($query) {
                $query->orderBy('id', 'asc');
            }, 'parameters' => function ($query) {
                $query->orderBy('id', 'asc');
            },
            'productinfos', 'skus'

        ])
        ->first();
        //如果用户已登录
        if ($user = $request->user()) {
            $liked = boolval($user->likedProducts()->find($id));
        };

        if (!$product->on_sale) {
            return response()->json(['msg' => 'error product not on_sale', 'on_sale' => 'false']);
        } else {
            return response()->json(['product' => $product, 'liked' => $liked]);
        }
    }

    //get 

    //搜索商品
    public function searchProducts(Request $request)
    {

        $searchKey = $request->input('keyword');
        if ($searchKey === '全部商品' || $searchKey === '全部') {
            $searchResults = Product::all();
            return response()->json($searchResults);
        } else if ($searchKey) {
            $like = '%' . $searchKey . '%';
            $searchResults = Product::where('zh_name', 'like', $like)
                ->where('on_sale', true)
                ->orWhere('tags', 'like', $like)
                ->get();
            return response()->json($searchResults);
        }
    }
    ///////////////////////////////////////////////////////////////////////////////////////////
    //收藏商品相关操作
    //添加喜欢的商品
    public function addToLike(Product $product, Request $request)
    {
        $user = $request->user();
        //如果已经添加则直接返回
        if ($user->likedProducts()->find($product->id)) {
            return [];
        }

        //添加关联
        $user->likedProducts()->attach($product);

        return [];
    }
    //取消喜欢的商品
    public function deleteLike(Product $product, Request $request)
    {
        $user = $request->user();
        //取消关联
        $user->likedProducts()->detach($product);

        return [];
    }
    //查询喜欢的商品
    public function getLikeProducts(Request $request)
    {
        $user = $request->user();

        $products = $user->likedProducts;
        return response()->json($products);
    }
}
