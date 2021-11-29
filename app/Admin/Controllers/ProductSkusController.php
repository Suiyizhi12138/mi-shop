<?php

namespace App\Admin\Controllers;

use App\Models\ProductSku;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProductSkusController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '商品Sku管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ProductSku());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('description', __('Description'));
        $grid->column('price', __('Price'));
        $grid->column('market_price',__('上架价格'));
        $grid->column('stock', __('Stock'));
        $grid->column('imgs', __('Imgs'));
        $grid->column('product_id', __('Product id'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ProductSku());

        $form->text('name', __('Name'))->rules('required');
        $form->textarea('description', __('Description'));
        $form->decimal('price', __('Price'))->rules('required|numeric|min:0.01');
        $form->decimal('market_price', __('Market_Price'))->rules('required|numeric|min:0.01');
        $form->decimal('stock', __('Stock'))->rules('required');
        $form->list('imgs',__('图片'))->rules('required');
        $form->number('product_id', __('Product id'))->rules('required');

       
        return $form;
    }
}
