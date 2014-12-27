<?php namespace Dzung\Blog\Repositories;

use Cartalyst\Interpret\Interpreter;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Dzung\Blog\Models\Tag;
use Symfony\Component\Finder\Finder;
use Validator;

class DbTagRepository implements TagRepositoryInterface {

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
		return $this->validateTag($data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForUpdate($id, array $data)
	{
		return $this->validateTag($data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(array $data)
	{
		with($tag = $this->createModel())->fill($data)->save();

		$this->dispatcher->fire('dzung.blog.tag.created', $tag);

		return $tag;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update($id, array $data)
	{
		$tag = $this->find($id);

		$tag->fill($data)->save();

		$this->dispatcher->fire('dzung.blog.tag.updated', $tag);

		return $tag;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete($id)
	{
		if ($tag = $this->find($id))
		{
			$this->dispatcher->fire('dzung.blog.tag.deleted', $tag);

			$tag->delete();

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
	protected function validateTag($data)
	{
		$validator = Validator::make($data, $this->rules);

		$validator->passes();

		return $validator->errors();
	}

}
