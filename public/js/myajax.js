var MyAjax = {};

MyAjax.getCentersFromRegion = function(region_id, callbackfn) {
  getDataFromServer("/member/ajax/centers/" + region_id, "", callbackfn);
};

MyAjax.loadMembers = function(searchTerm, callbackfn) {
  getDataFromServer("ajax/members/", "searchterm=" + searchTerm, callbackfn);
};

MyAjax.loadStudentsFromCenter = function(center_id, callbackfn) {
  getDataFromServer("anagkazolive/ajax/students/" + center_id, "", callbackfn);
};

MyAjax.loadStudents = function(searchTerm, callbackfn) {
  getDataFromServer("students/ajax/searchforstudent", searchTerm, callbackfn);
};

MyAjax.loadStudentsPage = function(page, callbackfn) {
  getDataFromServer("students/ajax/pages", "page=" + page, callbackfn);
};

MyAjax.saveStudentPoint = function(
  student_id,
  parameter_id,
  point,
  callbackfn
) {
  postDataToServer(
    "ajax/savepoint",
    { parameter_id: parameter_id, student_id: student_id, point: point },
    callbackfn
  );
};

MyAjax.getDashboardValue = function(context, params, callbackfn) {
  getDataFromServer(context, params, callbackfn);
};

function getDataFromServer(url, params, callbackFxn) {
  $.ajax({
    url: url,
    method: "GET",
    data: params,
    tryCount: 0,
    retryLimit: 2,
    success: function(response) {
      callbackFxn(response);
    },
    error: function(xhr, textstatus, errorThrown) {
      this.tryCount++;

      if (textstatus == "timeout") {
        if (this.tryCount <= this.retryLimit) {
          $.ajax(this);
          return;
        }
      }
      if (xhr.status == 500) {
        $.ajax(this);
        return;
      } else {
        console.log("wierd Error. This shouldn't happen");
      }
    },
  });
}

function postDataToServer(url, params, callbackFxn) {
  $.ajax({
    url: url,
    method: "POST",
    data: params,
    tryCount: 0,
    retryLimit: 5,
    success: function(response) {
      callbackFxn(response);
    },
    error: function(xhr, textstatus, errorThrown) {
      this.tryCount++;

      if (textstatus == "timeout") {
        if (this.tryCount <= this.retryLimit) {
          $.ajax(this);
          return;
        }
      }
      if (xhr.status == 500) {
        $.ajax(this);
        return;
      } else {
        console.log("wierd Error. This shouldn't happen");
      }
    },
  });
}
