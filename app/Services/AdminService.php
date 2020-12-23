<?php

namespace App\Services;

use App\Models\Action;
use App\Models\ActionAdminRole;
use App\Models\Menu;

class AdminService
{

//    public $actionAdminRoles;
    public $allMenu;
    public $allAction;

    /**
     * 菜单
     *
     */
    public function menu($aAadminRoleId, $isFull = false)
    {
//        $this->actionAdminRoles = ActionAdminRole::query()->get(['action_id', 'admin_role_id']);
        $this->allMenu = Menu::getAll();
        $this->allAction = Action::getAll();
        # 取得最上層menu
        $oMemus = Menu::query()->whereNull('parent_id')->get();

        # 設定menu
        $aMenu = $this->setMenu($oMemus, $aAadminRoleId, $isFull);

        return $aMenu;
    }

    /**
     * 设定菜单
     *
     */
    private function setMenu($oMenus, $aAdminRoleId, $isFull = false)
    {
        $aMenu = [];

        foreach ($oMenus as $oMenu) {

            $bValidShow = false;

            # 輸出格式
            $aMenuFormat = [
                'id'          => $oMenu->id,
                'name'        => $oMenu->name,
                'code'        => $oMenu->code,
                'description' => $oMenu->description,
                'is_show'     => $oMenu->is_show,
                'actions'     => [],
                'children'    => [],
            ];


            # 操作权限
            foreach ($this->allAction->where('menu_id', $oMenu->id) as $oAction) {

//                $bValid = $this->actionAdminRoles->where('action_id', $oAction)->whereIn('admin_role_id', $aAdminRoleId)->isEmpty();
                $bValid = $oAction->adminRoles()->whereIn('admin_role_id', $aAdminRoleId)->exists();
                if (!$bValidShow && $bValid) {
                    $bValidShow = $bValid;
                }

                $aMenuFormat["actions"][] = [
                    'id'            => $oAction->id,
                    'name'          => $oAction->name,
                    'url'           => $oAction->url,
                    'drop_list_url' => $oAction->drop_list_url,
                    'method'        => $oAction->method,
                    'action'        => $oAction->action,
                    'valid'         => $bValid,
                ];
            }

            # 递回设定子菜单

            $oSubMenu = $this->allMenu->where('parent_id', $oMenu->id)->sortBy('sort');
            if (!$oSubMenu->isEmpty()) {
                $aMenuFormat["children"] = $this->setMenu($oSubMenu, $aAdminRoleId, $isFull);
            }

            # 判断是否显示菜单
            if ($aMenuFormat["children"] || ($aMenuFormat["actions"] && $bValidShow)
                || $isFull // 开启显示并且需要全量显示的时候，才会全部显示
            ) {
                $aMenu[] = $aMenuFormat;
            }
        }

        return $aMenu;
    }


}
