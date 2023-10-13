Xsmind
==============

## Environments
xsmind.com ( `[test]`(https://test.xsmind.com//), [prd](https://www.xsmind.com/))  

## Database Dumps

Databases are automatically dumped at midnight for our development environment for each site. They are dumped to the following locations:  
  
[xsmind.sql](https://test.xsmind.com/database/xsmind.sql)  

## Installing with Lando

### Before set-up xsmind
1. Make sure you already have lando installed with the latest version (Need to call to ITPS to perform the installation):
    - MacOS https://docs.devwithlando.io/installation/macos.html
    - Linux https://docs.devwithlando.io/installation/linux.html
    - Windows https://docs.devwithlando.io/installation/windows.html
    - From source https://docs.devwithlando.io/installation/source.html
    - Update https://docs.devwithlando.io/installation/updating.html

Only windows users, possible considerations for use wsl with lando check this:
https://nickjanetakis.com/blog/setting-up-docker-for-windows-and-wsl-to-work-flawlessly
https://github.com/lando/lando/issues/564

### Building xsmind-lando for local
1. Clone the repo. `git clone https://github.com/sumeetbiswal/project.git xsmind-lando` (Due to we are using https instead ssh to avoid issues with SPE Network/VPN, when you run `git push` you will need to enter your github.com credentials).
2. Enter xsmind-lando `cd xsmind-lando`.
3. Get a database backup from either this options
    - Acquia Cloud either stage or prod environments.
    - Section above Database Dumps.
4. The DB always should be one of the latest one. Import the latest production database first and test database at a later stage. 
5. Create the database folder: `mkdir database`
6. Move the .sql inside the database folder. (Should be appear only one file with **xsmind** word in the file name e.g. dev-xsmind.sql)
7. Make a copy of lando/lando.env file with the name .env (this file should not be under git.) `cp lando/lando.env .env`
8. Make a copy of example.settings.local.php and rename to settings.local.php `cp docroot/sites/default/example.settings.local.php docroot/sites/default/settings.local.php`
9. Run `make build` command.
10. For the first time the `make build` should run on `outsite of VPN` since it downloads all the containers from the outside network.
11. Wait to finish the build.
12. Run `make ci` to load the local config split (this turns on stage_file_proxy, changes shield settings and other local dev items.)
13. Go to google-chrome to http://xsmind.lndo.site/user

** We need to verify the local config settings b/c shield should be turned off b/c it seems that shield is still on but it is not supposed to be on after the config split settings are synced to the database (remove me once verified).

**Notes** 
Run `make` or `make help` to see all available commands.

## Installing with SPEDD

### Requirements
1. Latest version of docker and docker-compose
2. You should be able to run make commands
    - Linux and Mac Users no more action needed.
    - For windows users use Docker Desktop Edge 2.1.6.1
    - Windows 10 build >1709
    - Install WSL (Windows Subsystem Linux) https://docs.microsoft.com/en-us/windows/wsl/install-win10
    - Use WSL2 https://docs.microsoft.com/en-us/windows/wsl/wsl2-install
    - Configure Docker with WSL https://docs.docker.com/docker-for-windows/wsl-tech-preview/

### Building xsmind-spedd for local
1. Clone the repo. `git clone https://github.com/sumeetbiswal/project.git xsmind-spedd` (Due to we are using https instead ssh to avoid issues with SPE Network/VPN, when you run `git push` you will need to enter your github.spehosting.com credentials).
2. `cd xsmind-spedd`.
3. Get a database backup from either this options
    - Acquia Cloud either stage or prod environments.
    - Section above Database Dumps.
4. Create the database folder: `mkdir database/backup`
5. Move the .sql inside the database folder. (the file should placed and named as database/backup/backup.sql)
6. Make a copy of spedd/spedd.env file with the name .env (this file should not be under git.) `cp spedd/spedd.env .env`
7. Make a copy of example-spedd.settings.local.php and rename to settings.local.php `cp docroot/sites/default/example-spedd.settings.local.php docroot/sites/default/settings.local.php`
8. Edit the Makefile, comment the lando lines and uncomment the spedd.
9. Run `make init_reverse_proxy` command.
10. Run `make build` command.
11. Wait to finish the build.
12. Go to google-chrome to http://xsmind.spe.localhost/user

**Notes**
Only works under google chrome now.
SAMLIDP for local does not work under SPEDD.
Run `make` or `make help` to see all available commands.

### SPEDD Workflow
1. If you already build, you can run `make stop` to stop containers.
2. `make start` to continue working.
3. Re-import the DB and apply updates and configs `make rebuild`
4. Destroy containers for app `make destroy`
5. Clean all `make destroy-all`
6. Run make for more info.
7. you can add custom command on xsmind-commands.mk file.

## Use HTTP on local not HTTPS
For local and debug please use only http, for that please comment the lines 80, 81 nad 82 in docroot/.htaccess
And please DO NOT commit that change.

## Rebuild drupal instance
For further rebuild process, you only need to run one command, because all is already prepared.
To rebuild from latest state of Prod database + current dev changes, run only:

`make build` 

## Update db
Only needs to download the version of db from Acquia Cloud either stage or prod environments, and replace it in database folder.
Then run `make rebuild`

## Simple SAML for local

By default when you run `make build` or `make up` or `make rebuild` or `lando start` the container for simplesaml will up, to check the IDP run `make getsaml` command or visit http://samlidp.xsmind.lndo.site:8080/simplesaml
Only runs for lando.

## Lando + Xdebug + PHPStorm

1. By default for xsmind xdebug is enabled, you can check in .lando.yml file appears `xdebug: true` under services and appserver.
2. Go to PHPStorm > Preferences > Languages and Frameworks > PHP
3. Select in PHP Language level: 7.2
4. Leave in black Interpreter.
5. In `Include path` section, remove all items added, and add (+) only 
    - For Mac users: `/Users/<yourUserName>/.lando/compose/xsmind`
    - For Windows: `path/to/.lando/compose/xsmind`
6. Apply and Save
7. Go to Menu > Run > Start listening for PHP Debug Connections (or click in telephone icon)
8. Select a breakpoint and happy debugging... 
9. Xdebug should be run under http not https to avoid any weird issues

## Check the quality of the code.

There are two commands to check the code using phpcs.
Please check the code and apply the code standards before open the PR.

Will run only for modules/custom:

```
# Run for all custom modules (modules/custom/*)
make phpcs

# Run for specific module (modules/custom/company)
make phpcs company

# Run for all custom themes (themes/custom/*)
make phpcs-theme

# Run for specific custom theme (themes/custom/singleportal)
make phpcs-theme singleportal
```

## Migration to https://github.com/sumeetbiswal/project.git

### Continue working on the same instance with the same remote. (Recommended)
 1. `cd path/xsmind/project`
 2. `git remote set-url origin https://github.com/sumeetbiswal/project.git`
 3. Continue with the normal workflow.

### Continue working on the same instance with new remote.
 1. `cd path/xsmind/project`
 2. `git remote add scm-isg https://github.com/sumeetbiswal/project.git`
 3. Use `git push/pull scm-isg branch-name`

## GitFlow and Conventions

We are using GitFlow to contribute the project, please check the documentation
https://datasift.github.io/gitflow/IntroducingGitFlow.html

As per SPE Policy you should create branches with following naming conventions

 1.  Feature branch - For any new features or enhancements, branch name should start with feature/ or feature_
 2.  Release branch - For any new releases , branch name should start with release/ or release_
 3.  QA branch - For any QA testing , branch name should start with qa/ or qa_
 4.  Hotfix branch  - For production bug fixes, branch name should start with hotfix/ or hotfix_
 5.  UAT branch - For any UAT testing , branch name should start with uat/ or uat_
 6.  Bugfix branch - For QA/UAT/Release bug fixes, branch n ame should start withbugfix/ or bugfix_

## CHANGELOG

To make it easier for users and contributors to see precisely what notable changes have been made between each release (or version) of xsmind.com.
@See more details on https://keepachangelog.com/en/1.0.0/

Every release must be update the changelog.

Changelog files:
* CHANGELOG-1.x.md