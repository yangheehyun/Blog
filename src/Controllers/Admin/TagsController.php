<?php namespace Dzung\Blog\Controllers\Admin;

use DataGrid;
use Input;
use Lang;
use Platform\Admin\Controllers\Admin\AdminController;
use Redirect;
use Response;
use View;
use Dzung\Blog\Repositories\TagRepositoryInterface;

class TagsController extends AdminController {

	/**
	 * {@inheritDoc}
	 */
	protected $csrfWhitelist = [
		'executeAction',
	];

	/**
	 * The Blog repository.
	 *
	 * @var \Dzung\Blog\Repositories\TagRepositoryInterface
	 */
	protected $tag;

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
	 * @param  \Dzung\Blog\Repositories\TagRepositoryInterface  $tag
	 * @return void
	 */
	public function __construct(TagRepositoryInterface $tag)
	{
		parent::__construct();

		$this->tag = $tag;
	}

	/**
	 * Display a listing of tag.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		return View::make('dzung/blog::tags.index');
	}

	/**
	 * Datasource for the tag Data Grid.
	 *
	 * @return \Cartalyst\DataGrid\DataGrid
	 */
	public function grid()
	{
		$data = $this->tag->grid();

		$columns = [
			'id',
			'name',
			'created_at',
		];

		$settings = [
			'sort'      => 'created_at',
			'direction' => 'desc',
		];

		return DataGrid::make($data, $columns, $settings);
	}

	/**
	 * Show the form for creating new tag.
	 *
	 * @return \Illuminate\View\View
	 */
	public function create()
	{
		return $this->showForm('create');
	}

	/**
	 * Handle posting of the form for creating new tag.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store()
	{
		return $this->processForm('create');
	}

	/**
	 * Show the form for updating tag.
	 *
	 * @param  int  $id
	 * @return mixed
	 */
	public function edit($id)
	{
		return $this->showForm('update', $id);
	}

	/**
	 * Handle posting of the form for updating tag.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update($id)
	{
		return $this->processForm('update', $id);
	}

	/**
	 * Remove the specified tag.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function delete($id)
	{
		if ($this->tag->delete($id))
		{
			$message = Lang::get('dzung/blog::tags/message.success.delete');

			return Redirect::toAdmin('blog/tags')->withSuccess($message);
		}

		$message = Lang::get('dzung/blog::tags/message.error.delete');

		return Redirect::toAdmin('blog/tags')->withErrors($message);
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
				$this->tag->{$action}($entry);
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
		// Do we have a tag identifier?
		if (isset($id))
		{
			if ( ! $tag = $this->tag->find($id))
			{
				$message = Lang::get('dzung/blog::tags/message.not_found', compact('id'));

				return Redirect::toAdmin('blog/tags')->withErrors($message);
			}
		}
		else
		{
			$tag = $this->tag->createModel();
		}

		// Show the page
		return View::make('dzung/blog::tags.form', compact('mode', 'tag'));
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

		// Do we have a tag identifier?
		if ($id)
		{
			// Check if the data is valid
			$messages = $this->tag->validForUpdate($id, $data);

			// Do we have any errors?
			if ($messages->isEmpty())
			{
				// Update the tag
				$tag = $this->tag->update($id, $data);
			}
		}
		else
		{
			// Check if the data is valid
			$messages = $this->tag->validForCreation($data);

			// Do we have any errors?
			if ($messages->isEmpty())
			{
				// Create the tag
				$tag = $this->tag->create($data);
			}
		}

		// Do we have any errors?
		if ($messages->isEmpty())
		{
			// Prepare the success message
			$message = Lang::get("dzung/blog::tags/message.success.{$mode}");

			return Redirect::toAdmin("blog/tags/{$tag->id}/edit")->withSuccess($message);
		}

		return Redirect::back()->withInput()->withErrors($messages);
	}

}
