<?php

/*
 * This file is part of the online grades system for STI College Novaliches
 *
 * (c) Raphael Marco <raphaelmarco@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;
use App\Traits\ChoosableTrait;

/**
 * Section model
 * 
 * Section model for sections table
 */
class Section extends Model
{
    use ChoosableTrait;

    public $timestamps = false;

    protected $fillable = array(
        'id'
    );

    /**
     * Get name for choice display. Used by FormModelChoicesTrait
     * 
     * return string
     */
    public function getChoiceName()
    {
        return $this->id;
    }

    /**
     * Get associated faculties
     * 
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function faculties()
    {
        return $this->belongsToMany('App\Models\Faculty');
    }
}
