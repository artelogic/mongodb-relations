<?php

namespace Artelogic\MongoRelations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ManyToArrayRelation extends ArrayOneOrManyRelation
{
    public $foreignField;

    /**
     * ManyToArrayRelation constructor.
     *
     * @param Builder $query
     * @param Model   $parent
     * @param string  $foreignKey
     * @param string  $foreignField
     * @param string  $localKey
     */
    public function __construct(Builder $query, Model $parent, $foreignKey, $foreignField, $localKey)
    {
        $this->foreignField = $foreignField;
        parent::__construct($query, $parent, $foreignKey, $localKey);
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints()
    {
        if (static::$constraints) {
            $this->query->where($this->foreignField, $this->getParentKey() ?: null);
        }
    }

    /**
     * set the constraints for the models
     * @param array $models
     */
    public function addEagerConstraints(array $models)
    {
        $this->query->whereIn($this->foreignField, $this->getKeys($models, $this->localKey) ?: []);
    }

    protected function buildDictionary(Collection $results)
    {
        $foreign    = $this->getForeignKeyName();
        $dictionary = [];

        $results->each(function ($model) use (&$dictionary, $foreign) {
            return $dictionary[$model->{$foreign}] = $model;
        });

        return $dictionary;
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array                                    $models
     * @param  \Illuminate\Database\Eloquent\Collection $results
     * @param  string                                   $relation
     * @return array
     */
    public function match(array $models, Collection $results, $relation)
    {
        $dictionary = $this->buildDictionary($results);

        // Once we have the dictionary we can simply spin through the parent models to
        // link them up with their children using the keyed dictionary to make the
        // matching very convenient and easy work. Then we'll just return them.
        foreach ($models as $model) {
            $key           = $model->getAttribute($this->localKey);
            $relatedModels = [];

            $results->each(function ($item) use (&$relatedModels, $key) {
                /** @var Model $item */
                $keys = $item->getAttribute($this->foreignField) ?: [];

                if (!empty($keys) && in_array($key, $keys)) {
                    $relatedModels[] = $item;
                }
            });

            $model->setRelation($relation, collect($relatedModels));
        }

        return $models;
    }
}
