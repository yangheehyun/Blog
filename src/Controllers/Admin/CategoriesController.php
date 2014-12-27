<?php namespace Dzung\Blog\Controllers\Admin;

use DataGrid;
use Input;
use Lang;
use Platform\Admin\Controllers\Admin\AdminController;
use Redirect;
use Response;
use View;
use Dzung\Blog\Repositories\CategoryRepositoryInterface;
use Illuminate\Support\Str;
use Dzung\Blog\Models\Category;
use Dzung\Blog\Controllers\Admin\PostsController;

class CategoriesController extends AdminController {

	/**
	 * {@inheritDoc}
	 */
	protected $csrfWhitelist = [
		'executeAction',
	];

	/**
	 * The Blog repository.
	 *
	 * @var \Dzung\Blog\Repositories\CategoryRepositoryInterface
	 */
	protected $category;

	/**
	 * Holds all the mass actions we can execute.
	 *
	 * @var array
	 */
	protected $actions = [
		'delete',
		'enable',
		'disable',
	];

	/**
	 * Constructor.
	 *
	 * @param  \Dzung\Blog\Repositories\CategoryRepositoryInterface  $category
	 * @return void
	 */
	public function __construct(CategoryRepositoryInterface $category)
	{
		parent::__construct();

		$this->category = $category;
	}

	/**
	 * Display a listing of category.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
        $categories = Category::all();
		return View::make('dzung/blog::categories.index')->with('categories', $categories);
	}

	/**
	 * Datasource for the category Data Grid.
	 *
	 * @return \Cartalyst\DataGrid\DataGrid
	 */
	public function grid()
	{
		$data = $this->category->grid();

        //Log::debug($data);
		$columns = [
			'id',
			'name',
			'num_post',
			'created_at',
		];

		$settings = [
			'sort'      => 'created_at',
			'direction' => 'desc',
		];

		return DataGrid::make($data, $columns, $settings);
	}

	/**
	 * Show the form for creating new category.
	 *
	 * @return \Illuminate\View\View
	 */
	public function create()
	{
		return $this->showForm('create');
	}

	/**
	 * Handle posting of the form for creating new category.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store()
	{
		return $this->processForm('create');
	}

	/**
	 * Show the form for updating category.
	 *
	 * @param  int  $id
	 * @return mixed
	 */
	public function edit($id)
	{
		return $this->showForm('update', $id);
	}

	/**
	 * Handle posting of the form for updating category.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update($id)
	{
		return $this->processForm('update', $id);
	}

	/**
	 * Remove the specified category.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function delete($id)
	{
        if($id != 0) {
            $this->set_undefined_posts($id);
            PostsController::update_no_post('0');
            PostsController::update_no_post($id);
            if ($this->category->delete($id)) {
                $message = Lang::get('dzung/blog::categories/message.success.delete');

                return Redirect::toAdmin('blog/categories')->withSuccess($message);
    		}

            $message = Lang::get('dzung/blog::categories/message.error.delete');
            return Redirect::toAdmin('blog/categories')->withErrors($message);
        }
        return Redirect::toAdmin('blog/categories')->withErrors("Deo duoc xoa danh muc nay");
	}

	/**
	 * Executes the mass action.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function executeAction()
	{
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    $action = Input::get('action');

		if (in_array($action, $this->actions))
		{
			foreach (Input::get('entries', []) as $entry)
			{
				$this->category->{$action}($entry);
			}

			return Response::json('Success');
		}

		return Response::json('Failed', 500);
	}

	/**
	 * Shows the form.
	 *
	 * @param  string  $mode
	 * @param  int  $id
	 * @return mixed
	 */
	protected function showForm($mode, $id = null)
	{
		// Do we have a category identifier?
		if (isset($id))
		{
			if ( ! $category = $this->category->find($id))
			{
				$message = Lang::get('dzung/blog::categories/message.not_found', compact('id'));

				return Redirect::toAdmin('blog/categories')->withErrors($message);
			}
		}
		else
		{
			$category = $this->category->createModel();
		}

		// Show the page
		return View::make('dzung/blog::categories.form', compact('mode', 'category'));
	}

	/**
	 * Processes the form.
	 *
	 * @param  string  $mode
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	protected function processForm($mode, $id = null)
	{
		// Get the input data
		$data = Input::all();

		// Do we have a category identifier?
		if ($id)
		{
			// Check if the data is valid
			$messages = $this->category->validForUpdate($id, $data);

			// Do we have any errors?
			if ($messages->isEmpty())
			{
				// Update the category
				$category = $this->category->update($id, $data);
			}
		}
		else
		{
			// Check if the data is valid
			$messages = $this->category->validForCreation($data);

			// Do we have any errors?
			if ($messages->isEmpty())
			{
				// Create the category
				$category = $this->category->create($data);
			}
		}

		// Do we have any errors?
		if ($messages->isEmpty())
		{
			// Prepare the success message
			$message = Lang::get("dzung/blog::categories/message.success.{$mode}");

			return Redirect::toAdmin("blog/categories/{$category->id}/edit")->withSuccess($message);
		}

		return Redirect::back()->withInput()->withErrors($messages);
	}

    protected  function set_undefined_posts($id){
        $posts = Category::find($id)->posts;
        foreach($posts as $post){
            $post->category_id = '0';
            $post->save();
        }
    }

    public function getslug(){
        $name = Input::get('name');
        $slug = $this->genslug($name);
        return $slug;

    }

    protected function genslug($name){

        $raw_slug =  Str::slug($name);
        $count = 0;
        $cat = Category::where('slug', '=', $raw_slug)->first();
        //if(!empty($post)) $raw_slug .= "-";
        while(!empty($cat)){
            $count++;
            $cat = Category::where('slug', '=', $raw_slug."-".$count)->first();
        }

        if($count == 0) return $raw_slug;
        return $raw_slug."-".$count;
    }

}
