<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\TopCategory;
class CategoriesController extends Controller
{
    //查询所有二级分类及八条产品
    public function getCategories(){
        $categories = Category::with([
            'products'=>function($query){
                $query->orderBy('created_at','desc');
            }
        ])
        ->limit(7)
        ->get();
        return  response()->json($categories);
    }
    //查询8条所有一级分类商品
    public function getAllTopCategories(){
        $categories = TopCategory::with([
            'products'=>function($query){
              $query->orderBy('created_at','desc');
            
            }
        ])
        
        ->get();

        return response()->json($categories);
    }
    //查询8条二级分类及商品
    public function getAllCategories(){
        $categories = Category::with([
            'products'=>function($query){
                $query->orderBy('created_at','desc');
            }
        ])
        
        ->get();
        return response()->json($categories);
    }
    
    

}
