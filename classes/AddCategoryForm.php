<?php

/**
 * class AddCategoryForm
 */
class AddCategoryForm extends BaseForm
{

    public $formName = 'category';

    protected $fields = ['category'];

    protected $rules = [
        ['required', ['category']]
    ];
}