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

use App\Models\Grade;
use App\Models\Faculty;

/**
 * Grading Sheet importer
 */
class GradeImporter implements ImporterInterface
{
    /**
     * {@inheritDoc}
     * 
     * @param Faculty $importedBy Faculty model entity to associate with grade
     */
    public static function import($sheets, Faculty $importedBy = null)
    {
        foreach ($sheets as $sheet) {
            foreach ($sheet['students'] as $student) {
                $student['student_id'] = parseStudentId($student['student_id']);
                
                $grade = Grade::where([
                    'student_id'    => $student['student_id'],
                    'subject'       => $sheet['metadata']['subject'],
                    'section'       => $sheet['metadata']['section']
                ])->first();

                if (!$grade) {
                    $grade = new Grade();
                }

                $grade->fill(array_merge($sheet['metadata'], $student));

                if ($importedBy) {
                    $grade->importer_id = $importedBy->id;
                }

                $grade->save();
            }
        }
    }
}