@extends('layouts.picocss-main')

@section('content')
<h1>Pastoral Point {{ date("Y") }}</h2>
    <div class="parent">
        <div class="mylabel">
           <h3> {{ $student->name }} </h3>
        </div>
    </div>
<label>Pastoral Point: {{ $total_point }}</label>
@endsection
