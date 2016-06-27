<?php

/*
 * This file is part of the online grades system for STI College Novaliches
 *
 * (c) Raphael Marco <raphaelmarco@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Extensions\Importer;

use DB;

/**
 * Student Sheet importer
 */
class StudentImporter implements ImporterInterface
{
    public static function import($data)
    {
        $timestamp = date('Y-m-d H:i:s');
        $chunks = array_chunk($data, 500);

        foreach ($chunks as $students) {
            $values = [];
            $bindings = [];

            $tables = '(id, last_name, first_name, middle_name, course, created_at, updated_at)';

            foreach ($students as $i => $student) {
                $values[] = '(?, ?, ?, ?, ?, "' . $timestamp . '", "' . $timestamp . '")';
                $bindings = array_merge($bindings, array_values($student));
            }

            DB::insert('insert ignore into students ' . $tables . ' values ' . implode(',', $values), $bindings);
        }
    }
}