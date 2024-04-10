@extends('layouts.picocss-main')

@section('content')
<h1>Pastoral Point {{ date("Y") }}</h2>
<form method="POST" action="{{ route('pastoral_form_submit') }}">

    <div class="parent">
        <div class="mylabel">
           <h1> {{ $student->name }} </h1>
           <input type = "hidden" name = "student_id" value = {{ $student->id }} />
        </div>
    </div>

    @foreach ($student->pastoralPoints as $point)
        {{ csrf_field() }}
        <div class="parent">
            <div class="child">
                {{ $point->parameter }}
            </div>
            @if ($point->point_category !== "Shepherdorial Status")
                <div class="child">
                    @if ($point->data_type === 'boolean')
                        @if ($point->pivot->points > 0)
                            <input type="checkbox" name="{{$point->id}}" value="1" checked>
                        @else
                            <input type="checkbox" name="{{$point->id}}" value="1" >
                        @endif
                    @else
                        @if ($point->pivot->points > 0)
                            <input type="number" name="{{$point->id}}" min=0 value="{{ intval($point->pivot->points / $point->weight) }}"  required>
                        @else
                            <input type="number" name="{{$point->id}}" min=0 value="0" required>
                        @endif
                    @endif
                </div>
            @else
                <div class="child">
                    @if ($point->data_type === 'boolean')
                        <input type="checkbox" name="{{$point->id}}" value="1" disabled>
                    @else
                        <input type="number" name="{{$point->id}}" min=0 value="0"  required readonly>
                    @endif
                </div>
            @endif
        </div>

    @endforeach

    <!-- Button -->
    <button type="submit">Submit</button>

  </form>
  @endsection

