<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProductsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Product';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product());

        $grid->column('id', __('Id'));
        $grid->column('zh_name', __('商品'));
        $grid->column('name',__('英文名'));
        $grid->column('description', __('简介'));
        $grid->column('tags',__('标签'));
        $grid->column('category_id', __('分类'));
        $grid->column('on_sale', __('是否上架'))->display(function($value){
            return $value?"是":"否";
        });
        $grid->column('image_url', __('商品小图'));
        $grid->column('price', __('单价'));
        $grid->column('discount', __('折扣'));
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('修改时间'));
        $grid->column('top_category_id',__('顶级分类'));
        $grid->actions(function($actions){
            $actions->disableView();
            $actions->disableDelete();
        });

        $grid->tools(function($tools){
            $tools->batch(function($batch){
                $batch->disableDelete();
            });
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Product::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('description', __('Description'));
        $show->field('category_id', __('Category id'));
        $show->field('on_sale', __('On sale'));
        $show->field('image_url', __('Image url'));
        $show->field('price', __('Price'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Product());

        $form->text('name', __('名称（英文）'))->rules('required');
        $form->text('zh_name', __('中文名'))->rules('required');
        $form->textarea('description', __('描述'))->rules('required');
        $form->textarea('tags',__('标签'))->rules('required');
        $form->number('category_id', __('分类'))->rules('required')->default(1);
        $form->switch('on_sale', __('是否上架'))->default(1);
        $form->text('image_url', __('商品小图'))->rules('required');
        $form->decimal('price',__('商品单价'));
        $form->decimal('discount',__('折扣'))->rules('required');
        $form->number('top_category_id',__('顶级分类'))->rules('required')->default(8);
        //直接添加一对多的关联模型
        // $form->hasMany('skus','SKU列表',function(Form\NestedForm $form){
        //     $form->text('name', 'SKU 名称')->rules('required');
        //     $form->text('description', 'SKU 描述');
        //     $form->decimal('price', '单价')->rules('required|numeric|min:0.01');
        //     $form->decimal('market_price', '上架价格')->rules('required|numeric|min:0.01');
        //     $form->decimal('stock', '剩余库存')->rules('required|integer|min:0');
        //     $form->list('imgs',__('图片'))->rules('required');
        // });
       
        
        //直接添加概述图片
        $form->hasMany('details','商品概述图片',function(Form\NestedForm $form){
            $form->text('image_url')->rules('required');
         })->rules('required');
          //直接添加参数图片
        $form->hasMany('parameters','商品参数图片',function(Form\NestedForm $form){
            $form->text('image_url')->rules('required');
         })->rules('required');
         
        
        return $form;
    }
}
