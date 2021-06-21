<?php

namespace App\Http\Controllers\Lms\Resource;

use App\Classes\LMS\Resource\AssetsClass;
use App\Http\Controllers\Controller;
use App\Models\Lms\Tenants;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AssetsController extends Controller
{
    protected AssetsClass $assetsClass;
    protected Tenants $tenants;


    public function __construct()
    {
        $this->assetsClass = new AssetsClass;
        $this->tenants = new Tenants;
    }

    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function create(Request $request)
    {
        //

        $this->validate($request, [
            "asset_url" => "required",
            "asset_name" => "required|string"
        ]);
        $user = $request->user();
        //get file type from file url send
        $exploded_url = explode(".", $request->asset_url);
        $type = $exploded_url[count($exploded_url) - 1];
        $tenant = $this->tenants->newQuery()->where('lms_user_id',$user->id)->first();
        if (!$tenant) throw new ResourceNotFoundException('tenant could not be found');
        return $this->assetsClass->createAsset($tenant->id, $request->asset_name, $request->asset_url, $type);
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, string $assetId)
    {
        $this->validate($request, [
            "asset_url" => "required",
            "asset_name" => "required|string"
        ]);
        //get file type from file url send
        $exploded_url = explode(".", $request->asset_url);
        $type = $exploded_url[count($exploded_url) - 1];
        $request->request->add(['type' => $type]);
        return $this->assetsClass->updateAsset($assetId, $request->all());
    }


    public function all(Request $request)
    {
        $user = $request->user();
        $tenant = $this->tenants->newQuery()->where('lms_user_id',$user->id)->first();
        if (!$tenant) throw new ResourceNotFoundException('tenant could not be found');
        return $this->assetsClass->fetchAssets($tenant->id);
    }

    public function single(string $assetId)
    {
        return $this->assetsClass->showAsset($assetId);
    }



    /**
     * @throws ValidationException
     */
    public function customUpload(Request $request)
    {
        $this->validate($request, [
            "asset_file" => "required|file",
        ]);
        return $this->assetsClass->storeToS3($request);

    }
}
