<?php namespace Dzung\Blog\Repositories;

use Cartalyst\Interpret\Interpreter;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Dzung\Blog\Models\Category;
use Symfony\Component\Finder\Finder;
use Validator;

class DbCategoryRepository implements CategoryRepositoryInterface {

	/**
	 * The Eloquent blog model.
	 *
	 * @var string
	 */
	protected $model;

	/**
	 * The event dispatcher instance.
	 *
	 * @var \Illuminate\Events\Dispatcher
	 */
	protected $dispatcher;

	/**
	 * Holds the form validation rules.
	 *
	 * @var array
	 */
	protected $rules = [
        'name' =>   'unique:blog_categories',
        'slug' =>   'unique:blog_categories'
	];

	/**
	 * Constructor.
	 *
	 * @param  string  $model
	 * @param  \Illuminate\Events\Dispatcher  $dispatcher
	 * @return void
	 */
	public function __construct($model, Dispatcher $dispatcher)
	{
		$this->model = $model;

		$this->dispatcher = $dispatcher;
	}

	/**
	 * {@inheritDoc}
	 */
	public function grid()
	{
		return $this
			->createModel();
	}

	/**
	 * {@inheritDoc}
	 */
	public function findAll()
	{
		return $this
			->createModel()
			->newQuery()
			->get();
	}

	/**
	 * {@inheritDoc}
	 */
	public function find($id)
	{
		return $this
			->createModel()
			->where('id', (int) $id)
			->first();
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForCreation(array $data)
	{
		return $this->validateCategory($data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForUpdate($id, array $data)
	{
        $this->rules['slug'] .= ',slug,'.$id;
        $this->rules['name'] .= ',name,'.$id;
		return $this->validateCategory($data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(array $data)
	{
		with($category = $this->createModel())->fill($data)->save();

		$this->dispatcher->fire('dzung.blog.category.created', $category);

		return $category;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update($id, array $data)
	{
		$category = $this->find($id);

		$category->fill($data)->save();

		$this->dispatcher->fire('dzung.blog.category.updated', $category);

		return $category;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete($id)
	{
		if ($category = $this->find($id))
		{
			$this->dispatcher->fire('dzung.blog.category.deleted', $category);

			$category->delete();

			return true;
		}

		return false;
	}

	/**
	 * Create a new instance of the model.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function createModel(array $data = [])
	{
		$class = '\\'.ltrim($this->model, '\\');

		return new $class($data);
	}

	/**
	 * Validates a blog entry.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Support\MessageBag
	 */
	protected function validateCategory($data)
	{
		$validator = Validator::make($data, $this->rules);

		$validator->passes();

		return $validator->errors();
	}

}
