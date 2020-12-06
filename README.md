# Vladimir-Plugin

This plugins allows to create a custom page with table of users loaded through external REST API.

Since this is just a test task solution, some functionality were not implemented, such as the settings page in the admin panel, because there were no requirements in the task.

The requirement "The content of three mandatory columns must be a link (<a> tag)." also were not implemented because I've decided to implement loading of details in little bit easier way without <a> tags.
  
## Installation
1. Clone this repository to the HTTP root folder
2. Run "composer update" command in the root folder (make sure you have write access)
3. Login to Admin Panel and activate the "Vladimir-Plugin" plugin
4. Navigate to the ".../wp/user_list" page and enjoy!

## Tests
To run automated tests:
1. Naviage to the ".../wp/wp-content/plugins/valdimir-plugin/tests"
2. Execute command "php vladimir-plugin-test.php"
