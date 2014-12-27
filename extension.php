<?php

use Cartalyst\Extensions\ExtensionInterface;
use Illuminate\Foundation\Application;

return [

	/*
	|--------------------------------------------------------------------------
	| Name
	|--------------------------------------------------------------------------
	|
	| This is your extension name and it is only required for
	| presentational purposes.
	|
	*/

	'name' => 'Blog',

	/*
	|--------------------------------------------------------------------------
	| Slug
	|--------------------------------------------------------------------------
	|
	| This is your extension unique identifier and should not be changed as
	| it will be recognized as a new extension.
	|
	| Ideally, this should match the folder structure within the extensions
	| folder, but this is completely optional.
	|
	*/

	'slug' => 'dzung/blog',

	/*
	|--------------------------------------------------------------------------
	| Author
	|--------------------------------------------------------------------------
	|
	| Because everybody deserves credit for their work, right?
	|
	*/

	'author' => 'Dzung',

	/*
	|--------------------------------------------------------------------------
	| Description
	|--------------------------------------------------------------------------
	|
	| One or two sentences describing the extension for users to view when
	| they are installing the extension.
	|
	*/

	'description' => 'Blog platform',

	/*
	|--------------------------------------------------------------------------
	| Version
	|--------------------------------------------------------------------------
	|
	| Version should be a string that can be used with version_compare().
	| This is how the extensions versions are compared.
	|
	*/

	'version' => '0.1.0',

	/*
	|--------------------------------------------------------------------------
	| Requirements
	|--------------------------------------------------------------------------
	|
	| List here all the extensions that this extension requires to work.
	| This is used in conjunction with composer, so you should put the
	| same extension dependencies on your main composer.json require
	| key, so that they get resolved using composer, however you
	| can use without composer, at which point you'll have to
	| ensure that the required extensions are available.
	|
	*/

	'require' => ['platform/admin'],

	/*
	|--------------------------------------------------------------------------
	| Autoload Logic
	|--------------------------------------------------------------------------
	|
	| You can define here your extension autoloading logic, it may either
	| be 'composer', 'platform' or a 'Closure'.
	|
	| If composer is defined, your composer.json file specifies the autoloading
	| logic.
	|
	| If platform is defined, your extension receives convetion autoloading
	| based on the Platform standards.
	|
	| If a Closure is defined, it should take two parameters as defined
	| bellow:
	|
	|	object \Composer\Autoload\ClassLoader      $loader
	|	object \Illuminate\Foundation\Application  $app
	|
	| Supported: "composer", "platform", "Closure"
	|
	*/

	'autoload' => 'composer',

	/*
	|--------------------------------------------------------------------------
	| Register Callback
	|--------------------------------------------------------------------------
	|
	| Closure that is called when the extension is registered. This can do
	| all the needed custom logic upon registering.
	|
	| The closure parameters are:
	|
	|	object \Cartalyst\Extensions\ExtensionInterface  $extension
	|	object \Illuminate\Foundation\Application        $app
	|
	*/

	'register' => function(ExtensionInterface $extension, Application $app)
	{
		$PostRepository = 'Dzung\Blog\Repositories\PostRepositoryInterface';

		if ( ! $app->bound($PostRepository))
		{
			$app->bind($PostRepository, function($app)
			{
				$model = get_class($app['Dzung\Blog\Models\Post']);

				return new Dzung\Blog\Repositories\DbPostRepository($model, $app['events']);
			});
		}

		$CategoryRepository = 'Dzung\Blog\Repositories\CategoryRepositoryInterface';

		if ( ! $app->bound($CategoryRepository))
		{
			$app->bind($CategoryRepository, function($app)
			{
				$model = get_class($app['Dzung\Blog\Models\Category']);

				return new Dzung\Blog\Repositories\DbCategoryRepository($model, $app['events']);
			});
		}

		$TagRepository = 'Dzung\Blog\Repositories\TagRepositoryInterface';

		if ( ! $app->bound($TagRepository))
		{
			$app->bind($TagRepository, function($app)
			{
				$model = get_class($app['Dzung\Blog\Models\Tag']);

				return new Dzung\Blog\Repositories\DbTagRepository($model, $app['events']);
			});
		}
	},

	/*
	|--------------------------------------------------------------------------
	| Boot Callback
	|--------------------------------------------------------------------------
	|
	| Closure that is called when the extension is booted. This can do
	| all the needed custom logic upon booting.
	|
	| The closure parameters are:
	|
	|	object \Cartalyst\Extensions\ExtensionInterface  $extension
	|	object \Illuminate\Foundation\Application        $app
	|
	*/

	'boot' => function(ExtensionInterface $extension, Application $app)
	{
		if (class_exists('Dzung\Blog\Models\Post'))
		{
			// Get the model
			$model = $app['Dzung\Blog\Models\Post'];

			// Register a new attribute namespace
			$app['Platform\Attributes\Models\Attribute']->registerNamespace($model);
		}

		if (class_exists('Dzung\Blog\Models\Category'))
		{
			// Get the model
			$model = $app['Dzung\Blog\Models\Category'];

			// Register a new attribute namespace
			$app['Platform\Attributes\Models\Attribute']->registerNamespace($model);
		}

		if (class_exists('Dzung\Blog\Models\Tag'))
		{
			// Get the model
			$model = $app['Dzung\Blog\Models\Tag'];

			// Register a new attribute namespace
			$app['Platform\Attributes\Models\Attribute']->registerNamespace($model);
		}
	},

	/*
	|--------------------------------------------------------------------------
	| Routes
	|--------------------------------------------------------------------------
	|
	| Closure that is called when the extension is started. You can register
	| any custom routing logic here.
	|
	| The closure parameters are:
	|
	|	object \Cartalyst\Extensions\ExtensionInterface  $extension
	|	object \Illuminate\Foundation\Application        $app
	|
	*/

	'routes' => function(ExtensionInterface $extension, Application $app)
	{
		Route::group(['namespace' => 'Dzung\Blog\Controllers'], function()
		{
			Route::group(['prefix' => admin_uri().'/blog/posts', 'namespace' => 'Admin'], function()
			{
				Route::get('/', 'PostsController@index');
				Route::post('/', 'PostsController@executeAction');
				Route::get('grid', 'PostsController@grid');
                Route::get('getslug/{title}', 'PostsController@getslug');
				Route::get('create', 'PostsController@create');
				Route::post('create', 'PostsController@store');
				Route::get('{id}/edit', 'PostsController@edit');
				Route::post('{id}/edit', 'PostsController@update');
				Route::get('{id}/delete', 'PostsController@delete');
			});

			Route::group(['prefix' => 'blog/posts', 'namespace' => 'Frontend'], function()
			{
				Route::get('/', 'PostsController@index');
                Route::get('/table', 'PostsController@table');
			});
            Route::group(['prefix' => 'blog/post', 'namespace' => 'Frontend'], function()
            {
                Route::get('/{slug}', 'PostsController@get_post');

            });
		});

		Route::group(['namespace' => 'Dzung\Blog\Controllers'], function()
		{
			Route::group(['prefix' => admin_uri().'/blog/categories', 'namespace' => 'Admin'], function()
			{
				Route::get('/', 'CategoriesController@index');
				Route::post('/', 'CategoriesController@executeAction');
				Route::get('grid', 'CategoriesController@grid');
				Route::get('create', 'CategoriesController@create');
				Route::post('create', 'CategoriesController@store');
				Route::get('{id}/edit', 'CategoriesController@edit');
				Route::post('{id}/edit', 'CategoriesController@update');
				Route::get('{id}/delete', 'CategoriesController@delete');
                Route::get('getslug', 'CategoriesController@getslug');

			});

			Route::group(['prefix' => 'blog/categories', 'namespace' => 'Frontend'], function()
			{
				Route::get('/', 'CategoriesController@index');

			});
            Route::group(['prefix' => 'blog/category', 'namespace' => 'Frontend'], function()
            {
                Route::get('{slug}', 'PostsController@get_post_from_cat');

            });
		});

		Route::group(['namespace' => 'Dzung\Blog\Controllers'], function()
		{
			Route::group(['prefix' => admin_uri().'/blog/tags', 'namespace' => 'Admin'], function()
			{
				Route::get('/', 'TagsController@index');
				Route::post('/', 'TagsController@executeAction');
				Route::get('grid', 'TagsController@grid');
				Route::get('create', 'TagsController@create');
				Route::post('create', 'TagsController@store');
				Route::get('{id}/edit', 'TagsController@edit');
				Route::post('{id}/edit', 'TagsController@update');
				Route::get('{id}/delete', 'TagsController@delete');
			});

			Route::group(['prefix' => 'blog/tags', 'namespace' => 'Frontend'], function()
			{
				Route::get('/', 'TagsController@index');
			});
            Route::group(['prefix' => 'blog/tag', 'namespace' => 'Frontend'], function()
            {
                Route::get('{tag_name}', 'PostsController@get_post_from_tag');
            });
		});
	},

	/*
	|--------------------------------------------------------------------------
	| Database Seeds
	|--------------------------------------------------------------------------
	|
	| Platform provides a very simple way to seed your database with test
	| data using seed classes. All seed classes should be stored on the
	| `database/seeds` directory within your extension folder.
	|
	| The order you register your seed classes on the array below
	| matters, as they will be ran in the exact same order.
	|
	| The seeds array should follow the following structure:
	|
	|	Vendor\Namespace\Database\Seeds\FooSeeder
	|	Vendor\Namespace\Database\Seeds\BarSeeder
	|
	*/

	'seeds' => [

	],

	/*
	|--------------------------------------------------------------------------
	| Permissions
	|--------------------------------------------------------------------------
	|
	| List of permissions this extension has. These are shown in the user
	| management area to build a graphical interface where permissions
	| may be selected.
	|
	| The admin controllers state that permissions should follow the following
	| structure:
	|
	|    Vendor\Namespace\Controller@method
	|
	| For example:
	|
	|    Platform\Users\Controllers\Admin\UsersController@index
	|
	| These are automatically generated for controller routes however you are
	| free to add your own permissions and check against them at any time.
	|
	| When writing permissions, if you put a 'key' => 'value' pair, the 'value'
	| will be the label for the permission which is displayed when editing
	| permissions.
	|
	*/

	'permissions' => function()
	{
		return [
			'Dzung\Blog\Controllers\Admin\PostsController@index,grid'   => Lang::get('dzung/blog::posts/permissions.index'),
			'Dzung\Blog\Controllers\Admin\PostsController@create,store' => Lang::get('dzung/blog::posts/permissions.create'),
			'Dzung\Blog\Controllers\Admin\PostsController@edit,update'  => Lang::get('dzung/blog::posts/permissions.edit'),
			'Dzung\Blog\Controllers\Admin\PostsController@delete'       => Lang::get('dzung/blog::posts/permissions.delete'),

			'Dzung\Blog\Controllers\Admin\CategoriesController@index,grid'   => Lang::get('dzung/blog::categories/permissions.index'),
			'Dzung\Blog\Controllers\Admin\CategoriesController@create,store' => Lang::get('dzung/blog::categories/permissions.create'),
			'Dzung\Blog\Controllers\Admin\CategoriesController@edit,update'  => Lang::get('dzung/blog::categories/permissions.edit'),
			'Dzung\Blog\Controllers\Admin\CategoriesController@delete'       => Lang::get('dzung/blog::categories/permissions.delete'),

			'Dzung\Blog\Controllers\Admin\TagsController@index,grid'   => Lang::get('dzung/blog::tags/permissions.index'),
			'Dzung\Blog\Controllers\Admin\TagsController@create,store' => Lang::get('dzung/blog::tags/permissions.create'),
			'Dzung\Blog\Controllers\Admin\TagsController@edit,update'  => Lang::get('dzung/blog::tags/permissions.edit'),
			'Dzung\Blog\Controllers\Admin\TagsController@delete'       => Lang::get('dzung/blog::tags/permissions.delete'),
		];
	},

	/*
	|--------------------------------------------------------------------------
	| Widgets
	|--------------------------------------------------------------------------
	|
	| Closure that is called when the extension is started. You can register
	| all your custom widgets here. Of course, Platform will guess the
	| widget class for you, this is just for custom widgets or if you
	| do not wish to make a new class for a very small widget.
	|
	*/

	'widgets' => function()
	{

	},

	/*
	|--------------------------------------------------------------------------
	| Settings
	|--------------------------------------------------------------------------
	|
	| Register any settings for your extension. You can also configure
	| the namespace and group that a setting belongs to.
	|
	*/

	'settings' => function()
	{

	},

	/*
	|--------------------------------------------------------------------------
	| Menus
	|--------------------------------------------------------------------------
	|
	| You may specify the default various menu hierarchy for your extension.
	| You can provide a recursive array of menu children and their children.
	| These will be created upon installation, synchronized upon upgrading
	| and removed upon uninstallation.
	|
	| Menu children are automatically put at the end of the menu for extensions
	| installed through the Operations extension.
	|
	| The default order (for extensions installed initially) can be
	| found by editing app/config/platform.php.
	|
	*/

	'menus' => [

		'admin' => [
			[
				'slug' => 'admin-dzung-blog',
				'name' => 'Blog',
				'class' => 'fa fa-circle-o',
				'uri' => 'blog',
				'children' => [
					[
						'slug' => 'admin-dzung-blog-post',
						'name' => 'Posts',
						'class' => 'fa fa-circle-o',
						'uri' => 'blog/posts',
					],
					[
						'slug' => 'admin-dzung-blog-category',
						'name' => 'Categories',
						'class' => 'fa fa-circle-o',
						'uri' => 'blog/categories',
					],
					[
						'slug' => 'admin-dzung-blog-tag',
						'name' => 'Tags',
						'class' => 'fa fa-circle-o',
						'uri' => 'blog/tags',
					],
				],
			],
		],
		'main' => [
			[
				'slug' => 'main-dzung-blog',
				'name' => 'Blog',
				'class' => 'fa fa-circle-o',
				'uri' => 'blog',
			],
		],
	],

];
