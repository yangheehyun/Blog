<?php namespace Dzung\Blog\Controllers\Admin;

use DataGrid;
use Dzung\Blog\Models\Category;
use Dzung\Blog\Models\Post;
use Dzung\Blog\Models\Tag;
use Input;
use Lang;
use Platform\Admin\Controllers\Admin\AdminController;
use Redirect;
use Response;
use View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Dzung\Blog\Repositories\PostRepositoryInterface;

class PostsController extends AdminController {

	/**
	 * {@inheritDoc}
	 */
	protected $csrfWhitelist = [
		'executeAction',
	];

	/**
	 * The Blog repository.
	 *
	 * @var \Dzung\Blog\Repositories\PostRepositoryInterface
	 */
	protected $post;

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
	 * @param  \Dzung\Blog\Repositories\PostRepositoryInterface  $post
	 * @return void
	 */
	public function __construct(PostRepositoryInterface $post)
	{
		parent::__construct();

		$this->post = $post;
	}

	/**
	 * Display a listing of post.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
        $posts = Post::all();

		return View::make('dzung/blog::posts.index')->with('posts', $posts);
	}

	/**
	 * Datasource for the post Data Grid.
	 *
	 * @return \Cartalyst\DataGrid\DataGrid
	 */
	public function grid()
	{
		$data = $this->post->grid();
        //$data = Category::chunk(2);
		$columns = [
			'id',
			'title',
			'content',
			'slug',
			'category_id',
			'created_at',
		];

		$settings = [
			'sort'      => 'created_at',
			'direction' => 'desc',
		];

		return DataGrid::make($data, $columns, $settings);
	}

	/**
	 * Show the form for creating new post.
	 *
	 * @return \Illuminate\View\View
	 */
	public function create()
	{
		return $this->showForm('create');
	}

	/**
	 * Handle posting of the form for creating new post.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store()
	{
		return $this->processForm('create');
	}

	/**
	 * Show the form for updating post.
	 *
	 * @param  int  $id
	 * @return mixed
	 */
	public function edit($id)
	{
		return $this->showForm('update', $id);
	}

	/**
	 * Handle posting of the form for updating post.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update($id)
	{
		return $this->processForm('update', $id);
	}

	/**
	 * Remove the specified post.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function delete($id)
	{
        $cat_id = Post::find($id)->category_id;

		if ($this->post->delete($id))
		{
            self::update_no_post($cat_id);
            self::delete_tags($id);
			$message = Lang::get('dzung/blog::posts/message.success.delete');

			return Redirect::toAdmin('blog/posts')->withSuccess($message);
		}

		$message = Lang::get('dzung/blog::posts/message.error.delete');

		return Redirect::toAdmin('blog/posts')->withErrors($message);
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
				$this->post->{$action}($entry);
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
        $tags = "";
		// Do we have a post identifier?
		if (isset($id))
		{

			if ( ! $post = $this->post->find($id))
			{
				$message = Lang::get('dzung/blog::posts/message.not_found', compact('id'));
				return Redirect::toAdmin('blog/posts')->withErrors($message);
			}
            $post_tags = DB::table('post_tag')->where('post_id', $id)->get();
            if(!empty($post_tags)) {
                foreach ($post_tags as $post_tag) {

                    $tag[] = Tag::find($post_tag->tag_id)->name;
                }
                $tags = implode(",", $tag);
            }
		}
		else
		{
			$post = $this->post->createModel();
		}

		// Show the page
        $categories = Category::all();
		return View::make('dzung/blog::posts.form', compact('mode', 'post', 'categories', 'tags'));
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


        // update the numper of post in the related category
        $category_name = $data['category_id'];
        $data['category_id'] = Category::where('name', '=', $category_name)->first()->id;

        $new_cat_id = $data['category_id'];
        $old_cat_id = $data['old_cat_id'];
        unset($data['old_cat_id']);

        // get tags
        $tags = $data['tags'];
        $temp_tags = strtolower($tags);
        $temp_tags = explode(',', $temp_tags);
        $temp_tags = array_map('trim', $temp_tags);
        $temp_tags = array_unique($temp_tags);

        unset($data['tags']);
		// Do we have a post identifier?
		if ($id)
		{
			// Check if the data is valid
			$messages = $this->post->validForUpdate($id, $data);

			// Do we have any errors?
			if ($messages->isEmpty())
			{
				// Update the post
				$post = $this->post->update($id, $data);
			}
		}
		else
		{
			//Check if the data is valid
			$messages = $this->post->validForCreation($data);

			// Do we have any errors?
			if ($messages->isEmpty())
			{
				// Create the post
				$post = $this->post->create($data);

			}
		}

		// Do we have any errors?
		if ($messages->isEmpty())
		{
			// Prepare the success message
			$message = Lang::get("dzung/blog::posts/message.success.{$mode}");
            if($old_cat_id != 0)
                self::update_no_post($old_cat_id);
            self::update_no_post($new_cat_id);

            // delete all old tag of this post
           self::delete_tags($post->id);

            //save new tag to db
            foreach($temp_tags as $temp_tag){
                $tag = Tag::firstOrCreate(array('name' => $temp_tag));
                DB::table('post_tag')->insertGetId(
                        array('post_id' => $post->id, 'tag_id' => $tag->id)
                );

            }
			return Redirect::toAdmin("blog/posts/{$post->id}/edit")->withSuccess($message);
		}

        $data['old_cat_id'] = $old_cat_id;
        $data['tags'] = $tags;
		return Redirect::back()->withInput()->withErrors($messages);
	}


    // update number of post in old and new catgory
    // $id string id of category to update

    public static function update_no_post($id){
        $cat = Category::find($id);
        $cat->num_post = Post::where('category_id', '=', $id)->count();
        $cat->save();
        return $cat->num_post;

    }

    public function getslug($title){
        $title = Input::get('title');
        $slug = $this->genslug($title);
        return $slug;
    }

    // delete all tags of post
    public static function delete_tags($id){
        $tag = DB::table('post_tag')->where('post_id', '=', $id)->first();
        while(!empty($tag)){
            $num_tag = DB::table('post_tag')->where('tag_id', $tag->tag_id)->count();

            // delete tag in tag table if needed
            if($num_tag == 1){
                Tag::find($tag->tag_id)->delete();
            }
            DB::table('post_tag')->where('post_id', '=', $id)->where('tag_id', '=', $tag->tag_id )->delete();
            $tag = DB::table('post_tag')->where('post_id', '=', $id)->first();
        }

    }
    protected function  genslug($title){
        $raw_slug =  Str::slug($title);
        $count = 0;
        $post = Post::where('slug', '=', $raw_slug)->first();
        //if(!empty($post)) $raw_slug .= "-";
        while(!empty($post)){
            $count++;
            $post = Post::where('slug', '=', $raw_slug."-".$count)->first();
        }

        if($count == 0) return $raw_slug;
        return $raw_slug."-".$count;
    }


}


