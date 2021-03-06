@extends('layout')
<?php
$chapterType = Chapter::getChapterType($detail->_id);

if (( in_array($chapterType, ['task_force', 'task_group', 'squadron', 'division', 'ship', 'station']) === true ) &&
        isset( $detail->hull_number ) === true
) {
    $hull_number = ' (' . $detail->hull_number . ')';
} else {
    $hull_number = '';
}

switch ($detail->chapter_type) {
    case 'task_force':
    case 'task_group':
        $type = str_replace('_', ' ', $detail->chapter_type);
        break;
    case 'exp_force':
        $type = 'Expeditionary Force';
        break;
    default:
        $type = $detail->chapter_type;
}
?>
@section('pageTitle')
    {{ $detail->chapter_name }} {{$hull_number}}
@stop

@section('content')
    <div class="row">
        <?php
        if ($detail->chapter_type == 'fleet') {
            $path = '/crests/fleet/';
        } else {
            $path = '/crests/';
        }
        ?>
        @if(file_exists(public_path() . $path . $detail->hull_number . '.svg') === true)
            <div class="columns small-2">
                <img class='crest' src="{{asset($path . $detail->hull_number . '.svg')}}"
                     alt="{{$detail->chapter_name}} Crest"
                     width="100px" data-src="{{asset($path . $detail->hull_number . '.svg')}}">
            </div>
        @endif
        <div class="columns small-10 end">
            <h2 class="Incised901Bold padding-5">{{ $detail->chapter_name }} {{$hull_number}}</h2>

            <h3 class="Incised901Bold padding-5">{{ isset($detail->ship_class) ? $detail->ship_class . ' Class' : '' }}</h3>
        </div>
    </div>



    <div class="row padding-5">
        <div class="small-2 columns Incised901Light">
            Chapter Name:
        </div>
        <div class="small-10 columns Incised901Light">
            {{ $detail->chapter_name }} {{$hull_number}} @if(in_array($detail->chapter_type, ['ship', 'station']) === true && empty($detail->idcards_printed) && $permsObj->hasPermissions(['ID_CARD']))
                <a class="fi-credit-card green size-24" href="/id/bulk/{{$detail->id}}" title="Print ID Cards"></a>
                <a class="fi-check green size-24" href="/id/markbulk/{{$detail->id}}" title="Mark ID Cards as printed"
                   onclick="return confirm('Mark ID cards as printed for this chapter?')"></a>
            @elseif(in_array($detail->chapter_type, ['ship', 'station']) === true && !empty($detail->idcards_printed) && $permsObj->hasPermissions(['ID_CARD']))
                <span class="fi-print size-24" title="ID Cards printed"></span> @endif
        </div>
    </div>
    <div class="row padding-5">
        <div class="small-2 columns Incised901Light">
            Chapter Type:
        </div>
        <div class="small-10 columns Incised901Light">
            {{ucwords($type)}}
            @if(in_array($detail->chapter_type, ['ship', 'station']) === true)
                @if(empty($detail->decommission_date) === true)
                    (Commissoned {{date('F jS, Y', strtotime($detail->commission_date))}})
                @else
                     (Decommissoned {{date('F jS, Y', strtotime($detail->decommission_date))}})
                @endif
            @endif
        </div>
    </div>
    @if($higher)
        <?php
        $higherType = Chapter::getChapterType($higher->_id);

        if (( in_array(
                                $higherType,
                                ['task_force', 'task_group', 'squadron', 'division', 'ship', 'station']
                        ) === true ) && isset( $higher->hull_number ) === true
        ) {
            $higherHullNumber = ' (' . $higher->hull_number . ')';
        } else {
            $higherHullNumber = '';
        }
        ?>
        <div class="row padding-5">
            <div class="small-2 columns Incised901Light">
                Component of:
            </div>
            <div class="small-10 columns Incised901Light">
                <a href="{{route('chapter.show', $higher->_id)}}">{{$higher->chapter_name}} {{$higherHullNumber}}</a>
            </div>
        </div>
    @endif
    @if (count($includes) > 0)
        <div class="row padding-5">
            <div class="small-2 columns Incised901Light">
                Elements:
            </div>
            <div class="small-10 columns Incised901Light">
                @foreach($includes as $chapter)
                    <?php
                    $elementType = Chapter::getChapterType($chapter['_id']);

                    if (( in_array(
                                            $elementType,
                                            ['task_force', 'task_group', 'squadron', 'division', 'ship', 'station']
                                    ) === true ) && isset( $chapter['hull_number'] ) === true
                    ) {
                        $elementHullNumber = ' (' . $chapter['hull_number'] . ')';
                    } else {
                        $elementHullNumber = '';
                    }
                    ?>
                    <a href="{{route('chapter.show', [$chapter['_id']])}}">{{ $chapter['chapter_name'] }}{{$elementHullNumber}}</a>
                    &nbsp;<br/>
                @endforeach
            </div>
        </div>
    @endif
    @if (count($command) > 0)
        <div class="row padding-5">
            <div class="small-2 columns Incised901Light">
                Command Crew:
            </div>
            <div class="small-10 columns Incised901Light">
                <div style="padding-bottom: 2px">
                    @if($detail->chapter_type == 'fleet')Fleet Commander:
                    @elseif($detail->chapter_type == 'bureau')
                        <?php
                        $spaceLord = new NumberFormatter('en_US', NumberFormatter::SPELLOUT);
                        $spaceLord->setTextAttribute(NumberFormatter::DEFAULT_RULESET, "%spellout-ordinal");
                        ?>
                        {{ucfirst($spaceLord->format($detail->hull_number))}} Space Lord:
                    @else
                        Commanding Officer:
                    @endif
                    @if(isset($command['CO']))
                        @if($permsObj->hasPermissions(['VIEW_MEMBERS']) || $permsObj->isInChainOfCommand($detail->getChapterIdWithParents()) === true)
                            <a href="{{ route('user.show' , [(string)$command['CO']->_id]) }}">
                                @endif
                                {{ $command['CO']->getGreeting() }} {{ $command['CO']->first_name }}{{ isset($command['CO']->middle_name) ? ' ' . $command['CO']->middle_name : '' }} {{ $command['CO']->last_name }}{{ !empty($command['CO']->suffix) ? ' ' . $command['CO']->suffix : '' }}
                                , {{$command['CO']->branch}}
                                @if($permsObj->hasPermissions(['VIEW_MEMBERS']) || $permsObj->isInChainOfCommand($detail->getChapterIdWithParents()) === true)
                            </a>
                            @if($command['CO']->hasNewExams()) <span class="fi-star red"
                                                                     title="New Exams Posted">&nbsp;</span> @endif
                        @endif
                    @else
                        N/A
                    @endif
                </div>
                @if(Auth::user()->getAssignedShip() == $detail->id || $permsObj->hasPermissions(['VIEW_MEMBERS'] || $permsObj->isInChainOfCommand($detail->getChapterIdWithParents()) === true))
                    <div style="padding-bottom: 2px;">
                        @if($detail->chapter_type == 'fleet')Deputy Fleet Commander:
                        @elseif($detail->chapter_type == 'bureau')
                            Deputy {{ucfirst($spaceLord->format($detail->hull_number))}} Space Lord:
                        @else
                            Executive Officer:
                        @endif
                        @if( isset( $command['XO'] ) )
                            @if($permsObj->hasPermissions(['VIEW_MEMBERS']) || $permsObj->isInChainOfCommand($detail->getChapterIdWithParents()) === true)
                                <a href="{{ route('user.show' , [(string)$command['XO']->id]) }}">
                                    @endif
                                    {{ $command['XO']->getGreeting() }} {{ $command['XO']->first_name }}{{ isset($command['XO']->middle_name) ? ' ' . $command['XO']->middle_name : '' }} {{ $command['XO']->last_name }}{{ !empty($command['XO']->suffix) ? ' ' . $command['XO']->suffix : '' }}
                                    , {{$command['XO']->branch}}
                                    @if($permsObj->hasPermissions(['VIEW_MEMBERS']) || $permsObj->isInChainOfCommand($detail->getChapterIdWithParents()) === true)
                                </a>
                                @if($command['XO']->hasNewExams()) <span class="fi-star red" title="New Exams Posted">&nbsp;</span> @endif
                            @endif
                        @else
                            N/A
                        @endif
                    </div>
                    <div style="padding-bottom: 2px;">
                        <?php
                        switch ($detail->chapter_type) {
                            case 'shuttle':
                            case 'section':
                            case 'squad':
                            case 'platoon':
                            case 'battalion':
                            case 'company':
                                echo "Gunny: ";
                                break;
                            case 'fleet':
                                echo "Fleet Bosun: ";
                                break;
                            case 'barracks':
                            case 'bivouac':
                            case 'fort':
                            case 'outpost':
                            case 'planetary':
                            case 'theater':
                                echo 'NCOIC: ';
                                break;
                            case 'bureau':
                                echo 'Chief of Staff: ';
                                break;
                            default:
                                echo "Bosun: ";
                        }
                        ?>
                        @if( isset( $command['BOSUN'] ) )
                            @if($permsObj->hasPermissions(['VIEW_MEMBERS']) || in_array(Auth::user()->duty_roster,$detail->getChapterIdWithParents()) === true)
                                <a href="{{ route('user.show' , [(string)$command['BOSUN']->id]) }}">
                                    @endif
                                    {{ $command['BOSUN']->getGreeting() }} {{ $command['BOSUN']->first_name }}{{ isset($command['BOSUN']->middle_name) ? ' ' . $command['BOSUN']->middle_name : '' }} {{ $command['BOSUN']->last_name }}{{ !empty($command['BOSUN']->suffix) ? ' ' . $command['BOSUN']->suffix : '' }}
                                    , {{$command['BOSUN']->branch}}
                                    @if($permsObj->hasPermissions(['VIEW_MEMBERS']) || in_array(Auth::user()->duty_roster,$detail->getChapterIdWithParents()) === true)
                                </a>
                                @if($command['BOSUN']->hasNewExams()) <span class="fi-star red"
                                                                            title="New Exams Posted">&nbsp;</span> @endif
                            @endif
                        @else
                            N/A
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif
    @if (count($crew) > 0 && (Auth::user()->getAssignedShip() == $detail->id || $permsObj->hasPermissions(['VIEW_MEMBERS']) || $permsObj->isInChainOfCommand($detail->getChapterIdWithParents()) === true))
        <div class="row padding-5">
            <div class="small-2 columns Incised901Light">
                Crew Roster:
                @if($permsObj->hasPermissions(['VIEW_MEMBERS']) || $permsObj->isInChainOfCommand($detail->getChapterIdWithParents()) === true)
                    <br /><a href="{{route('roster.export', [$detail->id])}}">Download Roster</a>
                @endif
            </div>
            <div class="small-10 columns Incised901Light">
                <table id="crewRoster" class="compact row-border" width="75%">
                    <thead>
                    <tr>
                        @if($permsObj->hasPermissions(['VIEW_MEMBERS']) || $permsObj->isInChainOfCommand($detail->getChapterIdWithParents()) === true)
                            <th class="center" width="20"><span class="fi-star red"
                                                                title="New Exams Posted">&nbsp;</span></th>
                        @endif
                        <th>Name</th>
                        @if($permsObj->hasPermissions(['VIEW_MEMBERS']) || $permsObj->isInChainOfCommand($detail->getChapterIdWithParents()) === true)
                            <th>ID #</th>
                        @endif
                        <th>Rank</th>
                        <th style="text-align: center">Time in Grade</th>
                        <th>Billet</th>
                        <th>Branch</th>
                        @if($permsObj->hasPermissions(['VIEW_MEMBERS']) || $permsObj->isInChainOfCommand($detail->getChapterIdWithParents()) === true)
                            <th>City</th>
                            <th>State / Province</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($crew as $member)
                        <tr>
                            @if($permsObj->hasPermissions(['VIEW_MEMBERS']) || $permsObj->isInChainOfCommand($detail->getChapterIdWithParents()) === true)
                                <td class="center">@if($member->hasNewExams()) <span class="fi-star red"
                                                                                     title="New Exams Posted">&nbsp;</span> @endif
                                </td>
                            @endif
                            <td>
                                @if($permsObj->hasPermissions(['VIEW_MEMBERS']) || $permsObj->isInChainOfCommand($detail->getChapterIdWithParents()) === true)
                                    <a href="{{ route('user.show', [$member->_id]) }}">
                                        @endif
                                        {{ $member->last_name }}{{ !empty($member->suffix) ? ' ' . $member->suffix : '' }}
                                        , {{ $member->first_name }}{{ isset($member->middle_name) ? ' ' . $member->middle_name : '' }}
                                        @if($permsObj->hasPermissions(['VIEW_MEMBERS']) || $permsObj->isInChainOfCommand($detail->getChapterIdWithParents()) === true)
                                    </a>

                                @endif
                            </td>
                            @if($permsObj->hasPermissions(['VIEW_MEMBERS']) || $permsObj->isInChainOfCommand($detail->getChapterIdWithParents()) === true)
                                <td>{{$member->member_id}}</td>
                            @endif
                            <td>{{$member->rank['grade']}} <br/>{{ $member->getGreeting() }} </td>
                            <td>{{is_null($tig = $member->getTimeInGrade(true))?'N/A':$tig}}</td>
                            <td>{{ $member->getBilletForChapter($detail->id) }}</td>
                            <td>{{$member->branch}}</td>
                            @if($permsObj->hasPermissions(['VIEW_MEMBERS']) || $permsObj->isInChainOfCommand($detail->getChapterIdWithParents()) === true)
                                <td>{{$member->city}}</td>
                                <td>{{$member->state_province}}</td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        @if($permsObj->hasPermissions(['VIEW_MEMBERS']) || $permsObj->isInChainOfCommand($detail->getChapterIdWithParents()) === true)
                            <th class="center"><span class="fi-star red" title="New Exams Posted">&nbsp;</span></th>
                        @endif
                        <th>Name</th>
                        @if($permsObj->hasPermissions(['VIEW_MEMBERS']) || $permsObj->isInChainOfCommand($detail->getChapterIdWithParents()) === true)
                            <th>ID #</th>
                        @endif
                        <th>Rank</th>
                        <th style="text-align: center">Time in Grade</th>
                        <th>Billet</th>
                        <th>Branch</th>
                        @if($permsObj->hasPermissions(['VIEW_MEMBERS']) || $permsObj->isInChainOfCommand($detail->getChapterIdWithParents()) === true)
                            <th>City</th>
                            <th>State / Province</th>
                        @endif
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif

@stop
@section('scriptFooter')
    <script type="text/javascript">
        $('#crewRoster').DataTable({
            "autoWidth": true,
            "pageLength": 25,
            "language": {
                "emptyTable": "No crew members found"
            },
            "$UI": true
            @if($permsObj->hasPermissions(['VIEW_MEMBERS']) || $permsObj->isInChainOfCommand($detail->getChapterIdWithParents()) === true)
                ,"order": [[0, "desc"]]
            @endif
        });
    </script>
@stop