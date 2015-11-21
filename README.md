# Laravel object based view templates

Homepage: https://github.com/mfn/php-laravel-view-obj

# Requirements

PHP 5.5 / Laravel 5.0/5.1

# Install / Setup

Using composer: `composer.phar require mfn/laravel-view-obj 0.1`

Register the service provider in your `config/app.php` by adding this line to
your `providers` entry: `Mfn\Laravel\ViewObj\Provider::class`

Publish the configuration:

`php artisan vendor:publish --provider="Mfn\Laravel\ViewObj\Provider"`

# Documentation

Turns an objects class hierarchy into a path for a view template, i.e. if you
want to "view" an object of type `App\Article`, the provided helper `view_obj()`
will try to render the view `_view_obj.App.Article.default` which usually maps
to your path `<PROJECT_ROOT>/resources/views/_view_obj/App/Article/default.php`
(or `default.blade.php`). The object itself will be passed to that view as `$obj`.

Conceptually you can think of it as a partial, where the name of the view is
derived from the objects class hierarchy.

The signature of the helper is:

`view_obj(object $object, string $template = 'default', array $data = []): View`

- `object $object`<br>Any object you want to render. Can be a Model or just
  about anything, as long as you created a view template for it in the
  appropriate location

- `string $template`<br>The actual view of that object. The concept is that
  depending on the context you want to render the object, you may want to use
  a different view.

- `array $data`<br>Any data you want to pass in addition to the template.
  Except the note below, the view will only receive explicitly passed variables.
  Note: the object itself is always available as `$obj`.

- returns a `View` object.

For Blade, the directive `@view_obj` is provided with the same signature but
it will already `echo` the returned `View`, whereas in pure PHP code you receive
a `View` object and need act on it yourself (e.g. `echo ...` or `->__toString()`).

For convenience sake, the helper just accepts `(object $object, array $data)`,
i.e. you can omit the `$template` for the default case.

## Examples

Most simple case, show the default view of the object. Assuming the class
`App\Article` we first create the default template in
`resources/views/_view_obj/App/Article/default.blade.php`:

*Note: the object we render always gets passed as `$obj`*
```HTML
<article>
<h1>{{ $obj->title }}</h1>
<div>
{{ $obj->content }}
</div>
</article>
```

Anywhere in a view where you pass on the article you can invoke the view with:
```PHP
@view_obj($article)
```

Assuming now you want to render the article as part of a list, i.e. you have an
array of articles. We first create a `list` template for the article in
`resources/views/_view_obj/App/Article/list.blade.php`. We usually only display
the link/title in the list:

```HTML
<li>
  <a href="{{ URL::route('article', [$obj->id]) }}">
    {{ $obj->title }}
  </a>
</li>
```

Now using the `list` view:

```HTML
<?php foreach ($articles as $article) { ?>
@view_obj($article, 'list');
<?php } ?>
```

# Configuration

- `base_path`: Defaults to `_view_obj.` and specifies the prefix for all object
  template specific views. If you don't want this separation, just set it to an
  empty string.

# Contribute

Fork it, hack on a feature branch, create a pull request, be awesome!

No developer is an island so adhere to these standards:

* [PSR 4 Autoloader](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* [PSR 2 Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
* [PSR 1 Coding Standards](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)

© Markus Fischer <markus@fischer.name>
