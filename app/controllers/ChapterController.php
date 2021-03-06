<?php

class ChapterController extends BaseController
{

    public function index()
    {
        $chapters = Chapter::orderBy('chapter_type')->orderBy('chapter_name')->get();

        return View::make('chapter.index', ['chapters' => $chapters]);
    }

    public function show($chapter)
    {
        if (isset( $chapter->assigned_to )) {
            $parentChapter = Chapter::find($chapter->assigned_to);
        } else {
            $parentChapter = false;
        }

        $includes =
            Chapter::where('assigned_to', '=', $chapter->_id)
                   ->whereNull('decommission_date')
                   ->orderBy('chapter_name')
                   ->get()
                   ->toArray();

        $commandCrew = $chapter->getCommandCrew();

        $crew = $chapter->getAllCrew();

        return View::make(
            'chapter.show',
            [
                'detail'   => $chapter,
                'higher'   => $parentChapter,
                'includes' => $includes,
                'command'  => $commandCrew,
                'crew'     => $crew
            ]
        );
    }

    /**
     * Show the form for creating a new chapter
     *
     * @return Response
     */
    public function create()
    {
        if (( $redirect = $this->checkPermissions('COMMISSION_SHIP') ) !== true) {
            return $redirect;
        }

        $types =
            Type::whereIn('chapter_type', ['ship', 'station'])->orderBy('chapter_description')->get(
                ['chapter_type', 'chapter_description']
            );
        $chapterTypes = [];

        foreach ($types as $chapterType) {
            $chapterTypes[$chapterType->chapter_type] = $chapterType->chapter_description;
        }

        $chapterTypes = ['' => 'Select a Ship Type'] + $chapterTypes;

        $chapters = Chapter::getChaptersByType('fleet');

        asort($chapters);

        return View::make(
            'chapter.create',
            [
                'chapterTypes' => $chapterTypes,
                'chapter'      => new Chapter,
                'branches'     => Branch::getNavalBranchList(),
                'fleets'       => ['' => 'Select a Fleet'] + $chapters,
            ]
        );
    }

    public function edit(Chapter $chapter)
    {

        if (( $redirect = $this->checkPermissions('EDIT_SHIP') ) !== true) {
            return $redirect;
        }

        $types =
            Type::whereIn('chapter_type', ['ship', 'station'])->orderBy('chapter_description')->get(
                ['chapter_type', 'chapter_description']
            );
        $chapterTypes = [];

        foreach ($types as $chapterType) {
            $chapterTypes[$chapterType->chapter_type] = $chapterType->chapter_description;
        }

        $chapterTypes = ['' => 'Select a Ship Type'] + $chapterTypes;

        $chapters = array_merge(
            Chapter::getChaptersByType('fleet'),
            Chapter::getChaptersByType('task_force'),
            Chapter::getChaptersByType('task_group'),
            Chapter::getChaptersByType('squadron'),
            Chapter::getChaptersByType('division')
        );

        asort($chapters);

        $crew = User::where('assignment.chapter_id', '=', (string)$chapter->_id)->get();

        return View::make(
            'chapter.edit',
            [
                'chapterTypes' => $chapterTypes,
                'chapter'      => $chapter,
                'chapterList'  => $chapters,
                'branches'     => Branch::getNavalBranchList(),
                'numCrew'      => count($crew),
            ]
        );
    }

    public function update(Chapter $chapter)
    {
        if (( $redirect = $this->checkPermissions('EDIT_SHIP') ) !== true) {
            return $redirect;
        }

        $validator = Validator::make($data = Input::all(), Chapter::$updateRules);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        unset( $data['_method'], $data['_token'] );

        if (empty( $data['decommission_date'] ) === false &&
            empty( $data['commission_date'] ) === false
        ) {
            // Figure out if the ship is in commission or not

            if (strtotime($data['commission_date']) > strtotime($data['decommission_date'])) {
                // Commission date is newer than decommission date
                unset( $data['decommission_date'] );
                $chapter->decommission_date = '';
            } else {
                // Decommission date is newer
                unset( $data['commission_date'] );
                $chapter->commission_date = '';
            }
        }

        foreach ($data as $k => $v) {
            if (empty( $data[$k] ) === false) {
                $chapter->$k = $v;
            }
        }

        $this->writeAuditTrail(
            (string)Auth::user()->_id,
            'update',
            'chapters',
            (string)$chapter->_id,
            $chapter->toJson(),
            'ChapterController@update'
        );

        $chapter->save();;

        Cache::flush();

        return Redirect::route('chapter.index');
    }

    /**
     * Save a newly created chapter
     *
     * @return Responsedb.
     */
    public function store()
    {
        if (( $redirect = $this->checkPermissions('COMMISSION_SHIP') ) !== true) {
            return $redirect;
        }

        $validator = Validator::make($data = Input::all(), Chapter::$rules);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        foreach ($data as $k => $v) {
            if (empty( $data[$k] ) === true) {
                unset( $data[$k] );
            }
        }

        $this->writeAuditTrail(
            (string)Auth::user()->_id,
            'create',
            'chapters',
            null,
            json_encode($data),
            'ChapterController@store'
        );

        Chapter::create($data);

        return Redirect::route('chapter.index');
    }

    public function decommission(Chapter $chapter)
    {
        if (( $redirect = $this->checkPermissions('DECOMMISSION_SHIP') ) !== true) {
            return $redirect;
        }

        $crew = User::where('assignment.chapter_id', '=', (string)$chapter->_id)->get();

        return View::make('chapter.confirm-decommission', ['chapter' => $chapter, 'numCrew' => count($crew),]);
    }

    /**
     * Remove the specified Chapter.
     *
     * @param  $chapterID
     *
     * @return Response
     */
    public function destroy(Chapter $chapter)
    {
        if (( $redirect = $this->checkPermissions('DECOMMISSION_SHIP') ) !== true) {
            return $redirect;
        }

        $chapter->commission_date = '';
        $chapter->decommission_date = date('Y-m-d');

        $this->writeAuditTrail(
            (string)Auth::user()->_id,
            'update',
            'chapters',
            (string)$chapter->_id,
            $chapter->toJson(),
            'ChapterController@destroy'
        );

        $chapter->save();

        return Redirect::route('chapter.index');
    }

    public function commandTriadReport()
    {
        if (( $redirect = $this->checkPermissions('TRIAD_REPORT') ) !== true) {
            return $redirect;
        }

        //get list of ships and stations
        $results = Chapter::where('chapter_type', '=', 'ship')->orWhere('chapter_type', '=', 'station')->get();

        $output[] = "'Member Number','Fleet','Name','Rank','Date of Rank','Billet','Ship','Highest Exam','Exam Date'\n";
        foreach ($results as $chapter) {
            $commandTriad = $chapter->getCommandCrew();

            foreach ($commandTriad as $billet => $user) {
                if (is_object($user) && get_class($user) == 'User') {
                    switch (substr($user->rank['grade'], 0, 1)) {
                        case 'E':
                            $exam = $user->getHighestEnlistedExam();
                            break;
                        case 'W':
                            $exam = $user->getHighestWarrantExam();
                            break;
                        case 'O':
                        case 'F':
                            $exam = $user->getHighestOfficerExam();
                            break;
                        default:
                            $exam = [];
                    }

                    if (count($exam) > 0) {
                        $examID = key($exam);
                        $examInfo = "$examID,{$exam[$examID]['date']}";
                    } else {
                        $examInfo = ",";
                    }

                    $output[] =
                        "{$user->member_id},{$chapter->getAssignedFleet()},{$user->getFullName()},{$user->rank['grade']},{$user->rank['date_of_rank']},$billet,$chapter->chapter_name,$examInfo\n";
                }
            }
        }

        /**
         * 1) Get list of Ship and Stations
         * 2) Add column headers as first entry in $output array
         * 3) iterate through list of ships and stations
         * 4) Get Command Triad for ship/station
         * 5) $tmp = '{$chapter->chapter_name},{$chapter->hull_number},';
         */

        //get Command triads for each ship/station

        //get exams for each member of each command triads

        if (ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }
        header('Pragma: public');   // required
        header('Expires: 0');       // no cache
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s'));
        header('Cache-Control: private', false);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="triad_report.csv"');
        header('Content-Transfer-Encoding: base64');
        header('Connection: close');
        foreach ($output as $line) {
            print $line;
        }
        exit();
    }

    public function exportRoster(Chapter $chapter)
    {
        if (( $redirect = $this->checkPermissions('DUTY_ROSTER') ) !== true) {
            return $redirect;
        }

        $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());
        $csv->setNewline("\r\n");
        $crew = $chapter->getAllCrew();

        $headers =
            [
                'RMN Number',
                'First Name',
                'Middle Name',
                'Last Name',
                'Suffix',
                'Email Address',
                'Phone Number',
                'Address 1',
                'Address 2',
                'City',
                'State/Province',
                'Postal Code',
                'Country',
                'Rank',
                'Date of Rank',
                'Branch',
                'Billet'
            ];

        $csv->insertOne($headers);

        foreach ($crew as $member) {
            $csv->insertOne(
                [
                    $member->member_id,
                    $member->first_name,
                    $member->middle_name,
                    $member->last_name,
                    $member->suffix,
                    $member->email_address,
                    $member->phone_number,
                    $member->address1,
                    $member->address2,
                    $member->city,
                    $member->state_province,
                    $member->postal_code,
                    $member->country,
                    $member->rank['grade'],
                    $member->rank['date_of_rank'],
                    $member->branch,
                    $member->getBillet('primary')
                ]
            );
        }

        $csv->output(date('Y-m-d') . '_' . str_replace(' ', '_', $chapter->chapter_name) . '_roster.csv');
    }
}
