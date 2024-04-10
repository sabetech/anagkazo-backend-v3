@extends('v2.layouts.client.main')
@section('content')
<div class="content-box mt-20 fadeInUp animated">
    <div class="row no-gutters p-15 align-items-center">

        <div class="col-12 box-title">
            <!-- <img src="{{ url($student->photo_url) }}" style="width:60%"> -->
        </div>

    </div>
    <div class="px-15 pb-15">

        <div class="alert alert-success mb-0" role="alert">
            <h4 class="alert-heading">
                Hello {{ $student->name }}, You have {{ $points }} Points.

            </h4>
            <h4>Download the Anagkazo App
                to Check your current Standing.</h4>
            <hr>
            <p class="">
                <a href="https://play.google.com/store/apps/details?id=com.test.dx">
                    <img src="{{ url('imgs/google_play_icon.png') }}" width="300">
                </a>
            </p>
        </div>



    </div>

</div>

@endsection
