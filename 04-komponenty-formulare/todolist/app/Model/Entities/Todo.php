<?php

namespace App\Model\Entities;

use Dibi\DateTime;
use LeanMapper\Entity;

/**
 * Class Todo
 * @package App\Model\Entities
 * @property int $todoId
 * @property string $title
 * @property string $description = ''
 * @property DateTime|null $deadline = null
 * @property bool $completed = false
 * @property Tag[] $tags m:hasMany
 *
 * @method addToTag(Tag $tag)
 * @method removeFromTag(Tag $tag)
 * @method removeAllTag()
 */
class Todo extends Entity
{

    public function getDeadlineFormatted(): string
    {
        if($this->deadline === null){
            return '';
        }

        return $this->deadline->format('j. n. Y');
    }
}