<?php
# makes every GitLab repository private

$access_token = require 'access_token.php';

function make_gitlab_request(string $access_token, string $uri, string $method = 'GET'): string {
	return shell_exec("curl --header 'PRIVATE-TOKEN: $access_token' -X $method https://gitlab.com/api/v4$uri");
}

foreach (json_decode(make_gitlab_request($access_token, '/projects'), true) as $project)
	if ($project['visibility'] != 'private')
		# unfortunately, due to GitLab's fucky API, you only get '403 Forbidden' when trying to change visibility
		# of *your* project (from somewhere else, e.g. GitHub) that *you* imported into *your* account
		make_gitlab_request($access_token, '/projects/'.$project['id'].'?visibility=private', 'PUT');

echo 'done!';