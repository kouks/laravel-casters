# Laravel Casters

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kouks/laravel-casters.svg?style=flat-square)](https://packagist.org/packages/kouks/laravel-casters)
[![Build Status](https://travis-ci.org/kouks/laravel-casters.svg?branch=master)](https://travis-ci.org/kouks/laravel-casters)
[![StyleCI](https://styleci.io/repos/73021711/shield?branch=master)](https://styleci.io/repos/73021711)

## Contents

- [Installation](#installation)
- [Usage](#usage)
- [FAQ](#faq)

## Installation

### Composer

Open your console and `cd` into your Laravel project. Run:

```bash
composer require kouks/laravel-casters
```

Yeah, you are pretty much ready to go right now...

## Usage

### Creating casters

You can put you casters pretty much anywhere, but I suppose you put them in a `app/Casters` directory. Now make a new php class, we are gonna be casting a Post model here. __Suppose our model has only `id`, `title`, `body` and `active`  database columns, along with timestamps.__

```php
namespace App\Casters;

use Koch\Casters\Caster;

class PostCaster extends Caster
{
    //
}
```

Good! Now we can put in our first casting rule.

```php
namespace App\Casters;

use Koch\Casters\Caster;

class PostCaster extends Caster
{
    public castRules()
    {
        return [
            'title',
        ];
    }
}
```

This caster is going to cast only the title column, leaving its contents unchanged. More about making rules in the follwing section.

### Making your rules

Let's move on how to make your own rules, there are __four__ ways to achieve that at this moment.

__Simple casting/renaming columns__

There was an example of a simple cast rule in the previous section. Let us reviews it again and att something up.

```php
namespace App\Casters;

use Koch\Casters\Caster;

class PostCaster extends Caster
{
    public castRules()
    {
        return [
            'title',
            'body' => 'long_text',
        ];
    }
}
```

This rule will remain the `title` column unchanged completely, and renaming the `body` column to `long_text`, however __leaving the contents unchanged__.

__Using cast queries__

Similar to Laravel's validation rules, you can use queries to cast.


```php
namespace App\Casters;

use Koch\Casters\Caster;

class PostCaster extends Caster
{
    public castRules()
    {
        return [
            'active' => '!name:is_active|type:bool',
        ];
    }
}
```

Note the `!` before the cast rule. This says that we want to use a query. This simple query renames the `active` column to `is_active` and casts its contents to a boolean. All documentation on cast queries can be found in the [Cast queries](#cast-queries) section.

__Casting using a closure__

As a value of the cast rules, you can speficy a closure, which will determine what data to return.


```php
namespace App\Casters;

use App\Post;
use Koch\Casters\Caster;

class PostCaster extends Caster
{
    public castRules()
    {
        return [
            'text' => function (Post $post) {
                return str_limit($post->body, 100);
            },
        ];
    }
}
```

This particular query is going to cast a __new__ column `body` and as its contents it is going to use whatever is returned from the closure. In this case - the post body limited to 100 characters. Note that __you are given an instance of the model__ that is being cast in the closure arguments.

__Casting using a method__

You can even use other methods on the caster class to determine what is going to be cast.


```php
namespace App\Casters;

use App\Post;
use Koch\Casters\Caster;

class PostCaster extends Caster
{
    public castRules()
    {
        return [
            'draft' => '@isDraft',
        ];
    }
    
    public function isDraft(Post $post)
    {
        return ! $post->active;
    }
}
```

Notice the `@` sign - it is to determine that we want to use a caster class method to do the casting. This cast is (similarly to the closure one) create a new column `draft`, which is just a negation of the `active` column in this example. You are given the model instance in this case, too. 

### Actual casting

The actual casting gets really simple, there are two ways to cast you data at this moment.

__Casting a single model__

Let us have a controller action `show`, which is going to cast our model, and retrun it as json.

```php
...

class PostController extends Controller
{
    public function show(Post $post, PostCaster $caster)
    {
        return $caster->cast($post);
    }
}

```

Note that you can use _DI_ for your casters, as with anything in Laravel. Then you call a single `cast` method and provide you model instance in the arguments. __If we were to combine all the example casters__, the returned json could look following:

```json
{
  "id": 1,
  "title": "Some title",
  "long_text": "Some long text...",
  "text": "Some long text...",
  "active": true,
  "draft": false,
  "updated_at": {
    "date": "2016-11-01 00:38:03.000000",
    "timezone_type": 3,
    "timezone": "UTC"
  },
  "created_at": {
    "date": "2016-08-06 17:58:09.000000",
    "timezone_type": 3,
    "timezone": "UTC"
  }
}
```

_Note that in the example above, the `text` field is going to be limited to 100 characters._

__Casting a collection__

You cast collections in __the same way__ that you would cast single models. Only difference is that you are given back array of cast models, instead of a single one.

### Casting relationships

There, obviously, is a way to cast relationships. Suppose there is a related model `Comments`, wich has a `many-one` relationship with our `Post` model. Also suppose that there is a `CommentCaster` set up. Look at the following code:

```php
namespace App\Casters;

use App\Post;
use Koch\Casters\Caster;

class PostCaster extends Caster
{
    protected $commentCaster;
    
    public function __construct(CommentCaster $commentCaster)
    {
        $this->commentCaster = $commentCaster;
    }
    
    public castRules()
    {
        return [
            'comments' => function (Post $post) {
                return $this->commentCaster->cast($post->comments);
            },
        ];
    }
}
```

Note that even here you can levarage Laravel's _DI_. You now create a new column `comments`, which is then populated by the closure. This closure usis the injected caster to cast the comments relationship. You can do the same with casting via methods.

__Also beware of cycling your casts. At the moment, there is no check for that. So if you were to cast your `comments` relationship on the `PostCaster` and do the same vice versa, you'd end end with a 500.__ Feel free to submit a PR correcting this issue.

### Cast queries

At this moment, there are only two queries. I am open for suggestions to add more.

- __`name:new_name`__
- __`type:new_type`__ - accepts `int`, `string`, `bool` and `float`

### Abstracion

There is a `Koch\Casters\Contracts\Caster` interface, which you could use for example with the repository pattern, where you have a parent `Repository` class which deals with all the stuff around any model, including casting it. You might want to inject the caster though a `PostRepository` constructor, in which case, the parent class would require the contract.

## FAQ

Nobody has ever asked me anything about this package so I can't determine the frequency of questions.
