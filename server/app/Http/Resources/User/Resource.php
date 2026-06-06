<?php

namespace App\Http\Resources\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResourceFilterable;
use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Media\Resource as MediaResource;
use App\Http\Resources\UserDevice\Resource as UserDeviceResource;

/**
 * @property User $resource
 */
#[SchemaName('User')]
class Resource extends JsonResource
{
    use ResourceFilterable;

    protected $model = User::class;

    /**
     * @return array{
     *     id: int,
     *     first_name: string|null,
     *     last_name: string|null,
     *     username: string|null,
     *     email: string,
     *     status: string,
     *     mobile_no: string|null,
     *     email_verified_at: int|null,
     *     last_login_at: int|null,
     *     created_at: int|null,
     *     updated_at: int|null,
     *     deleted_at: int|null,
     *     name: string,
     *     display_status: string,
     *     display_mobile_no: string,
     *     avatar: string|null,
     *     profile_image: MediaResource|null,
     *     user_device: UserDeviceResource|null
     * }
     */
    public function toArray(Request $request): array
    {
        if (is_null($this->resource)) {
            return [];
        }

        $data = $this->fields();
        $data['avatar'] = $this->avatar;
        $data['profile_image'] = MediaResource::make($this->whenLoadedMedia(config('media.tags.profile'), true));
        $data['user_device'] = UserDeviceResource::make($this->whenLoaded('userDevice'));
        $data['roles'] = $this->getRoleNames();
        $data['permissions'] = $this->getAllPermissions()->pluck('name');
        
        if ($this->hasRole(config('site.roles.vendor'))) {
            $data['vendor'] = \App\Models\Vendor::where('user_id', $this->id)->first();
        }

        return $data;
    }
}
