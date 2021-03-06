<?php

class MardetController extends \BaseController
{

    private $chapterTypes = ['shuttle', 'section', 'squad', 'platoon', 'company', 'battalion', 'corps', 'exp_force', 'regiment'];
    private $permissions = ['ADD' => 'ADD_MARDET', 'EDIT' => 'EDIT_MARDET', 'DELETE' => 'DELETE_MARDET'];
    private $auditName = 'MardetController';
    private $select = 'Select a MARDET Type';
    private $title = 'a MARDET';
    private $branch = 'RMMC';
    private $routePrefix = 'mardet';

    use Medusa\Echelons\MedusaEchelons;

    private function getCommands()
    {
        $types = ['ship', 'corps', 'exp_force', 'regiment'];

        foreach ($types as $type) {
            if (empty($chapters) == true) {
                $chapters = Chapter::getChaptersByType($type);
            } else {
                $chapters = array_merge($chapters, Chapter::getChaptersByType($type));
            }
        }

        asort($chapters);

        return $chapters;
    }

    private function getBranches()
    {
        return Form::hidden('branch', $this->branch);
    }

}