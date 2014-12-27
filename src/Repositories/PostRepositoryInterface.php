<?php namespace Dzung\Blog\Repositories;

interface PostRepositoryInterface {

	/**
	 * Returns a dataset compatible with data grid.
	 *
	 * @return \Dzung\Blog\Models\Post
	 */
	public function grid();

	/**
	 * Returns all the blog entries.
	 *
	 * @return \Dzung\Blog\Models\Post
	 */
	public function findAll();

	/**
	 * Returns a blog entry by its primary key.
	 *
	 * @param  int  $id
	 * @return \Dzung\Blog\Models\Post
	 */
	public function find($id);

	/**
	 * Determines if the given blog is valid for creation.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Support\MessageBag
	 */
	public function validForCreation(array $data);

	/**
	 * Determines if the given blog is valid for update.
	 *
	 * @param  int  $id
	 * @param  array  $data
	 * @return \Illuminate\Support\MessageBag
	 */
	public function validForUpdate($id, array $data);

	/**
	 * Creates a blog entry with the given data.
	 *
	 * @param  array  $data
	 * @return \Dzung\Blog\Models\Post
	 */
	public function create(array $data);

	/**
	 * Updates the blog entry with the given data.
	 *
	 * @param  int  $id
	 * @param  array  $data
	 * @return \Dzung\Blog\Models\Post
	 */
	public function update($id, array $data);

	/**
	 * Deletes the blog entry.
	 *
	 * @param  int  $id
	 * @return bool
	 */
	public function delete($id);

}
