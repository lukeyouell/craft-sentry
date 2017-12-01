var authToken = '#settings-authToken';
var projects = '#settings-project';
var projectsRefresh = '#settings-projectBtn';
var keys = '#settings-clientDsn';

$(projects).on('change', function() {

  updateKeys();

});

$(projectsRefresh).on('click', function() {

  updateProjects();

});

function updateProjects() {

  var spinner = '#settings-projectSpinner';

  $(projectsRefresh).addClass('disabled');
  $(projects).empty();
  $(spinner).removeClass('hidden');

  Craft.postActionRequest('sentry/sentry/list-projects', { authToken: $(authToken).val() }, function(response) {

    if (response.error) {

      $(keys).empty();

      alert(response.reason);

    } else if (response.length > 0) {

      $(projects).append('<option value="">Select a Project</option>');

      for (var i in response) {

        $(projects).append('<option value="'+response[i].organization.slug+'/'+response[i].slug+'">'+response[i].name+'</option>');

      }

    }

    updateKeys();

    $(projectsRefresh).removeClass('disabled');
    $(spinner).addClass('hidden end');

  });

}

function updateKeys() {

  var spinner = '#settings-keySpinner';

  $(keys).empty();
  $(spinner).removeClass('hidden');

  Craft.postActionRequest('sentry/sentry/list-keys', { authToken: $(authToken).val(), project: $(projects).val() }, function(response) {

    if (response.length > 0) {

      for (var i in response) {

        $(keys).append('<option value="'+response[i].dsn.secret+'">'+response[i].name+'</option>');

      }

    }

    $(spinner).addClass('hidden end');

  });

}
