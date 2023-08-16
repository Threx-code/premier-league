<div class="col-sm-5">
    <table class="table">
        <thead class="thead-dark ">
        <tr>
            <th scope="col">Club</th>
            <th scope="col">PTS</th>
            <th scope="col" >W</th>
            <th scope="col">D</th>
            <th scope="col">L</th>
            <th scope="col">GD</th>
            <th scope="col">Opp</th>
            <th scope="col">Wk</th>
        </tr>
        </thead>
        <tbody>
        @if(!empty($leagues))
            @foreach($leagues as $key => $league)
                <tr class="task-row">
                    <td>{{$league->team_name}}</td>
                    <td>{{$league->points}}</td>
                    <td>{{$league->won}}</td>
                    <td>{{$league->drawn}}</td>
                    <td>{{$league->lost}}</td>
                    <td>{{$league->goals_difference}}</td>
                    <td>{{$league->team_to_play}}</td>
                    <td>{{ str_replace(' ','',$league->current_week) }}</td>
                </tr>
                <input type="hidden" value="{{$league->team_id}}" name="team_id[]" />
                <input type="hidden" value="{{$league->week_id}}" name="week_id[]" />
                <input type="hidden" value="{{$league->team_to_play_id}}" name="team_to_play_id[]" />
            @endforeach
        @endif
        </tbody>
    </table>
</div>
<div class="col-4">
    @if(!empty($matches))
    <div class="">
            @php
                $weeks = array_unique(array_column($matches, 'current_week'));
            @endphp
            @foreach($weeks as $week)
            <div class="card-header">
                Match Result for {{ $week ?? '' }}
            </div>
            @endforeach
            <div class="card-body">
                <form method="post" class="" action="">
                    @csrf
                    @foreach($matches as $key => $match)
                        <div class="form-row">
                            <label>{{$match->home_team}}</label>
                            <div class="col"><input type="text" class="form-control" value="{{$match->home_goal}}"></div>
                            <div class="col"><input type="text" class="form-control" value="{{$match->away_goal}}"></div>
                            <label>{{$match->away_team}}</label>
                        </div>

                        <input type="hidden" value="{{$match->home_team_id}}" name="team_id[]" />
                        <input type="hidden" value="{{$match->match_id}}" name="team_id[]" />
                        <input type="hidden" value="{{$match->week_id}}" name="week_id[]" />
                        <input type="hidden" value="{{$match->away_team_id}}" name="team_to_play_id[]" />
                    @endforeach
                </form>
            </div>
        @endif
    </div>

    @csrf
</div>

<div class="col-3">
    <div class="">
        @if(!empty($predictions))
            @php
                $weeks = array_unique(array_column($predictions, 'week_name'));
            @endphp
            @foreach($weeks as $week)
                <div class="card-header">
                    Prediction Result for {{ $week ?? '' }}
                </div>
            @endforeach
            <div class="card-body">
                @foreach($predictions as $key => $prediction)
                    <div class="form-row" style="border-bottom: 0.1px solid #ddd">
                        <div class="col-8"><label>{{$prediction->name}}</label></div>
                        <div class="col-4"><label>{{$prediction->prediction}}%</label></div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>