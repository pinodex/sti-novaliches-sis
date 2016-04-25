<?php

/*
 * This file is part of the online grades system for STI College Novaliches
 *
 * (c) Raphael Marco <raphaelmarco@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Services\Session;

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Provides session handler for database
 * 
 * This session handler uses eloquent as the ORM
 */
class IlluminateDatabaseSessionHandler implements \SessionHandlerInterface
{
    /**
     * @var Illuminate\Database\Capsule\Manager Database capsule instance
     */
    private $capsule;

    /**
     * @var array Database session storage options
     */
    private $options;

    /**
     * @var bool Whether gc() has been called
     */
    private $gced = false;

    /**
     * Constructs the class
     *
     * @param array $app
     */
    public function __construct(Capsule $capsule, $options)
    {
        $this->capsule = $capsule;
        $this->options = $options;
    }

    /**
     * Get new instance of query builder
     *
     * @return Illuminate\Database\Query\Builder
     */
    public function table()
    {
        return $this->capsule->table($this->options['table']);
    }

    public function open($savePath, $sessionName)
    {
        return true;
    }

    public function close()
    {
        if ($this->gced) {
            $this->table()->where('expiry', '<', time())->delete();
            $this->gced = false;
        }

        return true;
    }

    public function read($id)
    {
        if ($session = $this->table()->find($id)) {
            return $session->data;
        }
    }

    public function write($id, $data)
    {
        $lifetime = (int) ini_get('session.gc_maxlifetime');
        $values = array(
            'id'        => $id,
            'data'      => $data,
            'expiry'    => $lifetime + time()
        );
        
        if ($this->table()->find($id)) {
            $this->table()->where('id', $id)->update($values);

            return true;
        }

        $this->table()->insert($values);

        return true;
    }

    public function gc($maxLifeTime)
    {
        $this->gced = true;

        return true;
    }

    public function destroy($id)
    {
        $this->table->where('id', $id)->delete();

        return true;
    }
}
