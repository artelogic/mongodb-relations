# mongodb relations for laravel 5.*

example of usage:
```
<?php

namespace App\Models;

use Artelogic\MongoRelations\ArrayRelations;
use App\User;
use Carbon\Carbon;
use Jenssegers\Mongodb\Eloquent\Model;

/**
 * @property string     _id
 * @property string     title
 * @property string     description
 * @property int        year
 * @property array      _users
 * @property Collection Users       *readonly
 * @property Carbon     created_at
 * @property Carbon     updated_at
 */
class Project extends Model
{
    use ArrayRelations;

    protected $fillable = [
        'title',
        'year',
        'description',
        '_users',
    ];
    protected $hidden   = [
        '_id',
        '_users',
    ];

    public function users()
    {
        return $this->arrayToManyRelation(User::class, '_id', '_users');
    }
}
```


