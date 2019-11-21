'use strict';

var checkbox = document.querySelector('.show_completed');
var taskCheckboxes = document.querySelectorAll('.task__checkbox');

if (checkbox) {
  checkbox.addEventListener('change', function (evt) {
    var is_checked = +evt.target.checked;

    var searchParams = new URLSearchParams(window.location.search);
    searchParams.set('show_completed', is_checked);

    window.location = '/?' + searchParams.toString();
  });
}

if (taskCheckboxes) {
  taskCheckboxes.forEach(function (taskCheckbox) {
    taskCheckbox.addEventListener('change', function (evt) {
      var is_checked = +evt.target.checked;

      var searchParams = new URLSearchParams(window.location.search);
      searchParams.set('complete_task', evt.target.value + '_' + is_checked);

      window.location = '/?' + searchParams.toString();
    });
  });
}

flatpickr('#date', {
  enableTime: false,
  dateFormat: "Y-m-d",
  locale: "ru"
});
