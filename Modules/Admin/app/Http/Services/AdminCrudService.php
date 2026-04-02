<?php

namespace Modules\Admin\Http\Services;

use Illuminate\Http\JsonResponse;
use Silber\Bouncer\BouncerFacade;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\Admin;
use Modules\Admin\Enums\AdminStatus;
use Modules\Admin\Events\RoleChangedEvent;
use Modules\Admin\Models\Admin as CrudModel;
use Modules\Base\Http\Services\BaseCrudService;
use Modules\Admin\Enums\permissions\AdminPermissions;
use Yajra\DataTables\Facades\DataTables;

class AdminCrudService extends BaseCrudService
{
    protected $modelClass = CrudModel::class;

    protected $unnecessaryFieldsForCrud = [
        'avatar',
        'avatar_remove',
        'current_password',
        'role'
    ];

    public function createModel(array $data) : CrudModel
    {
        $modelData = $this->prepareModelData($data);

        $model = DB::transaction(function () use($data, $modelData){
            $model = CrudModel::create($modelData);

            BouncerFacade::assign($data['role'])->to($model);

            $this->uploadImageForModel($model, $data, Admin::MEDIA_COLLECTION, 'avatar');

            return $model;
        });

        return $model;
    }

    public function updateModel(CrudModel $model, array $data) : CrudModel
    {
        if(is_null($data['password'])){
            unset($data['password']);
        }

        $modelData = $this->prepareModelData($data);

        DB::transaction(function () use($data, $model, $modelData){
            $model->update($modelData);

            if(isset($data['avatar_remove']) && $data['avatar_remove'] == true){
                $this->removeAvatar($model, Admin::MEDIA_COLLECTION);
            }

            $this->uploadImageForModel($model, $data, Admin::MEDIA_COLLECTION, 'avatar');

            if($data['status'] != AdminStatus::ACTIVE){
                $this->removeFcmToken($model);
            }

            if (isset($data['role'])) {
                $this->changeRole($model, $data['role']);
            }
        });

        return $model;
    }

    private function removeFcmToken(CrudModel $model) : void
    {
        foreach($model->fcmTokens ?? [] as $token){
            $token->delete();
        }
    }

    public function removeAvatar(CrudModel $model, string $collection) : void
    {
        $media = $model->getFirstMedia($collection);

        if($media) {
            $media->delete();
        }
    }

    private function changeRole(CrudModel $model, string $roleId) : void
    {
        $oldRole = $model->roles->first();

        if(is_null($oldRole) || $oldRole->id == $roleId) return;

        // Sync roles with pivot values
        $model->roles()->syncWithPivotValues([$roleId], ['entity_type' => get_class($model)]);

        if(config('audit.enabled')) event(new RoleChangedEvent($model, $oldRole->name, app('admin')->id));
    }

    protected function applyModelScopes($query)
    {
        return $query->exceptRoot()->exceptCurrentAdmin();
    }

    public function getDataTable(array $data) : JsonResponse
    {
        $model = CrudModel::query();

        if($this->hasWithDisabled()) {
            $model = $model->withDisabled();
        }

        $model = $model->with('roles.translations')->exceptRoot()->exceptCurrentAdmin();

        if($this->shouldShowTrash($data, AdminPermissions::VIEW_TRASH)) {
            $model = $model->onlyTrashed();
        }

        $canLoginToAnotherAccount   = app('owner') || app('admin')->can(AdminPermissions::LOGIN_TO_ANOTHER_ACCOUNT);
        $additionalActions          = [];

        return DataTables::of($model)
            ->filter(function ($query) use ($data) {
                if(isset($data['search']['value']) && !empty($data['search']['value'])){
                    $query->simpleSearch($data['search']['value']);
                }
                if(isset($data['advanced_search']) && !empty($data['advanced_search'])){
                    $query->advancedSearch($data['advanced_search']);
                }
            })
            ->addColumn('actions', function ($model) use($canLoginToAnotherAccount, $additionalActions){
                $excludeActions = [VIEW_ACTION];

                if($canLoginToAnotherAccount) {
                    $additionalActions[] = app('customDataTable')->addAction('login_to_another_account', 'fas fa-sign-in-alt', 99, $model->id, color: '#f1416c', route: route('admin.profile.loginToAnotherAccount', ['model' => $model->id]));
                }

                return
                    app('customDataTable')
                    ->routePrefix('admin.admins')
                    ->of($model, AdminPermissions::PERMISSION_NAMESPACE)
                    ->excludeActions($excludeActions)
                    ->getDatatableActions(additionalActions: $additionalActions, withMainCrudActions: true);
            })
            ->toJson();
    }
}
