## Add one controller in group
- add more key in function factoryModelBackend() (file /modules/Backend/Core/AppModel.php)
- add one file controller in /modules/Backend/Controllers/Group
- add one model in /modules/Backend/Models. There are at least 4 functions in this file model:
getList(), formCreate(), validateDataThenSave(), saveRecord()
- add one route resource in /modules/routes.php
- add resource in aclSource() (file /modules/Backend/Core/AclResource.php)
- add translate in //modules/Backend/lang/en/lang.php
- add menu item in /modules/Backend/views/setting/index.blade.php