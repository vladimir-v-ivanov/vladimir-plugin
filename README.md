# Vladimir-Plugin

This plugins allows to create a custom page with table of users loaded from external REST API.

Since this is just a test task solution, some functionality were not implemented, such as the settings page in the admin panel, because there were no requirements in the task.
  
## Installation
1. Clone this repository to your HTTP root folder
2. Run "composer install" command in the root folder (make sure you have write access)
3. Run "composer update" command in the root folder
4. Login to Admin Panel and activate the "Vladimir-Plugin" plugin
5. Navigate to the site.com/wp/?p=user_list or site.com/wp/user_list if you are using SEF urls

## Tests
To run automated tests:
1. Naviage to the ".../wp/wp-content/plugins/valdimir-plugin/tests"
2. Execute command "php vladimir-plugin-test.php"
