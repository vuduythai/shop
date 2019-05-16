<?php

namespace Modules\Backend\Controllers\Group;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Modules\Backend\Core\AclResource;
use Modules\Backend\Core\BackendGroupController;
use Modules\Backend\Core\Functions;
use Modules\Backend\Core\System;
use Modules\Backend\Models\BackendUser;
use Modules\Backend\Models\Role;

class BackendUserController extends BackendGroupController
{
    /**
     * Change permission by role id
     */
    public function onChangePermission(Request $request)
    {
        $roleId = $request->roleId;
        $data['permissionByRole'] = Role::getPermissionByRoleId($roleId);
        $data['permission'] = AclResource::aclSource();
        $data['allow'] = System::ALLOW;
        return view('Backend.View::group.backend_user.permission', $data);
    }

    /**
     * Send reset password email
     */
    public function onSendResetPasswordEmail(Request $request)
    {
        $email = $request->email;
        $id = $request->id;
        $strRandom = Functions::generateRandomString(6);
        $rs = BackendUser::sendEmailResetPassword($email, $id, $strRandom);
        if ($rs['rs'] == System::SUCCESS) {
            Session::flash('msg', trans('Backend.Lang::lang.backend_user.send_email_success'));
        }
        return response()->json($rs);
    }
}