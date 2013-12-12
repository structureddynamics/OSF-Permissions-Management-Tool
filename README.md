OSF-Permissions-Management-Tool
==================================

The Permissions Management Tool (PMT) is a command line tool used to manage access permissions on a OSF Web Services network instance. To list, create and delete access permissions, groups and users.


Installing & Configuring the Permissions Management Tool
-----------------------------------------------------

The Permissions Management Tool can easily be installed on your server using the [OSF-Installer](https://github.com/structureddynamics/Open-Semantic-Framework-Installer):

```bash

  ./osf-installer --install-osf-permissions-management-tool -v
  
```

The PMT is using the [OSF Web Services PHP API](https://github.com/structureddynamics/OSF-Web-Services-PHP-API) library to communicate with any OSF Web Services network instance. If the OSF-WS-PHP-API is not currently installed on your server, then follow these steps to download and install it on your server instance:

```bash

  ./osf-installer --install-osf-ws-php-api -v 

```

Once both packages are installed, you will be ready to use the Permissions Management Tool.

Usage Documentation
-------------------
```
Usage: pmt [OPTIONS]


Usage examples:
    List all existing groups: pmt --list-groups
    List all users of a group: pmt --list-group-users
    List all permissions of a group: pmt --list-group-permissions
    List all permissions of a user: pmt --list-user-permissions
    List all groups of a user: pmt --list-user-groups
    Create a new group: pmt --create-group
    Delete a group: pmt --delete-group
    Register a user to a group: pmt --register-user
    Unregister a user from a group: pmt --unregister-user
    Create a new access for a group: pmt --create-access
    Delete an access record: pmt --delete-access

    Delete all accesses records of a dataset: pmt --delete-dataset-accesses

Options:
-h, --help                                Show this help section

Listing Options:
--list-groups                             List all groups from all application IDs
--list-group-users="[GROUP-URI]"          List all users registered to a group
                                          If the URI is omited, then a list of groups will be displayed
--list-group-permissions="[GROUP-URI]"    List all permissions of a group of users
                                          If the URI is omited, then a list of groups will be displayed
--list-user-permissions="[USER-URI]"      List all permissions of a user
                                          The URI is required
--list-user-groups="[USER-URI]"           List all groups where the user is a member of
                                          The URI is required

Groups Management Options:
--create-group="[GROUP-URI]"              Create a new group
                                          If the URI is omited, then the user will have to provide it at the commandline
--app-id="[ID]"                (optional) Specifies an application ID where to create the group
                                          If the ID is omited, then the user will have to provide it at the commandline
--delete-group="[GROUP-URI]"              Delete a group (and remove all users registrations to it)
                                          If the URI is omited, then a list of groups will be displayed

Users Management Options:
--register-user="[USER-URI]"              Register a user to a group
                                          If the URI is omited, then the user will have to provide it at the commandline
--unregister-user="[USER-URI]"            Register a user to a group
                                          If the URI is omited, then a list of groups will be displayed
--register-user-group="[GROUP-URI]"       Specify the group URI where to register/unregister a user

Accesses Management Options:
--create-access                           Create a new permissions access record related to a dataset and a group
--delete-access="[URI]"                   Delete a permissions access record related to a dataset and a group
                                          If the URI is omited, then a list of available access records will be displayed
--delete-dataset-accesses                 Delete all the access permissions of a dataset (all groups)
                                          If the --access-dataset parameter is omited, then a list of datasets will be displayed
--access-dataset="[URI]"       (optional) Specify the dataset URI involved in an access management operation
--access-group="[URI]"         (optional) Specify the group URI involved in an access management operation
--access-perm-create="[BOOL]"  (optional) Specify if the Create permissions is granted'
--access-perm-read="[BOOL]"    (optional) Specify if the Read permissions is granted'
--access-perm-update="[BOOL]"  (optional) Specify if the Update permissions is granted'
--access-perm-delete="[BOOL]"  (optional) Specify if the Delete permissions is granted'
--access-all-ws                (optional) Specify that we want to use all the registered WS when creating the access record
--access-ws="[WS-URIs]"        (optional) A list of web service endpoints URIs, seperated by semi-colon ';'
                                          used to define the new access record

General Options:
--osf-web-services="[URL]"                (optional) Target OSF Web Services endpoints URL.
                                                     It uses the one defined in pmt.ini if this parameter is not defined
                                                     Example: 'http://localhost/ws/'
--osf-web-services-query-extension="[CLASS]"   (optional) Query Extension Class (with its full namespace) to use for querying the OSF Web Services
                                                          Example: 'StructuredDynamics\osf\framework\MYQuerierExtension'
```