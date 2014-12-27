<?php namespace Dzung\Blog\Repositories;

use Cartalyst\Interpret\Interpreter;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Dzung\Blog\Models\Post;
use Symfony\Component\Finder\Finder;
use Validator;

class DbPostRepository implements PostRepositoryInterface {

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
        'slug' => 'unique:blog_posts'
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
		return $this->validatePost($data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForUpdate($id, array $data)
	{
        $this->rules['slug'] .= ',slug,'.$id;
		return $this->validatePost($data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(array $data)
	{
		with($post = $this->createModel())->fill($data)->save();

		$this->dispatcher->fire('dzung.blog.post.created', $post);

		return $post;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update($id, array $data)
	{
		$post = $this->find($id);

		$post->fill($data)->save();

		$this->dispatcher->fire('dzung.blog.post.updated', $post);

		return $post;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete($id)
	{
		if ($post = $this->find($id))
		{
			$this->dispatcher->fire('dzung.blog.post.deleted', $post);

			$post->delete();

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
	protected function validatePost($data)
	{
		$validator = Validator::make($data, $this->rules);

		$validator->passes();

		return $validator->errors();
	}

}
