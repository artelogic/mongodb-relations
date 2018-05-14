<?php

namespace Artelogic\MongoRelations;

use Illuminate\Database\Eloquent\Collection;

class ArrayToManyRelation extends ArrayOneOrManyRelation
{
    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints()
    {
        if (static::$constraints) {
            $this->query->whereIn($this->foreignKey, $this->getParentKey() ?: []);
        }
    }

    /**
     * set the constraints for the models
     * @param array $models
     */
    public function addEagerConstraints(array $models)
    {
        $this->query->whereIn($this->foreignKey, $this->getKeys($models, $this->localKey));
    }

    /**
     * Build dictionary from the results
     * @param Collection $results
     * @return array
     */
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
            $keys           = $model->getAttribute($this->localKey);
            $relationModels = [];
            if (is_array($keys) && !empty($keys)) {
                foreach ($keys as $key) {
                    if (isset($dictionary[$key])) {
                        $relationModels[] = $dictionary[$key];
                    }
                }
            }
            $model->setRelation($relation, collect($relationModels));

        }

        return $models;
    }
}
