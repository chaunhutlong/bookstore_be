<?php

/**
 * @OA\Schema(
 *      title="Store Genre request",
 *      description="Store Genre request body data",
 *      type="object",
 *      required={"name"}
 * )
 */

class StoreGenreRequest
{
    /**
     * @OA\Property(
     *      title="name",
     *      description="Name of the new genre",
     *      example="A nice genre"
     * )
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property(
     *      title="description",
     *     description="Description of the new genre",
     *    example="A nice description"
     * )
     * @var string
     */
}
