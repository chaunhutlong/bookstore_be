<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Publisher;
use Illuminate\Http\Request;
use App\Http\Resources\PublisherResource;
use App\Http\Resources\PublisherCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PublisherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *      path="/publishers",
     *      operationId="getpublishersList",
     *      tags={"publishers"},
     *      summary="Get list of publishers",
     *      description="Returns list of publishers",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/PublisherResource")
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *    )
     */
    public function index()
    {
        $per_page = request()->input('per_page', 10);

        $publishers = Publisher::paginate($per_page);

        return response()->json(new PublisherCollection($publishers), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /**
     * @OA\Post(
     *      path="/publishers",
     *      operationId="storePublisher",
     *      tags={"publishers"},
     *      summary="Store new publisher",
     *      description="Returns publisher data",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StorePublisherRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/PublisherResource")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *      ),
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required|string|max:255',
                    'address' => 'required|string',
                    'phone' => 'required|numeric|digits_between:10,12',
                    'description' => 'required|string',
                ]
            );

            $data = $validator->validated();

            $publiser = Publisher::create($data);
            DB::commit();

            return response()->json(new PublisherResource($publiser), 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *      path="/publishers/{id}",
     *      operationId="getPublisherById",
     *      tags={"publishers"},
     *      summary="Get publisher information",
     *      description="Returns publisher data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Publisher id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/PublisherResource")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function show(Publisher $publisher)
    {
        try {
            return response()->json(new PublisherResource($publisher), 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Put(
     *      path="/publishers/{id}",
     *      operationId="updatePublisher",
     *      tags={"publishers"},
     *      summary="Update existing publisher",
     *      description="Returns updated publisher data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Publisher id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UpdatePublisherRequest")
     *      ),
     *      @OA\Response(
     *          response=202,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/PublisherResource")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function update(Request $request, Publisher $publisher)
    {
        DB::beginTransaction();
        try {

            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'string|max:255',
                    'address' => 'string',
                    'phone' => 'numeric|digits_between:10,12',
                    'description' => 'string',
                ]
            );

            $data = $validator->validated();

            $publisher->update($data);
            DB::commit();

            return response()->json(new PublisherResource($publisher), 202);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => $e->getMessage()], 500);;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Delete(
     *      path="/publishers/{id}",
     *      operationId="deletePublisher",
     *      tags={"publishers"},
     *      summary="Delete existing publisher",
     *      description="Deletes a record and returns no content",
     *      @OA\Parameter(
     *          name="id",
     *          description="Publisher id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function destroy(Publisher $publisher)
    {
        DB::beginTransaction();
        try {
            $publisher->delete();
            DB::commit();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => $e->getMessage()], 500);
        }
    }
}
