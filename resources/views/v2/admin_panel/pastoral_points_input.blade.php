@extends('v2.layouts.client.main')
@section('content')
    <div class="auth-wrapper d-flex align-items-center justify-content-center">

        <div class="auth-box bg-white border border-1 p-30 pt-50 rounded text-center fadeInDown animated">
            <img class="fw-65" src="imgs/logo_anagkazo.png" alt="Generic placeholder image">
            <h1>My Pastoral Points</h1>

            <div class="text-size-22 font-weight-normal mt-20 student-name"></div>
            <div class="mt-5">Make sure you have internet connection for this activity.</div>

            <form class="point-form mt-30" action="{{ url('/v2/pastoral_points_form') }}" method="POST">
                {{ csrf_field() }}

                <div class="row">
                    <div class='col-md-12' style="">
                        <div class="form-group">
                            <input id="student_info" type="hidden" name="student_info" value="">
                            <select name="student_id" class="student-select form-control" style="width:100%;float:left">

                            </select>
                        </div>
                    </div>
                </div>

                <br />
                <br />

                <div class="row">

                    <div class='col-md-12' style="">

                        <label>
                            <h4>Pastoral Point System</h4>
                        </label>
                        <label>Check all that apply</label>
                    </div>

                    <br />

                    <div class="btn-lg" style="float:right;display:none;">
                        <label>Total Point: </label> <strong id="total_point">val</strong>
                    </div>
                    <br />
                    <div>
                        <button class="btn btn-primary btn-lg" name="save_attendance" type="submit"
                            style="display:none">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.student-select').select2({
                placeholder: 'Choose your name',
                ajax: {
                    url: 'public/students/ajax/search-student-only',
                    delay: 250,
                    data: function(params) {
                        var query = {
                            search: params.term,
                        }
                        return query;
                    },
                    processResults: function(data) {

                        return {
                            results: data
                        };
                    }
                }
            });

            let div = $("#pastoral_points").html();
            let totalVal = 0

            $('.student-select').on("select2:select", function(e) {
                let studentParams = e.params.data;
                $("#student_info").val(e.params.data.text);

                $(".point-form").submit();
            });
        });
    </script>
@endsection
