// function subst() {
//     var vars = {};
//     var query_strings_from_url = document.location.search.substring(1).split('&');
//     for (var query_string in query_strings_from_url) {
//         if (query_strings_from_url.hasOwnProperty(query_string)) {
//             var temp_var = query_strings_from_url[query_string].split('=', 2);
//             vars[temp_var[0]] = decodeURI(temp_var[1]);
//         }
//     }
//     var css_selector_classes = ['page', 'frompage', 'topage', 'webpage', 'section', 'subsection', 'date', 'isodate', 'time', 'title', 'doctitle', 'sitepage', 'sitepages'];
//     for (var css_class in css_selector_classes) {
//         if (css_selector_classes.hasOwnProperty(css_class)) {
//             var element = document.getElementsByClassName(css_selector_classes[css_class]);
//             for (var j = 0; j < element.length; ++j) {
//                 element[j].textContent = vars[css_selector_classes[css_class]];
//             }
//         }
//     }
// }

window.addEventListener("load", function () {
  var pdfInfo = {};
  var lastPage = 1;
  var x = document.location.search.substring(1).split("&");

  for (var i in x) {
    var z = x[i].split("=", 2);
    pdfInfo[z[0]] = unescape(z[1]);
  }

  function getPdfInfo() {
    var page = pdfInfo.page || 1;
    var pageCount = pdfInfo.topage || 1;
    document.getElementById("page_current").textContent = page;
    document.getElementById("page_count").textContent = pageCount;
    console.log(document.getElementById("head_current"));
  }

  function setTotal() {
    var page = pdfInfo.page || 1;
    var pageCount = pdfInfo.topage || 1;
    document.getElementById("head_current7").textContent = "FDPPPPPP";
    if (lastPage != pageCount) {
      array = document.getElementById("tabArray");
      console.log(array);

      document.getElementById("head_current8").textContent = page;
      lastPage = pageCount;
    }
  }

  getPdfInfo();
  setTotal();
});
