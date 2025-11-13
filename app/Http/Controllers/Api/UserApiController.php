<?php

namespace App\Http\Controllers\Api;

use Froiden\RestAPI\ApiController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserApiController extends ApiController
{
    /**
     * The model to be used by the controller.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Default number of records to return.
     *
     * @var int
     */
    protected $defaultLimit = 10;

    /**
     * Maximum number of records allowed to be returned in a single request.
     *
     * @var int
     */
    protected $maxLimit = 100;

    /**
     * Hook to modify the object before storing.
     * Hash the password before saving.
     *
     * @param User $user
     * @return User
     */
    protected function storing($user)
    {
        if (request()->has('password')) {
            $user->password = Hash::make(request('password'));
        }
        return $user;
    }

    /**
     * Hook to modify the object before updating.
     * Hash the password before updating if provided.
     *
     * @param User $user
     * @return User
     */
    protected function updating($user)
    {
        if (request()->has('password') && !empty(request('password'))) {
            $user->password = Hash::make(request('password'));
        }
        return $user;
    }

    /**
     * Hook before destroying a user.
     * Prevent deleting own account.
     *
     * @param User $user
     * @return User
     */
    protected function destroying($user)
    {
        if ($user->id === auth()->id()) {
            abort(403, 'You cannot delete your own account');
        }
        return $user;
    }
}
