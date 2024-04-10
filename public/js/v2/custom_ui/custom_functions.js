$(document).ready(function() {
  $(".class-select").select2({
    placeholder: "Filter By Class",
    ajax: {
      url: "ajax/classes",
      delay: 250,
      data: function(params) {
        var query = {
          search: params.term,
        };
        return query;
      },
      processResults: function(data) {
        return {
          results: data,
        };
      },
    },
  });

  $(".region-filter").select2({
    placeholder: "Filter By Area",
    ajax: {
      url: "ajax/regions",
      delay: 250,
      data: function(params) {
        var query = {
          search: params.term,
        };
        return query;
      },
      processResults: function(data) {
        return {
          results: data,
        };
      },
    },
  });

  $(".center-select").select2({
    placeholder: "Filter By Center",
    ajax: {
      url: "ajax/centers",
      delay: 250,
      data: function(params) {
        var query = {
          search: params.term,
        };
        return query;
      },
      processResults: function(data) {
        return {
          results: data,
        };
      },
    },
  });

  $(".event-filter").select2({
    placeholder: "Filter By Event",
  });

  $(".event-select").select2({
    placeholder: "Select Event",
  });

  let preprocessURL = function(urlParam, urlParamValue) {
    let currentUrlParamString = window.location.search;
    const urlParams = new URLSearchParams(currentUrlParamString);
    let finalUrlStr =
      "/" +
      document.URL.split("/")[3].substring(
        0,
        document.URL.split("/")[3]
          .toString()
          .indexOf("?") !== -1
          ? document.URL.split("/")[3]
              .toString()
              .indexOf("?")
          : document.URL.split("/")[3].toString().length
      ) +
      "?";

    //let finalUrlStr = "/students?";

    if (urlParam == "date_select") {
      //if url param is date_select it means that user is trying to change the date_select param value
      finalUrlStr += "date_select=" + urlParamValue;
      finalUrlStr += constructUrl();
    } else {
      //if date is already in the url and another filter paramter is being selected ...
      finalUrlStr +=
        "date_select=" +
        (urlParams.get("date_select") || moment().format("YYYY-MM-DD"));
    }

    function constructUrl() {
      if (urlParams.get("filter_class"))
        return "&filter_class=" + urlParams.get("filter_class");
      if (urlParams.get("filter_region"))
        return "&filter_region=" + urlParams.get("filter_region");
      if (urlParams.get("filter_center"))
        return "&filter_center" + urlParams.get("filter_region");
      return "";
    }

    //these are mutually exclusive
    if (urlParam == "filter_class") {
      //if url param is filter_class it means that user is trying to change the filter_class param value
      finalUrlStr += "&filter_class=" + urlParamValue;
    }

    if (urlParam == "filter_center") {
      finalUrlStr += "&filter_center=" + urlParamValue;
    }

    if (urlParam == "filter_region") {
      finalUrlStr += "&filter_region=" + urlParamValue;
    }
    return window.location.origin + finalUrlStr;
  };

  $(".date-select").on("apply.daterangepicker", function(ev, picker) {
    let dateSelectValue = picker.startDate.format("YYYY-MM-DD");
    window.location = preprocessURL("date_select", dateSelectValue);
  });

  $(".class-select").change(function() {
    window.location = preprocessURL(
      "filter_class",
      $(this)
        .text()
        .trim()
    ); //window.location.origin + "/home?filter_class="+ ;
  });

  $(".region-filter").change(function() {
    window.location = preprocessURL(
      "filter_region",
      $(this)
        .text()
        .trim()
    ); //window.location.origin + "/home?filter_region="+
  });

  $(".center-select").change(function() {
    window.location = preprocessURL(
      "filter_center",
      $(this)
        .text()
        .trim()
    ); //window.location.origin + "/home?filter_center="+$(this).text().trim();
  });

  $(".report-type-filter").select2();

  /////+=========================++++======== fire this function when data is ready .... ===========================
  let dataReadyCallback = function(responseData, status, target) {
    console.log(responseData);
    /*
          target =>
              {
            chart: {
              Su2
              }
            }
        */
  };
});
