<?php

/**
 * @OA\Schema(
 *      title="Update Genre request",
 *      description="Update Genre request body data",
 *      type="object"
 * )
 */

class UpdateGenreRequest
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
    public $description;
}
