@extends('layouts.app')

@section('content')
<div class="py-5 container">

    <div class="row">
        @include('dashboard.partials.calendar', [ 'schedules'=>$schedules, 'dates'=>$dates ])
    </div>

</div>
@endsection
