<?php namespace Dzung\Blog\Controllers\Frontend;

use Dzung\Blog\Models\Tag;
use Platform\Foundation\Controllers\BaseController;
use View;

class TagsController extends BaseController {

	/**
	 * Return the main view.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		return View::make('dzung/blog::index');
	}

    public static function tag_sidebar(){
        return Tag::all();
    }
}
