<?php namespace Dzung\Blog\Controllers\Frontend;

use Dzung\Blog\Models\Category;
use Platform\Foundation\Controllers\BaseController;
use View;

class CategoriesController extends BaseController {

	/**
	 * Return the main view.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		return View::make('dzung/blog::index');
	}

    public static function category_sidebar(){
        return Category::all();
    }


}
